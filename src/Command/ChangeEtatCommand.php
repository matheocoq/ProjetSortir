<?php

namespace App\Command;

use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ChangeEtatCommand',
    description: 'Add a short description for your command',
)]
class ChangeEtatCommand extends Command
{

    protected static $defaultName = 'app:update-event-states';

    /** @var EntityManagerInterface */
    private $entityManager;

    private $logger;

    /** @var EventStateHelper */
    private $stateHelper;
    private $sortieRepository;
    private $etatsRepository;

    public function __construct(EntityManagerInterface $entityManager,string $name = null,SortiesRepository $sortieRepository,EtatsRepository $etatsRepository)
    {
        $this->entityManager = $entityManager;
        parent::__construct($name);
        $this->sortieRepository=$sortieRepository;
        $this->etatsRepository=$etatsRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Met à jour les états des sorties')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('event states update started');

        $io = new SymfonyStyle($input, $output);

        $sorties =  $this->sortieRepository->findAll();

        
        foreach($sorties as $sortie) {
            $now = new \DateTime();

            if ($sortie->getEtat()->getId()==2 && $sortie->getDateDebut()>$now && $sortie->getDateCloture()<$now){
                $etat=$this->etatsRepository->find(3);
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
                continue;
            }

            if (($sortie->getEtat()->getId()==2 || $sortie->getEtat()->getId()==3) && $sortie->getDateDebut()<$now && ){
                $etat=$this->etatsRepository->find(4);
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
                continue;
            }

            if (($sortie->getEtat()->getId()==2 || $sortie->getEtat()->getId()==3 || $sortie->getEtat()->getId()==4) && ){
                $etat=$this->etatsRepository->find(5);
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
                $this->entityManager->flush();
                continue;
            }

        }

        $io->success("OK c'est fait !");
        

        return 0;
    }
}
