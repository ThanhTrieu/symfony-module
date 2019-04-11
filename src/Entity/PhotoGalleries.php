<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PhotoGalleries
 *
 * @ORM\Table(name="photo_galleries")
 * @ORM\Entity
 */
class PhotoGalleries
{
    /**
     * @var int
     *
     * @ORM\Column(name="gallery_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $galleryId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"comment"="Tiêu đề thư viện ảnh"})
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh đại diện"})
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"comment"="Mô tả"})
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cate_id", type="integer", nullable=true, options={"comment"="Chuyên mục"})
     */
    private $cateId;

    /**
     * @var int
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=false, options={"comment"="Người tạo"})
     */
    private $creatorId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="modifier_id", type="integer", nullable=true, options={"comment"="Người sửa cuối"})
     */
    private $modifierId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false, options={"comment"="Ngày tạo"})
     */
    private $createdDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true, options={"comment"="Ngày sửa cuối"})
     */
    private $modifiedDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_avatar", type="string", length=255, nullable=true, options={"comment"="Link ảnh đại diện nguồn"})
     */
    private $srcAvatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_link", type="string", length=255, nullable=true, options={"comment"="Link trang nguồn"})
     */
    private $srcLink;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"comment"="Trạng thái: 0-Chờ duyệt, 1-Duyệt"})
     */
    private $status = '0';

    public function getGalleryId(): ?int
    {
        return $this->galleryId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getCateId(): ?int
    {
        return $this->cateId;
    }

    public function setCateId(?int $cateId): self
    {
        $this->cateId = $cateId;

        return $this;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): self
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    public function getModifierId(): ?int
    {
        return $this->modifierId;
    }

    public function setModifierId(?int $modifierId): self
    {
        $this->modifierId = $modifierId;

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

    public function getModifiedDate(): ?\DateTimeInterface
    {
        return $this->modifiedDate;
    }

    public function setModifiedDate(?\DateTimeInterface $modifiedDate): self
    {
        $this->modifiedDate = $modifiedDate;

        return $this;
    }

    public function getSrcAvatar(): ?string
    {
        return $this->srcAvatar;
    }

    public function setSrcAvatar(?string $srcAvatar): self
    {
        $this->srcAvatar = $srcAvatar;

        return $this;
    }

    public function getSrcLink(): ?string
    {
        return $this->srcLink;
    }

    public function setSrcLink(?string $srcLink): self
    {
        $this->srcLink = $srcLink;

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


}
