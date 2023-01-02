<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/attente', name: 'logIn')]
    public function logIn(Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()){

        }

        return $this->render('user/index.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }
}
