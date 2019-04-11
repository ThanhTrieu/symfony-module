<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CelebPhotos
 *
 * @ORM\Table(name="celeb_photos")
 * @ORM\Entity
 */
class CelebPhotos
{
    /**
     * @var int
     *
     * @ORM\Column(name="celeb_photo_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $celebPhotoId;

    /**
     * @var int
     *
     * @ORM\Column(name="celeb_id", type="integer", nullable=false, options={"comment"="ID người nổi tiếng"})
     */
    private $celebId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=500, nullable=true, options={"comment"="Tiêu đề ảnh"})
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true, options={"comment"="Đường dẫn ảnh"})
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_url", type="string", length=255, nullable=true, options={"comment"="Đường dẫn ảnh trang nguồn"})
     */
    private $srcUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=false, options={"comment"="Người tạo"})
     */
    private $creatorId = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false, options={"comment"="Ngày tạo"})
     */
    private $createdDate;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"comment"="Trạng thái"})
     */
    private $status = '0';

    public function getCelebPhotoId(): ?int
    {
        return $this->celebPhotoId;
    }

    public function getCelebId(): ?int
    {
        return $this->celebId;
    }

    public function setCelebId(int $celebId): self
    {
        $this->celebId = $celebId;

        return $this;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getSrcUrl(): ?string
    {
        return $this->srcUrl;
    }

    public function setSrcUrl(?string $srcUrl): self
    {
        $this->srcUrl = $srcUrl;

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

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

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
