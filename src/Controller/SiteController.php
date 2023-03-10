<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Form\SitesType;
use App\Repository\SitesRepository;
use App\Repository\UserRepository;
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
            $this->addFlash('success', 'site modifier !');
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
            $this->addFlash('success', 'site modifier !');
            return $this->redirectToRoute('site_liste');
        }
        
        return $this->render('site/siteUpdate.html.twig', [
            'siteForm' => $siteForm->createView(),
            'site' => $site,
        ]);
    }

    #[Route('/site/delete/{id}', name: 'site_delete')]
    public function delete(EntityManagerInterface $entityManager,UserRepository $userRepository,Sites $site): Response
    {
       
        $trouver =false;
        $users=$userRepository->findAll();
        foreach ($users as $user) {
            if ($user->getSites()->getId()==$site->getId()) {
                $trouver=true;
            }
        }
        if($trouver==false){
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('success', 'site supprimer !');
        }
        else{
            $this->addFlash('danger', 'le site ne peut pas ??tre supprimer !');
        }
       
        return $this->redirectToRoute("site_liste");
    }
}