<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use mysql_xdevapi\Exception;
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
            if($userForm->get('password')->getData() != null) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userForm->get('password')->getData()
                    )
                );
            }
            $entityManager->flush();
        }

        return $this->render('user/index.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/user/create', name: 'user_create')]
    public function create(Request $request,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager,UserRepository $userRepository): Response {

        $user = new User();
        $userForm = $this->createForm(UserUpdateType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()){
            if($userForm->get('password')->getData() != null) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userForm->get('password')->getData()
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_create');
        }

        return $this->render('user/userCreate.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/user/detail/{id}', name: 'user_detail')]
    public function detail($id, Request $request, UserRepository $userRepository): Response
    {

        $user = $userRepository->find($id);

        if (!$user) {
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('user/userDetail.html.twig', [
            'user' => $user
        ]);
    }
}
