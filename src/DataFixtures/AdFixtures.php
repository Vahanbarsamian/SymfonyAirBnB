<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // Gestion des Roles
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $userRole = new Role();
        $userRole->setTitle('ROLE_USER');
        $manager->persist($userRole);
        
        $adminUser = new User();
        $adminUser->setFirstName('Vahan')
        ->setLastName('Barsamian')
        ->setEmail('vahanbarsamian@gmail.com')
        ->setIntroduction('Jeune développeur de 50 ans réalise un voeux')
                ->setDescription("<p>Jeune développeur de 50 ans j'ai entammé une reconversion en PHP et Symfony notamment</p>")
                ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                ->setPicture('https://avataaars.io/?avatarStyle=Circle&topType=ShortHairShortCurly&accessoriesType=Prescription01&hairColor=BrownDark&facialHairType=Blank&clotheType=BlazerShirt&eyeType=Default&eyebrowType=DefaultNatural&mouthType=Smile&skinColor=Pale')
                ->addRole($adminRole);
        $manager->persist($adminUser);

        // Gestion des Users
        $genres = ["male","female"];
        $users = [];
        for ($i=0; $i < 10; $i++) {
            $genre = $faker->randomElement($genres);
    
            $picture = "https://randomuser.me/api/portraits/";
            $numb = $faker->numberBetween(1, 99).".jpg";
            $picture .= ($genre == 'male'? "men/":"women/").$numb;
            
            $user = new User();
            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->paragraph(2))
                ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)).'</p>')
                ->setHash($hash)
                ->setPicture($picture)
                ->addRole($userRole);
            $manager->persist($user);
            $users[]= $user;
        }

        // Gestion des annonces
        for ($i = 1; $i <=30; $i++) {
            $title = $faker->sentence(6);
            $imageCover = $faker->imageUrl(400, 200);
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>', $faker->paragraphs(5)).'</p>';
            $user = $users[mt_rand(0, count($users)-1)];

            $ad = new Ad();
            $ad->setTitle($title)
                ->setPrice(mt_rand(80, 120))
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setCoverImage($imageCover)
                ->setRooms(mt_rand(1, 3))
                ->setAuthor($user);

        // Nombre d'images associées aléatoires
            for ($j=1; $j<=(mt_rand(2, 5)); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }
            
        // Nombre de réservations associées aléatoires
            for ($r = 1; $r<= mt_rand(0, 10); $r++) {
                // Définition des variables
                $dateCreate = $faker->dateTimeBetween('-6 months');
                $dateStart = $faker->dateTimeBetween('-3 months');
                $duration = mt_rand(1, 90);
                $dateEnd = (clone $dateStart)->modify("+ $duration days");
                $amount = $ad->getPrice()*$duration;
                $comment = $faker->paragraph();
                $booker = $users[mt_rand(0, count($users)-1)];
                $reservation = new Booking();
                $reservation->setAd($ad)
                    ->setBooker($booker)
                    ->setAd($ad)
                    ->setCreateAt($dateCreate)
                    ->setStartDate($dateStart)
                    ->setEndDate($dateEnd)
                    ->setAmount($amount)
                    ->setComment($comment);
                $manager->persist($reservation);
            }
            $manager->persist($ad);
                        // Nombre de commentaires associés aléatoires
            if (mt_rand(0, 1)) {
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setRating(mt_rand(1, 5))
                    ->setAuthor($booker)
                    ->setAd($ad);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
