<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PrePersist;
use App\Repository\BookingRepository;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Ad::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("today", message="La date d'arrivée doit forcemment être superieure à la date actuelle")
     * @Assert\GreaterThan("today",message="Vous ne pouvez reserver qu'a partir de la date de demain")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\GreaterThan(
     *  propertyPath="startDate",
     *  message="La date de départ doit être supérieur à la date d'arrivée"
     * )
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
    
    /**
     * Check that createdAt is always filled and calculate amount
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function createdAt()
    {
        if (empty($this->getCreateAt)) {
            $this->setCreateAt(new \DateTime('now'));
        }
        if (empty($this->amount)) {
            $this->setAmount($this->getDuration() * $this->ad->getPrice());
        }
    }

    /**
     * Return nb of location days
     *
     */
    public function getDuration()
    {
        return $this->getEndDate()->diff($this->getStartDate())->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    /**
     * Verify if the date passed is avilable or not
     * @return null|[]
     */
    public function unvalidateDays()
    {
        $days = $this->ad->getUnbookingDays();
        $myDays = range($this->getStartDate()->getTimestamp(), $this->getEndDate()->getTimestamp(), (24*60*60));
            $mydays = array_map(function ($item) {
                return date("Y-m-d", $item);
            }, $myDays);
        foreach ($days as $daystab) {
            if (count($daystab)>0) {
                if ($sameDate = array_intersect($daystab, $mydays)) {
                    return $sameDate;
                }
                continue;
            }
        }
        return false;
    }


    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount = $this->getDuration() * $this->ad->getPrice();
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
