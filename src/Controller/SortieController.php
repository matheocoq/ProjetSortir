<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\SortiesRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SitesRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/sortie/liste', name: 'sortie_liste')]
    public function liste(SitesRepository $sitesRepository,SortiesRepository $sortiesRepository,Request $request): Response
    {  

        $sorties = $sortiesRepository->findAll();
        $sites= $sitesRepository->findAll();
        $siteRechercher="";
        $contient=null;
        $dateDebut=null;
        $dateFin=null;
        $orga=false;
        $inscrit=false;
        $nonInscrit=false;
        $passer=false;
        $dateValid=true;
        if ($request->query->get("recherche_site")) {
            if ($request->query->get("date_fin")!="" && $request->query->get("date_debut")!="" && $request->query->get("date_fin") < $request->query->get("date_debut")) {
                $dateValid=false;
            }
            else {
                $sorties=$sortiesRepository->findByRecherche($request->query,$this->getUser());
            }
          
            if ($request->query->get("nom_site")!="") {
                $siteRechercher=$request->query->get("nom_site");
            }
            if ($request->query->get("nom_sortie_contient")!="") {
                $contient=$request->query->get("nom_sortie_contient");
            }
            if ($request->query->get("date_debut")!="") {
                $dateDebut=$request->query->get("date_debut");
            }
            if ($request->query->get("date_fin")!="") {
                $dateFin=$request->query->get("date_fin");
            }
            if ($request->query->get("sortie_orga")!=null) {
                $orga=true;
            }
            if ($request->query->get("sortie_insc")!=null){
                $inscrit=true;
            }
            
            if ($request->query->get("sortie_n_insc")!=null) {
                $nonInscrit=true;
            }
            if ($request->query->get("sortie_passee")!=null) {
                $passer=true;
            }
        }
        return $this->render('sortie/sortieListe.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
            'siteRechercher' =>$siteRechercher,
            'contient' =>$contient,
            'dateDebut' =>$dateDebut,
            'dateFin' =>$dateFin,
            'orga' =>$orga,
            'inscrit' =>$inscrit,
            'nonInscrit' =>$nonInscrit,
            'passer' =>$passer,
            'dateValid'=>$dateValid
        ]);
    }

    #[Route('/sortie/create', name: 'sortie_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, EtatsRepository $etatsRepository): Response
    {

        $sortie = new Sorties();
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            if ($request->request->get('typeRegister') === 'Publier la sortie') {
                $sortie->setEtat($etatsRepository->find(2));
            } else {
                $sortie->setEtat($etatsRepository->find(1));
            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Sortie added !');
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/sortie/update/{id}', name: 'sortie_update')]
    public function update($id, Request $request, EntityManagerInterface $entityManager, SortiesRepository $sortiesRepository, EtatsRepository $etatsRepository): Response
    {

        $sortie = $sortiesRepository->find($id);

        if (!$sortie || $sortie->getDateDebut() < new \DateTime() || $sortie->getDateCloture() < new \DateTime() || $sortie->getEtat()->getId() !== 1) {
            return $this->redirectToRoute('sortie_liste');
        }
        $sortieForm = $this->createForm(SortiesType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $messageSucces = '';
            if ($request->request->get('typeRegister') === 'Supprimer la sortie') {
                $entityManager->remove($sortie);
                $messageSucces = 'Sortie deleted !';
            }
            else if ($request->request->get('typeRegister') === 'Publier la sortie') {
                $sortie->setEtat($etatsRepository->find(2));
                $entityManager->persist($sortie);
                $messageSucces = 'Sortie updated !';
            }
            else {
                $sortie->setEtat($etatsRepository->find(1));
                $entityManager->persist($sortie);
                $messageSucces = 'Sortie updated !';
            }


            $entityManager->flush();

            $this->addFlash('succes', $messageSucces);
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/sortieUpdate.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }
    #[Route('/sortie/detail/{id}', name: 'sortie_detail')]
    public function detail($id, Request $request, SortiesRepository $sortiesRepository, UserRepository $userRepository): Response {
        $sortie = $sortiesRepository->find($id);
        if (!$sortie) {
            return $this->redirectToRoute('sortie_liste');
        }

        //dump($sortie->getInscriptions());

        $inscriptions = $sortie->getInscriptions();

        foreach ($inscriptions as $inscription) {
            $idParticipant = $inscription->getParticipant()->getId();
            $participant = $userRepository->find($idParticipant);
            dump($participant);
        }

        $infos = [
            'Nom de la sortie' => $sortie->getNom(),
            'Lieu' => $sortie->getLieux()->getNom(),
            'Date et heure de la sortie' => $sortie->getDateDebut()->format('Y-m-d H:i:s'),
            'Rue' => $sortie->getLieux()->getRue(),
            'Date limite d\'inscription' => $sortie->getDateCloture()->format('Y-m-d H:i:s'),
            'Ville' => $sortie->getLieux()->getVille()->getNom(),
            'Nombre de place' => $sortie->getNbInscriptionMax(),
            'Code postal' => $sortie->getLieux()->getVille()->getCodePostal(),
            'Durée' => $sortie->getDuree(),
            'Latitude' => $sortie->getLieux()->getLatitude(),
            'Description et infos' => $sortie->getDescription(),
            'Longitude' => $sortie->getLieux()->getLongitude(),
        ];

        return $this->render('sortie/sortieDetail.html.twig', [
            'infos' => $infos,
            'sortie'=>$sortie
        ]);
    }

    #[Route('/api/lieux')]
    public function getLieux(LieuxRepository $lieuxRepository, VilleRepository $villeRepository): JsonResponse {
        $lieux = $lieuxRepository->findAll();
        $list = [];
        foreach ($lieux as $unLieu) {
            $ville = $villeRepository->find($unLieu->getVille()->getId());
            $list[] = [
                'idLieux' => $unLieu->getId(),
                'nom' => $unLieu->getNom(),
                'rue' => $unLieu->getRue(),
                'latitude' => $unLieu->getLatitude(),
                'longitude' => $unLieu->getLongitude(),
                'idVille' => $ville->getId(),
                'nomVille' => $ville->getNom(),
                'codePostal' => $ville->getCodePostal()
            ];
        }
        return new JsonResponse($list);
	}
	
    #[Route('/sortie/inscription/{id}', name: 'sortie_inscription')]
    public function inscription(EntityManagerInterface $entityManager,Sorties $sortie,InscriptionsRepository $inscriptionsRepository): Response
    {  
        $user = $this->getUser();
        $date = new DateTime();
        $listeInscription = $inscriptionsRepository->findBySortie($sortie->getId());
        $nbinscrit=count($listeInscription);
        if( $sortie->getDateDebut()>=$date &&$sortie->getDateCloture()>=$date && $sortie->getEtat()->getId() == 2 && $nbinscrit+1<=$sortie->getNbInscriptionMax()){
            $result=$inscriptionsRepository->findOneByUserSortie($user->getId(),$sortie->getId());
            if ($result == null) {
                $inscription = new Inscriptions();
                $inscription->setDateInscription($date);
                $inscription->setParticipant($user);
                $inscription->setSorties($sortie);
                $entityManager->persist($inscription);
                $entityManager->flush();
            }
        }
        
        return $this->redirectToRoute("sortie_liste");
    }

    #[Route('/sortie/desistement/{id}', name: 'sortie_desistement')]
    public function desistement(EntityManagerInterface $entityManager,Sorties $sortie,InscriptionsRepository $inscriptionsRepository): Response
    {  
        $user = $this->getUser();
        $date = new DateTime();
        if( $sortie->getDateDebut()>=$date &&$sortie->getDateCloture()>=$date && $sortie->getEtat()->getId() == 2){
            $result=$inscriptionsRepository->findOneByUserSortie($user->getId(),$sortie->getId());
            if ($result != null) {
                $entityManager->remove($result);
                $entityManager->flush();
            }
        }
        
        return $this->redirectToRoute("sortie_liste");
    }
}
