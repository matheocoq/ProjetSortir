<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserUpdateType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    #[Route('/user/update', name: 'user_update')]
    public function update(Request $request,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager,UserRepository $userRepository,SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserUpdateType::class,$user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted()){
            $image = $userForm->get('image')->getData();
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $user->getPseudo().'-'.uniqid().'.'.$image->guessExtension();

                if ($user->getImage()!= null) {
                    unlink($this->getParameter('image_directory').'/'.$user->getImage());
                }
                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setImage($newFilename);
            }
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
            'userForm' => $userForm->createView(),
            'user'=> $user
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
