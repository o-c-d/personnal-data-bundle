<?php

namespace Ocd\PersonnalDataBundle\Command;

use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class PersonnalDataListCommand extends Command
{
    protected static $defaultName = 'ocd:personnal-data:list';

    /** @var AnnotationManager $manager */
    protected $manager;
    protected $entityManager;

    public function __construct(AnnotationManager $manager, EntityManager $entityManager)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->manager = $manager;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
    $this
        ->setName(self::$defaultName)
        // the short description shown while running "php bin/console list"
        ->setDescription('List all Anonymizable fields from Entities.')

        // the full command description shown when running the command with the "--help" option
        ->setHelp('This command allows you to list all Anonymizable fields from Entities.')
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ...
        $io = new SymfonyStyle($input, $output);

        $io->title('List Anonymizable Entities');

        $data = $this->manager->getAllEntitiesAnnotations();
        foreach($data as $entityName => $entityData) {
            $io->section($entityName);
            if(!empty($entityData['personnalDataReceiptAnnotation']))
            {
                $io->text("personnalDataReceiptAnnotation: ".$entityData['personnalDataReceiptAnnotation']->getName());
            }
            if(!empty($entityData['fields']))
            {
                $io->text("Fields:");
                foreach($entityData['fields'] as $fieldName => $fieldData)
                {
                    $annotation = $fieldData['annotation'];
                    $txt = "--".$fieldName." (".$fieldData['type']."):";
                    if(null != $annotation) $txt .= " PersonnalData:".$annotation->getName();
                    $io->text($txt);
                    // foreach($fieldAnnotations as $annotation)
                    // {
                    //     if(null != $annotation) $io->text("----".get_class($annotation));
                    // }
                }
            }
        }

        // $list = $this->manager->getAnonymizables();
        // foreach($list as $element) {
        //     $io->text($element['class']." : ".$element['annotation']->getName()." - conservationDuration =  ".$element['annotation']->getConservationDuration());
        // }

        // $io->title('List all PersonnalData');
        // $list = $this->manager->getPersonnalData();
        // foreach($list as $element) {
        //     $io->text($element['class']." : ".$element['annotation']->getName());
        // }


        // $io->title('List all Entities');
        // $entities = array();
        // $meta = $this->entityManager->getMetadataFactory()->getAllMetadata();
        // /** @var ClassMetadata $classMeta*/
        // foreach ($meta as $classMeta) {
        //     $entities[] = $classMeta->getName();
        //     $io->text($classMeta->getName());
        //     // $io->text($classMeta->getDescription());
        // }
        
        return Command::SUCCESS;
    }
}