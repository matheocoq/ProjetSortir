<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\SortiesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SitesRepository;
use App\Repository\VilleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        dump($this->getUser());

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //dd($request->request->get('typeRegister'));
            // UNIQUEMENT POUR LES TESTS
            $toGo = 'sortie_liste';
            if ($this->getUser()){
                $sortie->setOrganisateur($this->getUser());
            } else {
                // UNIQUEMENT POUR LES TESTS
                $sortie->setOrganisateur($userRepository->find(1));
                $toGo = 'sortie_create';
            }

            if ($request->request->get('typeRegister') === 'Publier la sortie') {
                $sortie->setEtat($etatsRepository->find(2));
            } else {
                $sortie->setEtat($etatsRepository->find(1));
            }

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Sortie added !');
            return $this->redirectToRoute($toGo);
        }

        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/sortie/inscription/{id}', name: 'sortie_inscription')]
    public function inscription(EntityManagerInterface $entityManager,Sorties $sortie): Response
    {  
        $user = $this->getUser();
        $date = new DateTime();
        if($sortie){
            $entityManager->persist($sortie);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute("sortie_liste");
    }
}
