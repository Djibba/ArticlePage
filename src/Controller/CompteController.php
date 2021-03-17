<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CompteController extends AbstractController
{
    /**
     * @Route("/account", name="app_compte", methods={"GET"})
     */
    public function index(UserRepository $userRepo): Response
    {
        $user = $userRepo->findAll();

        return $this->render('compte/index.html.twig');
    }

    /**
     * @Route("/account/edit", name="app_compte_edit", methods={"GET", "PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        
        $user = $this->getUser();
        
        $form = $this->createForm(UserFormType::class, $user, [
            'method' => 'PUT'
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $manager->flush();

            $this->addFlash('success', 'Profil modifié modifié avec succès');

            return $this->redirectToRoute('app_compte');
        }

        return $this->render('compte/edit.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/change-password", name="app_compte_change_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));

            $manager->flush();

            $this->addFlash('success', 'Mot de passe modifié modifié avec succès');

            return $this->redirectToRoute('app_compte');

        }

        return $this->render('compte/change.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}