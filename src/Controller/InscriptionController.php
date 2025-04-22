<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $hasher->hashPassword($utilisateur, $utilisateur->getMdp());
            $utilisateur->setMdp($hashedPassword);
            $utilisateur->setRole('ROLE_USER');
            $utilisateur->setDateInscription(new \DateTime());

            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}