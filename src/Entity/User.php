<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
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
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenExpire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActiveUser;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getToken(): string{
        return (string) $this->token;
    }

    public function setToken(?string $token): self{
        $this->token = $token;

        return $this;
    }

    public function getTokenExpire(): ?\DateTimeInterface{
        /** @var \DateTime $tokenExpire */
        $tokenExpire = $this->tokenExpire;
        return $tokenExpire;
    }

    public function setDate(?\DateTime $tokenExpire): self
    {
        $this->tokenExpire = $tokenExpire;

        return $this;
    }

    public function getIsActiveUser(): ?bool{
        return $this->isActiveUser;
    }

    public function setIsActiveUser(bool $isActiveUser): self{
        $this->isActiveUser = $isActiveUser;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function encodePassword(string $raw, ?string $salt)
    {
        // TODO: Implement encodePassword() method.
    }

    public function isPasswordValid(string $encoded, string $raw, ?string $salt)
    {
        // TODO: Implement isPasswordValid() method.
    }

    public function needsRehash(string $encoded): bool
    {
        // TODO: Implement needsRehash() method.
    }
}
