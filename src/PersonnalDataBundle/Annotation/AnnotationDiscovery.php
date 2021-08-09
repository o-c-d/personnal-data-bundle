<?php

namespace Ocd\PersonnalDataBundle\Annotation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Config\ResourceCheckerConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class AnnotationDiscovery
{

    private EntityManagerInterface $entityManager;

    private Reader $annotationReader;

    protected CacheInterface $cache;

    private array $annotations;

    private string $annotationCacheDuration='1 day';

    /**
     * @var array
     */
    private $personnalDatas = [];

    public function __construct(EntityManagerInterface $entityManager, Reader $annotationReader, CacheInterface $cache, string $annotationCacheDuration='1 day')
    {
        $this->entityManager = $entityManager;
        $this->annotationReader = $annotationReader;
        $this->cache = $cache;
        $this->annotationCacheDuration = $annotationCacheDuration;
    }

    public function getAllEntitiesAnnotations() {
        $managedEntities = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $cacheKey = serialize($managedEntities);
        $annotationReader = $this->annotationReader;
        $annotationCacheDuration = $this->annotationCacheDuration;
        return $this->cache->get('ocd_personnal_data_'.md5($cacheKey), function (ItemInterface $item) use ($managedEntities, $annotationReader, $annotationCacheDuration) {
            $item->expiresAfter(\DateInterval::createFromDateString($annotationCacheDuration));
            $data = [];
            /** @var ClassMetadata $managedEntity */
            foreach ($managedEntities as $managedEntity) {
                //
                $entityClassName = $managedEntity->getName();
                $reflection = $managedEntity->getReflectionClass();
                $personnalDataReceiptAnnotation = $annotationReader->getClassAnnotation($reflection, PersonnalDataReceipt::class);

                if (!isset($data[$entityClassName]) && null !== $personnalDataReceiptAnnotation) {
                    $data[$entityClassName] = [
                        'annotation' => $personnalDataReceiptAnnotation,
                        'fields' => [],
                    ];
                    $reflectionProperties = $managedEntity->getReflectionProperties();
                    /** @var \ReflectionProperty $refProperty */
                    foreach ($reflectionProperties as $refProperty) {
                        //
                        $propertyName = $refProperty->getName();
                        $propertyType = $refProperty->getType() ? $refProperty->getType()->getName() : null;
                        if (null === $propertyType) {
                            $doctrinePropertyAnnotation = $annotationReader->getPropertyAnnotation($refProperty, Column::class);
                            /** @var $doctrinePropertyAnnotation Annotation */
                            if ($doctrinePropertyAnnotation instanceof Column) {
                                $propertyType = $doctrinePropertyAnnotation->type;
                            }
                        }
                        $propertyAnnotation = $annotationReader->getPropertyAnnotation($refProperty, PersonnalData::class);
                        if (null !== $propertyAnnotation) {
                            $data[$entityClassName]['fields'][$propertyName] = [
                                "type" => $propertyType,
                                "annotation" => $propertyAnnotation,
                            ];
                        }
                    }
                }
            }
            return $data;
        });
    }
}