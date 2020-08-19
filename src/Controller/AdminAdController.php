<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * This method show all ads in administration view
     * @return Response
     * @Route("/admin/ads", name="list_admin_ad")
     */
    public function listAction(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('admin/ad/list_admin_ad.html.twig', [
            'titre' => 'Administration des annonces',
            'ads'=>$ads
        ]);
    }

    /**
     * This method edit an existing ad to be modified
     * @param Ad $ad
     * @return Response
     * @Route("/admin/ad/edit/{id}", name="edit_admin_ad")
     */
    public function editAction(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                "success",
                "<h5>La modification de l'annonce à bien été effectuée</h5>"
            );
            return $this->redirectToRoute("edit_admin_ad", ['id'=>$ad->getId()]);
        }
        return $this->render(
            'admin/ad/forms_ad.html.twig',
            [
            'titre'=>"Edition de l'annonce Id ".$ad->getId()." de ".$ad->getAuthor()->fullName(),
            'form'=> $form->createView()
            ]
        );
    }
}
