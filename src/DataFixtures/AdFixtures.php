<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
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
                ->setPicture($picture);
                
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
            for ($j=1; $j<=(mt_rand(2, 5)); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}
