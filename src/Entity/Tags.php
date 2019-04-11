<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tags
 *
 * @ORM\Table(name="tags", indexes={@ORM\Index(name="cms-status", columns={"status"})})
 * @ORM\Entity(repositoryClass="App\Repository\TagsRepository")
 */
class Tags
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false, options={"comment"="Tên tag"})
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slug", type="string", length=200, nullable=true)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh đại diện cho tag"})
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"comment"="Mô tả tag"})
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false, options={"comment"="Ngày tạo tag"})
     */
    private $createdDate;

    /**
     * @var int
     *
     * @ORM\Column(name="focus", type="integer", nullable=false, options={"comment"="Tag nổi bật"})
     */
    private $focus = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="post_count", type="integer", nullable=false, options={"comment"="Tổng số bài viết"})
     */
    private $postCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="is_special", type="integer", nullable=false, options={"comment"="0: Tag thường; 1: Tag đặc biệt"})
     */
    private $isSpecial = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="summary", type="string", length=2000, nullable=true, options={"comment"="Giới thiệu tổng quan về tag đặc biệt"})
     */
    private $summary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="focus_posts", type="string", length=200, nullable=true, options={"comment"="ID post nổi bật cách nhau dấu ,"})
     */
    private $focusPosts;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"default"="1","comment"="Trạng thái: 0-Chưa kích hoạt, 1-Đã kích hoạt"})
     */
    private $status = '1';

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false, options={"comment"="Điểm cho tag đặc biệt"})
     */
    private $points = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_tags", type="string", length=100, nullable=true, options={"comment"="Tên thiết bị / Hệ điều hành cho Game"})
     */
    private $otherTags;

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getFocus(): ?int
    {
        return $this->focus;
    }

    public function setFocus(int $focus): self
    {
        $this->focus = $focus;

        return $this;
    }

    public function getPostCount(): ?int
    {
        return $this->postCount;
    }

    public function setPostCount(int $postCount): self
    {
        $this->postCount = $postCount;

        return $this;
    }

    public function getIsSpecial(): ?int
    {
        return $this->isSpecial;
    }

    public function setIsSpecial(int $isSpecial): self
    {
        $this->isSpecial = $isSpecial;

        return $this;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getOtherTags(): ?string
    {
        return $this->otherTags;
    }

    public function setOtherTags(?string $otherTags): self
    {
        $this->otherTags = $otherTags;

        return $this;
    }


}
