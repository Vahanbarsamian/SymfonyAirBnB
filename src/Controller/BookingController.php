<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookingController extends AbstractController
{

    /**
     * This method alloaw to add a reservation
     * @Route("/booking/ad/{slug}", name="ads_add_booking")
     * @IsGranted("ROLE_USER")
     */
    public function addAction(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dateStart = $booking->getStartDate()->getTimestamp();
            $dateEnd = $booking->getEndDate()->getTimestamp();
            $booking->setAd($ad)
            ->setBooker($this->getUser());
            if ($booking->unvalidateDays()) {
                $unvalidateDays = $booking->unvalidateDays();
                $this->addFlash(
                    'warning',
                    "<h5><strong>Attention !!!</strong></h5>
                    <p>Desolé... ce bien est déjà retenu pour la période du
                    <b>{$this->formatDate($unvalidateDays[0])} au
                    {$this->formatDate($unvalidateDays[count($unvalidateDays)-1])} inclus.</b></p>
                    <p>Veuillez décaler votre réservation si vous le pouvez... Merci!</p>"
                );
                return $this->redirectToRoute("ads_add_booking", ["slug"=>$ad->getSlug()]);
            }
            $manager->persist($booking);
            $manager->flush();
            setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
            $startDate = strftime('%A %e %B %Y', $dateStart);
            $endDate = strftime('%A %e %B %Y', $dateEnd);

                return $this->redirectToRoute("ad_get_booking", ['id'=>$booking->getId(),'withAlert'=>true]);
        }
        return $this->render(
            'booking/form_booking.html.twig',
            [
                'titre' => "Reservation du bien nommé: &nbsp; ".$ad->getTitle(),
                'ad'=>$ad,
                'booking'=>$booking,
                'form'=> $form->createView()
            ]
        );
    }

    /**
     * This method show details of booking passed
     * @param Booking $booking
     * @return Response
     * @Route("/booking/get/{id}", name="ad_get_booking")
     */
    public function getAction(Booking $booking)
    {
        return $this->render(
            "/booking/resume_booking.html.twig",
            [
                'booking'=>$booking
            ]
        );
    }

    private function formatDate($myDate)
    {
        $date = new \DateTime($myDate);
        $date = $date->getTimestamp();
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        $date = strftime('%A %e %B %Y', $date);
        return $date;
    }
}
