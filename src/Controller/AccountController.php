<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\AccountType;
use App\Entity\ModifyPassword;
use App\Form\ModifyPasswordType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Connection form and route to log in
     *
     * @return Response
     *
     * @Route("/account/login", name="account_login")
     */
    public function loginAction(AuthenticationUtils $utils)
    {
        return $this->render('account/login.html.twig', [
        'titre' => 'Connexion',
        'error' => $utils->getLastAuthenticationError(),
        'lastUser' => $utils->getLastUsername()
        ]);
    }

    /**
     * Connection logout
     *
     * @return Response
     *
     * @Route("/account/logout", name="account_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * This method allows the user to create an account
     *
     * @return Response
     *
     * @Route("/account/register", name="account_register")
     */
    public function registerAction(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
    ) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $encodPassword = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($encodPassword);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "Votre compte à bien été créé !. Vous pouvez à présent vous connecter");
            return $this->redirectToRoute('account_login');
        }
        return $this->render(
            '/account/account_register.html.twig',
            [
            'titre'=>"Inscription",
            'form'=> $form->createView()
            ]
        );
    }

    /**
     * This method allows the user to modify his profile
     *
     * @return Response
     *
     * @Route("/account/profil/edit", name="profil_edit")
     *
     * @IsGranted("ROLE_USER")
     */
    public function profilEditAction(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "Vos modifications ont bien été prises en compte");
        }
        return $this->render(
            'account/profil_edit.html.twig',
            [
            'titre'=>"Modification du profil utilisateur",
            'form'=> $form->createView()
            ]
        );
    }

    /**
    *This method allws user to modify his passwordUpdateAction
    *
    * @return Response
    *
    * @Route("/account/update/password", name="password_update")
    *
    * @IsGranted("ROLE_USER")
    */
    public function passwordUpdateAction(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encode
    ) {
        $passwordUpdate = new ModifyPassword();
        $user = $this->getUser();
        $form = $this->createForm(ModifyPasswordType::class, $passwordUpdate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $newpassword = $passwordUpdate->getNewPassword();
                $user->setHash($encode->encodePassword($user, $newpassword));
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', "Le mot de passe a bien été modifié !");
                return $this->redirectToRoute('password_update');
            }
                $form->get('oldPassword')->addError(new FormError(
                    "Le mot de passe saisi ne correspond pas a l'ancien mot de passe connu !"
                ));
        }
        
        return $this->render(
            'account/password_update.html.twig',
            [
            'titre'=>"Modification du mot de passe de ".$user->getFirstname().' '.$user->getLastName(),
            'form'=> $form->createView()
            ]
        );
    }

    /**
     * This method allow current user see its profil
     *
     * @return Response
     *
     * @Route("/account/user", name="account_user")
     *
     * @Security("is_granted('ROLE_USER')", message="Vous ne pouvez pas acceder au profil d'un tiers")
     */
    public function userAccountAction()
    {
        $user = $this->getUser();
        return $this->render(
            'user/user_account.html.twig',
            [
                'titre'=> 'Mon Compte utilisateur',
                'user' => $user
            ]
        );
    }

    /**
     * This method redirect to user bookings view
     *
     * @return Response
     *
     * @Route("/account/show/bookings", name="account_bookings")
     */
    public function bookings()
    {
        return $this->render(
            'account/show_bookings.html.twig',
            [
                "titre"=>"Liste de mes réservations"
            ]
        );
    }
}
