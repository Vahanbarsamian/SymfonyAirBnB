<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    /**
     * This function return all ad values
     *
     * @return Response
     *
     * @Route("/ads", name="list_ad")
     */
    public function listAction(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render(
            'ad/list_ad.html.twig',
            [
            'titre' => 'Liste des locations de nos hôtes',
            'ads' => $ads
            ]
        );
    }
    
    /**
     * This method create a new ad
     *
     * @return Response
     *
     * @Route("/ad/create", name="create_ad")
     */
    public function addAction(Request $request, EntityManagerInterface $manager)
    {
        $ad = new Ad();
        $user = $this->getUser();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($user);
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash("success", "L'annonce <strong>{$ad->getTitle()}</strong> � bien �t� enregistr�e !");
            return $this->redirectToRoute('get_ad', ['slug'=>$ad->getSlug()]);
        }
        return $this->render(
            'ad/forms_ad.html.twig',
            [
                'titre'=>"Cr�ation d'une nouvelle annonce",
                'form' => $form->createView()
            ]
        );
    }

    /**
     * This method return the selected ad
     *
     * @return Response
     *
     * @Route("/ad/{slug}", name="get_ad")
     */
    public function getAction(Ad $ad)
    {
        return $this->render(
            'ad/get_ad.html.twig',
            [
                'titre' => $ad->getTitle(),
                'ad' => $ad,
                'userSlug'=> null !== $this->getUser()->getSlug() ?  $this->getUser():null
            ]
        );
    }

    /**
    * This method edit the choosen ad form to be modifyed
    *
    * @return Ad
    *
    * @Route("/ad/edit/{slug}", name="edit_ad")
    */
    public function editAction(Request $request, Ad $ad, EntityManagerInterface $manager)
    {
        //Initialisation d'un tableau qui contient les images pr�sentes en bdd
        $originalImages = new ArrayCollection();
        foreach ($ad->getImages() as $image) {
            $originalImages->add($image);
        }
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Comparaison de mon tableau de valeurs initial et ma request: on supprime de la bdd les images non pr�sentes dans request
            foreach ($originalImages as $image) {
                if (false === $ad->getImages()->contains($image)) {
                    $ad->removeImage($image);
                    $manager->remove($image);
                    $manager->flush();
                }
            }

            //Validation des images pr�sentes dans ma request
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', "Les modifications de l'annonce ont bien �t� prises en compte");
            return $this->redirectToRoute('get_ad', ["slug"=>$ad->getSlug()]);
        }
        return $this->render(
            "ad/forms_ad.html.twig",
            [
            'titre'=>'Modification de l\'annonce: '.$ad->getTitle(),
            'button_label'=> "Modifier cette annonce",
            'form'=>$form->createView()
            ]
        );
    }
}
