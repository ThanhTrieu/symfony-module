<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GroupBoxItems
 *
 * @ORM\Table(name="group_box_items")
 * @ORM\Entity(repositoryClass="App\Repository\GroupBoxItemsRepository")
 */
class GroupBoxItems
{
    /**
     * @var int
     *
     * @ORM\Column(name="box_item_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $boxItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="box_id", type="integer", nullable=false, options={"comment"="Thuộc box"})
     */
    private $boxId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="key", type="integer", nullable=true, options={"comment"="Khóa hiển thị FrontEnd"})
     */
    private $key;

    /**
     * @var int|null
     *
     * @ORM\Column(name="data_id", type="integer", nullable=true, options={"comment"="Id của post hoặc tag tùy thuộc vào type"})
     */
    private $dataId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"comment"="Tiêu đề item box"})
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link_url", type="string", length=255, nullable=true, options={"comment"="Link item box"})
     */
    private $linkUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh item box"})
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sapo", type="string", length=500, nullable=true, options={"comment"="Mô tả item box"})
     */
    private $sapo;

    /**
     * @var int
     *
     * @ORM\Column(name="order", type="integer", nullable=false, options={"comment"="Thứ tự item box"})
     */
    private $order = '0';

    public function getBoxItemId(): ?int
    {
        return $this->boxItemId;
    }

    public function getBoxId(): ?int
    {
        return $this->boxId;
    }

    public function setBoxId(int $boxId): self
    {
        $this->boxId = $boxId;

        return $this;
    }

    public function getKey(): ?int
    {
        return $this->key;
    }

    public function setKey(?int $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getDataId(): ?int
    {
        return $this->dataId;
    }

    public function setDataId(?int $dataId): self
    {
        $this->dataId = $dataId;

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

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(?string $linkUrl): self
    {
        $this->linkUrl = $linkUrl;

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

    public function getSapo(): ?string
    {
        return $this->sapo;
    }

    public function setSapo(?string $sapo): self
    {
        $this->sapo = $sapo;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }


}
