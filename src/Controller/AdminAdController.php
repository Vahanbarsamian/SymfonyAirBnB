<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
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
        }
        return $this->render(
            'admin/ad/forms_ad.html.twig',
            [
            'titre'=>"Edition de l'annonce <br><br><i>Id "
            .$ad->getId()."<br>"
            .$ad->getTitle()
            ."<br> de "
            .$ad->getAuthor()->fullName()
            ."</i>",
            'form'=> $form->createView(),
            'ad'=>$ad
            ]
        );
    }


    /**
     * This method permit Admin user to delete an ad
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/admin/ad/delete/{id}", name="delete_admin_ad")
     */
    public function deleteAction(Ad $ad, EntityManagerInterface $manager)
    {
        if (count($ad->getBookings())> 0) {
            $this->addFlash('warning', "<strong>Cette annonce contient des r&eacute;servations et ne peut donc &ecirc;tre supprim&eacute;e</strong>");
            return $this->redirectToRoute('list_admin_ad');
        }
        $this->removeImages($ad, $manager);
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("success", "<strong>L'annonce {$ad->getTitle()} à bien &eacute;t&eacute; supprim&eacute;e !!!</strong>");
        return $this->redirectToRoute('list_admin_ad');
    }

    /**
    * This method only delete images join to an ad
    *
    * @param Ad $ad
    * @param EntityManagerInterface $manager
    *
    * @return void
    */
    public function removeImages($ad, $manager)
    {
        $images = $ad->getImages();
        foreach ($images as $image) {
            $ad->removeImage($image);
            $manager->remove($image);
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
