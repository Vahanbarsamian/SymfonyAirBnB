<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields = {"email"},
 *  message ="Un utilisateur avec le même email existe déjà !!!. Veuillez en saisir un autre merci !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Regex(pattern="/\d/", match=false, message="Le prénom ne peut contenir de chiffres")
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/\d/", match=false, message="Le nom ne peut contenir de chiffres")
     * @Assert\NotBlank(message="Vous devez renseigner votre nom de famille")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide !")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez saisir une url valide pour votre image de profil")
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(min=8, minMessage="Le mot de passe doit au moins contenir {{ limit }} caractères")
     */
    private $hash;

    /**
     * Confirm Password
     *
     * @var string
     *
     * @Assert\EqualTo(
     *  propertyPath="hash",
     *  message="Le mot de passe et confirmation mot de passe doivent être identique"
     * )
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Length(min=20, minMessage="L'introduction doit au minimum contenir {{ limit }} caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\Length(min=50, minMessage="L'introduction doit au minimum contenir {{ limit }} caractères")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Ad::class, mappedBy="author")
     */
    private $ads;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="users")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="booker")
     */
    private $bookings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    /**
    * This methode create an automatic slug in case it isn't completed
    *
    * @ORM\PrePersist
    * @ORM\PreUpdate
    *
    * @return void
    */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugUser = new Slugify();
            $this->setSlug($slugUser->slugify($this->firstName.' '.$this->lastName));
            return $this->slug;
        }
        return $this->slug;
    }

    /**
     * This method return the user fullname
     *
     * @return string
     */
    public function fullName()
    {
        return "$this->firstName  $this->lastName" ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Returns the roles granted to the user.
    *
    * @return Collection|Role[]
    */
    public function getRoles()
    {
        $userRoles = $this->roles->map(function ($role) {
            return $role->getTitle();
        })->toArray();
        $userRoles[] = 'ROLE_USER';
        return $userRoles;
    }

    /**
    * Returns the password used to authenticate the user.
    *
    * This should be the encoded password. On authentication, a plain-text
    * password will be salted, encoded, and then compared to this value.
    *
    * @return string|null The encoded password if any
    */
    public function getPassword()
    {
        return $this->hash;
    }

    /**
    * Returns the salt that was originally used to encode the password.
    *
    * This can return null if the password was not encoded using a salt.
    *
    * @return string|null The salt
    */
    public function getSalt()
    {
    }

    /**
    * Returns the username used to authenticate the user.
    *
    * @return string The username
    */
    public function getUsername()
    {
        return $this->email;
    }

    /**
    * Removes sensitive data from the user.
    *
    * This is important if, at any given point, sensitive information like
    * the plain-text password is stored on this object.
    */
    public function eraseCredentials()
    {
    }

    /**
     * Get confirm Password
     *
     * @return  string
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * Set confirm Password
     *
     * @param  string  $confirmPassword  Confirm Password
     *
     * @return  self
     */
    public function setConfirmPassword(string $confirmPassword)
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            $role->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
            }
        }

        return $this;
    }
}
