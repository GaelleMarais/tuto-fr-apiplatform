<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"read"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"datagouvid": "partial", "item":"partial", "itemLabel":"partial"})
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
    * L'identifiant data.gouv de l'organisation
    *
    * @ORM\Column(type="string", length=255)
    * @Groups({"read"})
     */
    private $datagouvid;

    /**
     * L'identifiant wikidata de l'organisation
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $item;

    /**
     * Le label francophone de Wikidata de l'organisation
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read"})
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
