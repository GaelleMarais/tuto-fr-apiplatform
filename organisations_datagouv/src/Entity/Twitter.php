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
 * @ApiFilter(SearchFilter::class, properties={"datagouvid": "partial", "twitter":"partial"})
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
     * L'identifiant data.gouv de l'organisation
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     */
    private $datagouvid;

    /**
     * L'identifiant twitter de l'organisation
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read"})
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
