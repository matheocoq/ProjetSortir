<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionsRepository::class)]
class Inscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_inscription = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?sorties $sorties = null;

    #[ORM\ManyToOne(inversedBy: 'inscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $participant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getSorties(): ?sorties
    {
        return $this->sorties;
    }

    public function setSorties(?sorties $sorties): self
    {
        $this->sorties = $sorties;

        return $this;
    }

    public function getParticipant(): ?user
    {
        return $this->participant;
    }

    public function setParticipant(?user $participant): self
    {
        $this->participant = $participant;

        return $this;
    }
}
