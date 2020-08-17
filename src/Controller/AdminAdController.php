<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="list_admin_ad")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('admin/ad/list_admin_ad.html.twig', [
            'titre' => 'Administration des annonces',
            'ads'=>$ads
        ]);
    }
}
