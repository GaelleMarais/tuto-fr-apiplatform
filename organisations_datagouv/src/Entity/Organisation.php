<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
* @ApiResource(
*     collectionOperations={"get"},
*     itemOperations={"get"}
* )
 * @ORM\Entity(repositoryClass="App\Repository\OrganisationRepository")
 */
class Organisation
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
    private $item;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $itemLabel;

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

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function setItem(?string $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getItemLabel(): ?string
    {
        return $this->itemLabel;
    }

    public function setItemLabel(?string $itemLabel): self
    {
        $this->itemLabel = $itemLabel;

        return $this;
    }
}
