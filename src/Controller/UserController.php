<?php

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use App\Entity\User;
use App\Form\ImportUsersType;
use App\Form\UserCreateType;
use App\Form\UserUpdateType;
use App\Repository\SitesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Exception;
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
            //récupération du champ image du formulaire
            $image = $userForm->get('image')->getData();
            
            // regarder si une image est présente 
            if ($image) {
                
                $newFilename = $user->getPseudo().'-'.uniqid().'.'.$image->guessExtension();

                //supprimer l'image si l'utilsateur en à déjà une
                if ($user->getImage()!= null) {
                    unlink($this->getParameter('image_directory').'/'.$user->getImage());
                }
                
                try {
                    //deplace l'image dans le fichier 
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                    $user->setImage($newFilename);
                } catch (FileException $e) {
                    throw new Exception("Problème pour déplacer l'image");
                }

                
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
    public function create(Request $request,UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager,UserRepository $userRepository,SluggerInterface $slugger): Response {

        $user = new User();
        $userForm = $this->createForm(UserCreateType::class, $user);
        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){

            //récupération du champ image du formulaire
            $image = $userForm->get('image')->getData();

            // regarder si une image est présente 
            if ($image) {

                $newFilename = $user->getPseudo().'-'.uniqid().'.'.$image->guessExtension();

                //supprimer l'image si l'utilsateur en à déjà une
                if ($user->getImage()!= null) {
                    unlink($this->getParameter('image_directory').'/'.$user->getImage());
                }
                

                try {
                    //deplace l'image dans le fichier 
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                    $user->setImage($newFilename);
                } catch (FileException $e) {
                    throw new Exception("Problème pour déplacer l'image");
                }
  
            }
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
            $this->addFlash('success', 'Utilisateur crée !');
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
                'upload',
                $fileName
            );

            // Read the CSV file and do something with the data
            $csv = array_map(function($v){return str_getcsv($v, ";");}, file('upload'.'/'.$fileName));
            
            foreach ($csv as &$ligne) {
                try {
                    //La 1ère ligne correspond à l'entète
                    if ($i == 0) {
                        $i++;
                        continue;
                    }
                    
                    $separer = $ligne;
                    //Si le nombre de colonne est différent de 7 alors il y a un problème dans les données fournis pour cette ligne
                    if (count($separer) != 7) {
                        $i++;
                        $arrayOfErrrors[] = "Erreur lors de la tentative de création de l'utilisateur à la ligne " . $i . " . Veuillez vérifier les informations de l'utilisateur.";
                        continue;
                    }
                    $user = $userRepository->findOneBy(array('pseudo' => $separer[4]));
                    //On verifie si le user existe déja par son pseudo si oui alors on fait un update du User autrement on le créer
                    if ($user == null) {
                        $user = new User();
                    }
                    //Vérification format Email
                    if (!filter_var($separer[0], FILTER_VALIDATE_EMAIL)) {
                        $arrayOfErrrors[] = "Erreur lors de la tentative de création de l'utilisateur à la ligne " . $i . " . Le format de l'email n'est pas bon.";
                        $i++;
                        continue;
                    }
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
                    //Vérification format téléphone
                    if(!preg_match('/^[0-9]{10}+$/', $separer[5])) {
                        $arrayOfErrrors[] = "Erreur lors de la tentative de création de l'utilisateur à la ligne " . $i . " . Le format du numéro de téléphone n'est pas bon.";
                        $i++;
                        continue;
                    }
                    $user->setTelephone($separer[5]);
                    
                    $siteObj = $sitesRepository->findByName($separer[6]);

                    $user->setSites($siteObj);

                    $entityManager->persist($user);
                    $entityManager->flush();
                    $i++;
                }catch (\Exception $e){
                    $arrayOfErrrors[] = "Erreur lors de la tentative de création de l'utilisateur à la ligne " . $i . " . Veuillez vérifier les informations de l'utilisateur.";
                }
            }
        }
        //Afficahge de la liste des erreurs présent dans le fichier par un bandeau flash
        foreach ($arrayOfErrrors as &$errror) {
            $this->addFlash("danger", $errror);
        }
        return $this->render('user/import.html.twig', [
            'Importform' => $form->createView(),
        ]);
    }
    #[Route('/user/liste', name: 'user_list')]
    public function userList(Request $request, UserRepository $userRepository) {
        $users = $userRepository->findAll();
        return $this->render('user/userList.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/user/Enable-Disable/{idUser}', name: 'user_enable_disable')]
    public function userEnableDisable($idUser, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager) {
        $user = $userRepository->find($idUser);
        if (!$user) {
            return $this->redirectToRoute('user_list');
        }

        if ($user->isActif()){
            $user->setActif(false);
        } else {
            $user->setActif(true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_list');
    }

    #[Route('/user/Delete/{idUser}', name: 'user_delete')]
    public function userDelete($idUser, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ResetPasswordRequestRepository $resetPasswordRequestRepository) {
        $user = $userRepository->find($idUser);

        if (!$user) {
            return $this->redirectToRoute('user_list');
        }

        $sortiesUser = $user->getOrganisations();
        $participationsUser = $user->getInscriptions();
        $resetPassUser = $resetPasswordRequestRepository->findBy([
           'user' => $user
        ]);

        foreach ($resetPassUser as $unResetPassUser) {
            $entityManager->remove($unResetPassUser);
        }

        foreach ($sortiesUser as $sortie){
            $inscriptions = $sortie->getInscriptions();
            foreach ($inscriptions as $inscription) {
                $entityManager->remove($inscription);
            }
            $entityManager->remove($sortie);
        }

        foreach ($participationsUser as $inscription) {
            $entityManager->remove($inscription);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_list');
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
