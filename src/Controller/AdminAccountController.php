<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAccountController extends AbstractController
{
    /**
     * Login
     * @return Response
     * @Route("/admin/account/login", name="admin_account_login")
     */
    public function loginAction()
    {
        return $this->render('admin/account/admin_account_login.html.twig', [
            'titre' => 'Accès Administration'
        ]);
    }

    /**
     * Logout
     * @Route("/admin/account/logout",name="admin_account_logout")
     * @return void
     */
    public function logout()
    {
    }
}
