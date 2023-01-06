<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ImportUsersType;
use App\Form\UserUpdateType;
use App\Repository\SitesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Length;

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
    #[Route('/user/upload', name: 'user_upload')]
    public function importAction(Request $request,EntityManagerInterface $entityManager,SitesRepository $sitesRepository,UserPasswordHasherInterface $userPasswordHasher,UserRepository $userRepository)
    {
        $form = $this->createForm(ImportUsersType::class);
        $form->handleRequest($request);

        $arrayOfErrrors = [];
        $i =0;
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            // Move the file to the directory where CSV files are stored
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            $file->move(
                'public/upload',
                $fileName
            );

            // Read the CSV file and do something with the data
            $csv = array_map(function($v){return str_getcsv($v, ";");}, file('public/upload'.'/'.$fileName));
            dump($csv);
            foreach ($csv as &$ligne) {
                if($i == 0) {
                    $i++;
                    continue;
                }
                dump($ligne);
                $separer = $ligne;
                if(count($separer) != 7){
                    $i++;
                    $arrayOfErrrors[] = "Erreur lors de la tentative de création de l'utilisateur à la ligne " . $i . " . Veuillez vérifier les informations de l'utilisateur.";
                    continue;
                }
                $user = $userRepository->findOneBy(array('pseudo' => $separer[4]));
                if($user == null) {
                    dump('Aucun user trouver avec ce pseudo, on en créer un nouveau');
                    $user = new User();
                }
                dump($user);
                $user->setEmail($separer[0]);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $separer[1]
                    )
                );
                $user->setNom($separer[2]);
                $user->setPrenom($separer[3]);
                $user->setPseudo($separer[4]);
                $user->setTelephone($separer[5]);
                dump($separer[6]);
                $siteObj = $sitesRepository->findByName($separer[6]);

                $user->setSites($siteObj);

                $entityManager->persist($user);
                $entityManager->flush();
                $i++;
            }
        }
        dump($arrayOfErrrors);
        foreach ($arrayOfErrrors as &$errror) {
            $this->addFlash("notice", $errror);
        }
        return $this->render('user/import.html.twig', [
            'Importform' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
