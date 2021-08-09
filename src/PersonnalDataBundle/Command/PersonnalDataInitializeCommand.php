<?php

namespace Ocd\PersonnalDataBundle\Command;

use Doctrine\ORM\EntityManager;
use Ocd\PersonnalDataBundle\Annotation\AnnotationManager;
use Ocd\PersonnalDataBundle\Service\DataProtectionOfficer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class PersonnalDataInitializeCommand extends Command
{
    protected static $defaultName = 'ocd:personnal-data:initialize';

    /** @var DataProtectionOfficer $dpo */
    protected $dpo;
    protected $entityManager;
    protected $annotationManager;

    public function __construct(AnnotationManager $annotationManager, EntityManager $entityManager, DataProtectionOfficer $dpo)
    {
        $this->dpo = $dpo;
        $this->entityManager = $entityManager;
        $this->annotationManager = $annotationManager;

        parent::__construct();
    }

    protected function configure()
    {
    $this
        ->setName(self::$defaultName)
        // the short description shown while running "php bin/console list"
        ->setDescription('Initialize PersonnalDataRegister.')
        ->addOption(
            'withConsent',
            null,
            InputOption::VALUE_NONE,
            'Create a generic consent for each data ?',
        )
        // the full command description shown when running the command with the "--help" option
        ->setHelp('This command allows you to create all PersonnalDataRegister from Entities.')
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Initialize PersonnalDataRegister');

        $withConsent = $input->getOption('withConsent');
        $this->dpo->declareAllPersonnalDataInDatabase($withConsent);

        $annotations = $this->annotationManager->getAllEntitiesAnnotations();
        $progressBar = new ProgressBar($output, count($annotations));
        $progressBar->start();
        foreach ($annotations as $entityName => $entityData) {
            /** @var PersonnalDataReceipt $personnalDataReceiptAnnotation*/
            // $io->section($entityName);
            $personnalDataReceiptAnnotation = $entityData['annotation'];
            if ($personnalDataReceiptAnnotation->isPersonnalDataProvider()) {
                $allEntityData = $this->entityManager->getRepository($entityName)->findAll();
                // $io->text('Declare '.$entityName.' providers');
                $progressBar2 = new ProgressBar($output, count($allEntityData));
                $progressBar2->start();
                foreach ($allEntityData as $index => $entity) {
                    // Todo: check if this personal data is already present in personnalDataRegister
                    $this->dpo->declareAllPersonnalDataFromEntity($entity);
                    $progressBar2->advance();
                }
                $progressBar2->finish();
            }
            $progressBar->advance();
        }
        $progressBar->finish();

        return Command::SUCCESS;
    }

}