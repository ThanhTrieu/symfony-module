<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Celebrities
 *
 * @ORM\Table(name="celebrities")
 * @ORM\Entity
 */
class Celebrities
{
    /**
     * @var int
     *
     * @ORM\Column(name="celeb_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $celebId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, options={"comment"="Tên người nổi tiếng"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh người nổi tiếng"})
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true, options={"comment"="Mô tả nghệ sỹ"})
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="biography", type="text", length=65535, nullable=true, options={"comment"="Tiểu sử"})
     */
    private $biography;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true, options={"comment"="Ngày sinh"})
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_avatar", type="string", length=255, nullable=true, options={"comment"="Avatar nguồn"})
     */
    private $srcAvatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_link", type="string", length=255, nullable=true, options={"comment"="Link nguồn"})
     */
    private $srcLink;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_photo_link", type="string", length=255, nullable=true, options={"comment"="Link ảnh nghệ sỹ trang nguồn"})
     */
    private $srcPhotoLink;

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_birthday", type="string", length=50, nullable=true, options={"comment"="Ngày sinh nguồn"})
     */
    private $srcBirthday;

    /**
     * @var int|null
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=true, options={"comment"="Người tạo"})
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
     * @ORM\Column(name="post_count", type="integer", nullable=false, options={"comment"="Số bài được gắn nghệ sỹ"})
     */
    private $postCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="photo_count", type="integer", nullable=false, options={"comment"="Tổng số ảnh của nghệ sỹ"})
     */
    private $photoCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"comment"="Trạng thái"})
     */
    private $status = '0';

    public function getCelebId(): ?int
    {
        return $this->celebId;
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

    public function setSlug(string $slug): self
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

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

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

    public function getSrcPhotoLink(): ?string
    {
        return $this->srcPhotoLink;
    }

    public function setSrcPhotoLink(?string $srcPhotoLink): self
    {
        $this->srcPhotoLink = $srcPhotoLink;

        return $this;
    }

    public function getSrcBirthday(): ?string
    {
        return $this->srcBirthday;
    }

    public function setSrcBirthday(?string $srcBirthday): self
    {
        $this->srcBirthday = $srcBirthday;

        return $this;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function setCreatorId(?int $creatorId): self
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

    public function getPostCount(): ?int
    {
        return $this->postCount;
    }

    public function setPostCount(int $postCount): self
    {
        $this->postCount = $postCount;

        return $this;
    }

    public function getPhotoCount(): ?int
    {
        return $this->photoCount;
    }

    public function setPhotoCount(int $photoCount): self
    {
        $this->photoCount = $photoCount;

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
