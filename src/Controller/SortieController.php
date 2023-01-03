<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_liste')]
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
    public function create(): Response
    {
        return $this->render('sortie/sortieCreate.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}
