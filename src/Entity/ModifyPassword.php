<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ModifyPassword
{

    /**
     * Design the old password
     *
     * @var string $oldpassword
     */
    private $oldPassword;

    /**
     * Design the new password
     *
     * @var string $newPassword
     *
     * @Assert\Length(
     *  min=8,
     *  minMessage="Un minimum de 8 caractères est requis ! Merci de bien vouloir corriger"
     * )
     */
    private $newPassword;


    /**
     * Design confirm password
     *
     * @var string $confirmPassword
     *
     * @Assert\EqualTo(
     *  propertyPath="newPassword",
     *  message="Le mot de passe n'a pas été correctement confirmé !. Veuillez réessayer... merci!"
     * )
     */
    private $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
