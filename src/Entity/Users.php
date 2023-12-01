<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UsersRepository::class)]

#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $FirstName = null;

    #[ORM\Column(length: 50)]
    private ?string $LastName = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateOfBith = null;

    #[ORM\Column(nullable: true)]
    private ?int $LoyaltyPoints = null;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Appointements::class)]
    private Collection $user;

    #[ORM\OneToMany(mappedBy: 'users', targetEntity: Notifications::class)]
    private Collection $users;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $resetToken;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiration = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): static
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): static
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDateOfBith(): ?\DateTimeInterface
    {
        return $this->DateOfBith;
    }

    public function setDateOfBith(\DateTimeInterface $DateOfBith): static
    {
        $this->DateOfBith = $DateOfBith;

        return $this;
    }

    public function getLoyaltyPoints(): ?int
    {
        return $this->LoyaltyPoints;
    }

    public function setLoyaltyPoints(?int $LoyaltyPoints): static
    {
        $this->LoyaltyPoints = $LoyaltyPoints;

        return $this;
    }

    /**
     * @return Collection<int, Appointements>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Appointements $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setUsers($this);
        }

        return $this;
    }

    public function removeUser(Appointements $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUsers() === $this) {
                $user->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }
    // Dans votre entité Users

    public function setResetTokenExpiration(\DateTimeInterface $resetTokenExpiration): self
    {
        $this->resetTokenExpiration = $resetTokenExpiration;
        return $this;
    }

    public function isPasswordResetTokenExpired(): bool
    {
        if ($this->resetToken === null || $this->resetTokenExpiration === null) {
            return true; // Le token n'a pas été défini, donc il est expiré.
        }

        if ($this->resetTokenExpiration instanceof \DateTime) {
            // Récupérez la date actuelle
            $now = new \DateTime();

            // Calculez la date d'expiration en ajoutant 30 minutes à la date de création du token
            $expirationTime = clone $this->resetTokenExpiration;
            $expirationTime->add(new \DateInterval('PT30M'));

            // Comparez la date actuelle avec la date d'expiration
            if ($now > $expirationTime) {
                return true; // Le token a expiré.
            }

            return false; // Le token est toujours valide.
        } else {
            return true; // En cas d'erreur, considérez que le token a expiré
        }
    }
    private $plainPassword;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
