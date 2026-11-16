<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * F-10 : inscription client ou coiffeur avec consentement RGPD.
 */
final class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        Security $security,
    ): Response {
        // Déjà connecté : pas besoin de s'inscrire
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Lien « Proposer mes services » : le choix « coiffeur » est présélectionné
        if ($request->query->get('type') === 'coiffeur') {
            $form->get('accountType')->setData(RegistrationFormType::TYPE_HAIRDRESSER);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isHairdresser = $form->get('accountType')->getData() === RegistrationFormType::TYPE_HAIRDRESSER;

            $user
                ->setEmail(mb_strtolower(trim((string) $user->getEmail())))
                ->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()))
                ->setRoles([$isHairdresser ? 'ROLE_HAIRDRESSER' : 'ROLE_CLIENT'])
                ->setConsentAt(new \DateTimeImmutable()); // preuve du consentement RGPD

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $isHairdresser
                ? 'Bienvenue ! Votre compte coiffeur est créé. Vous pourrez bientôt créer votre fiche.'
                : 'Bienvenue ! Votre compte est créé.');

            // Connexion automatique après l'inscription
            return $security->login($user, 'form_login', 'main')
                ?? $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
