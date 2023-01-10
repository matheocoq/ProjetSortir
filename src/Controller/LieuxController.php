<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuxType;
use App\Repository\LieuxRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    #[Route('/lieux/liste', name: 'lieux_liste')]
    public function liste(LieuxRepository $lieuxRepository): Response
    {
        $lieux= $lieuxRepository->findAll();
        return $this->render('lieux/lieuxListe.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/lieux/create', name: 'lieux_create')]
    public function create(EntityManagerInterface $entityManager,Request $request,LieuxRepository $lieuxRepository): Response
    {
        $lieu = new Lieux();
        $lieuxForm = $this->createForm(LieuxType::class,$lieu);
        $lieuxForm->handleRequest($request);

        if($lieuxForm->isSubmitted() && $lieuxForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('succes', 'lieux crée !');
            return $this->redirectToRoute('lieux_liste');
        }

        return $this->render('lieux/lieuxCreate.html.twig', [
            'lieuxForm'=>$lieuxForm->createView(),
            'lieu' => $lieu,
        ]);
    }

    #[Route('/lieux/update/{id}', name: 'lieux_update')]
    public function update(EntityManagerInterface $entityManager,Request $request,LieuxRepository $lieuxRepository,Lieux $lieu): Response
    {
       
        
        $lieuxForm = $this->createForm(LieuxType::class,$lieu);
        $lieuxForm->handleRequest($request);

        if($lieuxForm->isSubmitted() && $lieuxForm->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('succes', 'lieux modifier !');
            return $this->redirectToRoute('lieux_liste');
        }
        
        return $this->render('lieux/lieuxUpdate.html.twig', [
            'lieuxForm'=>$lieuxForm->createView(),
            'lieu' => $lieu,
        ]);
    }

    #[Route('/lieux/delete/{id}', name: 'lieux_delete')]
    public function delete(EntityManagerInterface $entityManager,SortiesRepository $sortiesRepository,Lieux $lieux): Response
    {
        
        $trouver =false;
        $sortis=$sortiesRepository->findAll();
        foreach ($sortis as $sorti) {
            if ($sorti->getLieux()->getId()==$lieux->getId()) {
                $trouver=true;
            }
        }
        if($trouver==false){
            $entityManager->remove($lieux);
            $entityManager->flush();
            $this->addFlash('succes', 'lieu supprimer !');
        }
        else{
            $this->addFlash('error', 'le lieu ne peut pas être supprimer !');
        }
       
        return $this->redirectToRoute("lieux_liste");
    }
}
