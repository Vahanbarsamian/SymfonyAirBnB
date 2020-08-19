<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAccountController extends AbstractController
{
    /**
     * Login
     * @return Response
     * @Route("/admin/account/login", name="admin_account_login")
     */
    public function loginAction(AuthenticationUtils $utils)
    {
        ($error = $utils->getLastAuthenticationError())? $error ="Login et/ou mot de passe incorrects... Accès refusé !": null;
    
        return $this->render('admin/account/admin_account_login.html.twig', [
            'titre' => 'Accès Administration',
            'error'=>$error,
            'lastUser'=>$utils->getLastUsername()
        ]);
    }

    /**
     * Logout
     * @Route("/admin/account/logout", name="admin_account_logout")
     * @return void
     */
    public function logout()
    {
    }
}
