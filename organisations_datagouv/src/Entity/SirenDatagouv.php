<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\SirenDatagouvRepository")
 */
class SirenDatagouv
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $siren;

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

    public function getSiren(): ?int
    {
        return $this->siren;
    }

    public function setSiren(?int $siren): self
    {
        $this->siren = $siren;

        return $this;
    }
}
