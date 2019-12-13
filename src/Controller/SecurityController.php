<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // Check if user exist
        if ($this->getUser()){

            return $this->redirectToRoute('homepage');

        }
        
        // create a new user
        $user = new User;

        // create form
        $form = $this->createForm(RegisterType::class, $user);

        // Handle the request in post method
        $form->handleRequest($request);

        // if form is submit
        if( $form->isSubmitted() && $form->isValid() )
        {
            // Plaitext Password
            $plainPassword = $form->get('password')->getData();

            // Encode the plain password

            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $plainPassword
            );

            // Generate the activation Token
            $activationToken = md5($user->getEmail().uniqid());

            // Persist data
            $user->setPassword($encodedPassword);
            $user->setActivateToken($activationToken);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Display message
            $this->addFlash(
                'success', 'Your account is now created, check your mailbox to active this'
            );

            return $this->redirectToRoute('login');

           
        }

        // Create a view form; 
        $form = $form->createView();

        return $this->render('security/register.html.twig', 
            [ 'form' => $form]);
    }

    /**
     * @Route("/account", name="account")
     */
    public function account()
    {
        

        return $this->render('security/account.html.twig', 
            []);
    }
}
