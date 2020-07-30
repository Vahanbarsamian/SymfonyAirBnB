<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
    * Undocumented function
    *
    * @param User $user
    *
    * @return Response
    *
    * @Route("/account/profil/{slug}", name="account_profil")
    */
    public function accountUserAction(User $user)
    {
        return $this->render(
            'user/user_account.html.twig',
            [
            'titre'=> "A propos de : ".$user->fullName(),
            'user' => $user
            ]
        );
    }
}
