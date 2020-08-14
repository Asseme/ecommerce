<?php

namespace App\Controller;
use App\Form\RegistrationFormType;
use App\Repository\PanierRepository;
use App\Security\AppUserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class MonCompteController extends AbstractController
{
    /**
     * @Route("/mon/compte", name="mon_compte")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppUserAuthenticator $authenticator, PanierRepository $panierRepository): Response
    {
        /* On récupère le user connecté */
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('mon_compte/index.html.twig', [
            'controller_name' => 'MonCompteController',
            'registrationForm' => $form->createView(),
            'paniers' => $panierRepository->findBy(['user'=>$user]),
        ]);
    }
}
