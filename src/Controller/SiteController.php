<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Form\SitesType;
use App\Repository\SitesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends AbstractController
{
    #[Route('/site/liste', name: 'site_liste')]
    public function liste(SitesRepository $sitesRepository): Response
    {
        $sites= $sitesRepository->findAll();
        return $this->render('site/siteListe.html.twig', [
            'sites' => $sites,
        ]);
    }

    #[Route('/site/create', name: 'site_create')]
    public function create(EntityManagerInterface $entityManager,Request $request,SitesRepository $sitesRepository): Response
    {
        
        $site = new Sites();
        $siteForm = $this->createForm(SitesType::class,$site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash('succes', 'site modifier !');
            return $this->redirectToRoute('site_liste');
        }
        
        return $this->render('site/siteCreate.html.twig', [
            'siteForm' => $siteForm->createView(),
            'site' => $site,
        ]);
    }

    #[Route('/site/update/{id}', name: 'site_update')]
    public function update(EntityManagerInterface $entityManager,Request $request,SitesRepository $sitesRepository,Sites $site): Response
    {
       
      
        $siteForm = $this->createForm(SitesType::class,$site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash('succes', 'site modifier !');
            return $this->redirectToRoute('site_liste');
        }
        
        return $this->render('site/siteUpdate.html.twig', [
            'siteForm' => $siteForm->createView(),
            'site' => $site,
        ]);
    }

    #[Route('/site/delete/{id}', name: 'site_delete')]
    public function delete(SitesRepository $sitesRepository,Lieux $lieux): Response
    {
       
        return $this->render('lieux/lieuxListe.html.twig', [
            'site' => $site,
        ]);
    }
}