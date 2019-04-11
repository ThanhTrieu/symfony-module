<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpecialTags
 *
 * @ORM\Table(name="special_tags")
 * @ORM\Entity(repositoryClass="App\Repository\SpecialTagsRepository")
 */
class SpecialTags
{
    /**
     * @var int
     *
     * @ORM\Column(name="tag_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tagId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="summary", type="string", length=2000, nullable=true, options={"comment"="Mô tả"})
     */
    private $summary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="focus_posts", type="string", length=200, nullable=true)
     */
    private $focusPosts;

    /**
     * @var string|null
     *
     * @ORM\Column(name="release_date", type="string", length=50, nullable=true, options={"comment"="Ngày ra mắt game"})
     */
    private $releaseDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ios_link", type="string", length=255, nullable=true, options={"comment"="Link game IOS"})
     */
    private $iosLink;

    /**
     * @var string|null
     *
     * @ORM\Column(name="android_link", type="string", length=255, nullable=true, options={"comment"="Link game Android"})
     */
    private $androidLink;

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getFocusPosts(): ?string
    {
        return $this->focusPosts;
    }

    public function setFocusPosts(?string $focusPosts): self
    {
        $this->focusPosts = $focusPosts;

        return $this;
    }

    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?string $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getIosLink(): ?string
    {
        return $this->iosLink;
    }

    public function setIosLink(?string $iosLink): self
    {
        $this->iosLink = $iosLink;

        return $this;
    }

    public function getAndroidLink(): ?string
    {
        return $this->androidLink;
    }

    public function setAndroidLink(?string $androidLink): self
    {
        $this->androidLink = $androidLink;

        return $this;
    }


}
