<?php

namespace App\Entity;

use App\Repository\AboutUsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: AboutUsRepository::class)]
class AboutUs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $quote = null;

    #[ORM\Column(length: 1000)]
    private ?string $description = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 1000)]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    private ?string $scheduleWeekdays = null;

    #[ORM\Column(length: 255)]
    private ?string $scheduleSat = null;

    #[ORM\Column(length: 255)]
    private ?string $scheduleSun = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(?string $quote): static
    {
        $this->quote = $quote;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getScheduleWeekdays(): ?string
    {
        return $this->scheduleWeekdays;
    }

    public function setScheduleWeekdays(string $scheduleWeekdays): static
    {
        $this->scheduleWeekdays = $scheduleWeekdays;

        return $this;
    }

    public function getScheduleSat(): ?string
    {
        return $this->scheduleSat;
    }

    public function setScheduleSat(string $scheduleSat): static
    {
        $this->scheduleSat = $scheduleSat;

        return $this;
    }

    public function getScheduleSun(): ?string
    {
        return $this->scheduleSun;
    }

    public function setScheduleSun(string $scheduleSun): static
    {
        $this->scheduleSun = $scheduleSun;

        return $this;
    }
}
