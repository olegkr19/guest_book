<?php

namespace App\Entity;

use App\Repository\MessageFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageFileRepository::class)]
class MessageFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\ManyToOne(inversedBy: 'id', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Message $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageId(): ?Message
    {
        return $this->message;
    }

    public function setMessageId(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }
}
