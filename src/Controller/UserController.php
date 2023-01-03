<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/update', name: 'user_update')]
    public function update(Request $request,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserUpdateType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()){
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userForm->get('password')->getData()
                )
            );
            $entityManager->flush();
        }

        return $this->render('user/index.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }
}
