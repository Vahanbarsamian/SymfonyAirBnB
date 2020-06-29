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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
            'titre' => 'Liste des locations de nos hÃ´tes',
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
     *
     * @IsGranted("ROLE_USER")
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
            $this->addFlash("success", "L'annonce <strong>{$ad->getTitle()}</strong> à bien été enregistrée !");
            return $this->redirectToRoute('get_ad', ['slug'=>$ad->getSlug()]);
        }
        return $this->render(
            'ad/forms_ad.html.twig',
            [
                'titre'=>"Création d'une nouvelle annonce",
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
                'ad' => $ad
            ]
        );
    }

    /**
    * This method edit the choosen ad form to be modifyed
    *
    * @return Ad
    *
    * @Route("/ad/edit/{slug}", name="edit_ad")
    *
    * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous ne pouvez pas modifier l'annonce d'un tiers")
    */
    public function editAction(Request $request, Ad $ad, EntityManagerInterface $manager)
    {
        //Initialisation d'un tableau qui contient les images présentes en bdd
        $originalImages = new ArrayCollection();
        foreach ($ad->getImages() as $image) {
            $originalImages->add($image);
        }
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Comparaison de mon tableau de valeurs initial et ma request: on supprime de la bdd les images non présentes dans request
            foreach ($originalImages as $image) {
                if (false === $ad->getImages()->contains($image)) {
                    $ad->removeImage($image);
                    $manager->remove($image);
                    $manager->flush();
                }
            }

            //Validation des images présentes dans ma request
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', "Les modifications de l'annonce ont bien été prises en compte");
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

    /**
     * Undocumented function
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     *
     * @return Response
     *
     * @Route("/ad/delete/{slug}", name="delete_ad")
     *
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()")
     */
    public function deleteAction(Ad $ad, EntityManagerInterface $manager)
    {
        $this->removeImages($ad, $manager);
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash('success', "l'annonce {ad->getTitle()} à bien été supprimée");
        return $this->redirectToRoute('list_ad');
    }


    /**
     * This method only delete images join to an ad
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
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
    }
}
