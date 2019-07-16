<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TwitterRepository")
 */
class Twitter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $datagouvid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitterUsername;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatagouvid(): ?string
    {
        return $this->datagouvid;
    }

    public function setDatagouvid(string $datagouvid): self
    {
        $this->datagouvid = $datagouvid;

        return $this;
    }

    public function getTwitterUsername(): ?string
    {
        return $this->twitterUsername;
    }

    public function setTwitterUsername(?string $twitterUsername): self
    {
        $this->twitterUsername = $twitterUsername;

        return $this;
    }
}
