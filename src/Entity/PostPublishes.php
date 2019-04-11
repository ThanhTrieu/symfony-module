<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostPublishes
 *
 * @ORM\Table(name="post_publishes")
 * @ORM\Entity(repositoryClass="App\Repository\PostPublishesRepository")
 */
class PostPublishes
{
    /**
     * @var int
     *
     * @ORM\Column(name="post_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $postId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh đại diện bài viết"})
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sapo", type="string", length=255, nullable=true)
     */
    private $sapo;

    /**
     * @var int
     *
     * @ORM\Column(name="cate_id", type="integer", nullable=false, options={"comment"="Primary cate ID"})
     */
    private $cateId = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_date", type="datetime", nullable=false, options={"default"="0000-00-00 00:00:00","comment"="Ngày xuất bản"})
     */
    private $publishedDate = '0000-00-00 00:00:00';

    /**
     * @var int|null
     *
     * @ORM\Column(name="published_timestamp", type="integer", nullable=true, options={"unsigned"=true,"comment"="Ngày xuất bản theo timestamp"})
     */
    private $publishedTimestamp;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true, options={"comment"="Ngày sửa bài cuối cùng"})
     */
    private $modifiedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="author_id", type="integer", nullable=false, options={"comment"="ID Tác giả"})
     */
    private $authorId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author_name", type="string", length=50, nullable=true, options={"comment"="Tên tác giả"})
     */
    private $authorName;

    /**
     * @var int
     *
     * @ORM\Column(name="post_type", type="integer", nullable=false, options={"comment"="0: Bài viết, 1: Trang web"})
     */
    private $postType = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="review_id", type="integer", nullable=false, options={"comment"="Đánh giá bài viết"})
     */
    private $reviewId = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_title", type="string", length=255, nullable=true, options={"comment"="Tiêu đề SEO"})
     */
    private $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_metadesc", type="string", length=500, nullable=true, options={"comment"="Mô tả SEO"})
     */
    private $seoMetadesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_focus_keyword", type="string", length=255, nullable=true, options={"comment"="Keyword chính cho SEO"})
     */
    private $seoFocusKeyword;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_focus_others", type="string", length=1000, nullable=true, options={"comment"="Keywords phụ khác"})
     */
    private $seoFocusOthers;

    /**
     * @var int
     *
     * @ORM\Column(name="focus_status", type="integer", nullable=false, options={"comment"="Trạng thái nổi bật: 0-Tin thường, 1-Nổi bật trang chủ, 2-Nổi bật chuyên mục"})
     */
    private $focusStatus = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="focus_avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh nổi bật"})
     */
    private $focusAvatar;

    /**
     * @var int
     *
     * @ORM\Column(name="view_count", type="integer", nullable=false, options={"comment"="Tổng số lượt xem của bài viết"})
     */
    private $viewCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="like_share_count", type="integer", nullable=false, options={"comment"="Tổng số like + share facebook"})
     */
    private $likeShareCount = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_tags", type="string", length=100, nullable=true, options={"comment"="Tên thiết bị / Hệ điều hành cho Game"})
     */
    private $otherTags;

    /**
     * @var int
     *
     * @ORM\Column(name="review_points", type="integer", nullable=false, options={"comment"="Điểm bài review"})
     */
    private $reviewPoints = '0';

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getSapo(): ?string
    {
        return $this->sapo;
    }

    public function setSapo(?string $sapo): self
    {
        $this->sapo = $sapo;

        return $this;
    }

    public function getCateId(): ?int
    {
        return $this->cateId;
    }

    public function setCateId(int $cateId): self
    {
        $this->cateId = $cateId;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(\DateTimeInterface $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getPublishedTimestamp(): ?int
    {
        return $this->publishedTimestamp;
    }

    public function setPublishedTimestamp(?int $publishedTimestamp): self
    {
        $this->publishedTimestamp = $publishedTimestamp;

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

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function getPostType(): ?int
    {
        return $this->postType;
    }

    public function setPostType(int $postType): self
    {
        $this->postType = $postType;

        return $this;
    }

    public function getReviewId(): ?int
    {
        return $this->reviewId;
    }

    public function setReviewId(int $reviewId): self
    {
        $this->reviewId = $reviewId;

        return $this;
    }

    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): self
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSeoMetadesc(): ?string
    {
        return $this->seoMetadesc;
    }

    public function setSeoMetadesc(?string $seoMetadesc): self
    {
        $this->seoMetadesc = $seoMetadesc;

        return $this;
    }

    public function getSeoFocusKeyword(): ?string
    {
        return $this->seoFocusKeyword;
    }

    public function setSeoFocusKeyword(?string $seoFocusKeyword): self
    {
        $this->seoFocusKeyword = $seoFocusKeyword;

        return $this;
    }

    public function getSeoFocusOthers(): ?string
    {
        return $this->seoFocusOthers;
    }

    public function setSeoFocusOthers(?string $seoFocusOthers): self
    {
        $this->seoFocusOthers = $seoFocusOthers;

        return $this;
    }

    public function getFocusStatus(): ?int
    {
        return $this->focusStatus;
    }

    public function setFocusStatus(int $focusStatus): self
    {
        $this->focusStatus = $focusStatus;

        return $this;
    }

    public function getFocusAvatar(): ?string
    {
        return $this->focusAvatar;
    }

    public function setFocusAvatar(?string $focusAvatar): self
    {
        $this->focusAvatar = $focusAvatar;

        return $this;
    }

    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    public function setViewCount(int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    public function getLikeShareCount(): ?int
    {
        return $this->likeShareCount;
    }

    public function setLikeShareCount(int $likeShareCount): self
    {
        $this->likeShareCount = $likeShareCount;

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

    public function getReviewPoints(): ?int
    {
        return $this->reviewPoints;
    }

    public function setReviewPoints(int $reviewPoints): self
    {
        $this->reviewPoints = $reviewPoints;

        return $this;
    }


}
