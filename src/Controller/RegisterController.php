<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encode): Response
    {
        $user = new Utilisateur;
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encode->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setStatus(true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Vous avez bien inscrit');
            return $this->redirectToRoute('register');
        }
        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
