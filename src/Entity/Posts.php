<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Posts
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="App\Repository\PostsRepository")
 */
class Posts
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
     * @ORM\Column(name="sapo", type="string", length=255, nullable=true, options={"comment"="Mô tả ngắn"})
     */
    private $sapo;

    /**
     * @var int
     *
     * @ORM\Column(name="cate_id", type="integer", nullable=false, options={"comment"="Primary cate ID"})
     */
    private $cateId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=false, options={"comment"="Người tạo bài"})
     */
    private $creatorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false, options={"comment"="Ngày tạo bài"})
     */
    private $createdDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="modifier_id", type="integer", nullable=true, options={"comment"="Ngưới sửa bài"})
     */
    private $modifierId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modified_date", type="datetime", nullable=true, options={"comment"="Ngày sửa bài"})
     */
    private $modifiedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="publisher_id", type="integer", nullable=false, options={"comment"="Người xuất bản bài viết"})
     */
    private $publisherId = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="published_date", type="datetime", nullable=true, options={"comment"="Ngày xuất bản"})
     */
    private $publishedDate;

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
     * @ORM\Column(name="avatar_image_id", type="integer", nullable=false, options={"comment"="ID ảnh đại diện"})
     */
    private $avatarImageId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="review_id", type="integer", nullable=false, options={"comment"="Review ID"})
     */
    private $reviewId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false, options={"comment"="Page parent ID"})
     */
    private $parentId = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="notify_status", type="integer", nullable=false, options={"comment"="0: Không gửi notify, 1: Cho phép Push notification, 2: Đã gửi notification"})
     */
    private $notifyStatus = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, options={"comment"="0: Draft, 1: Pending, 2: Published, 3: Trash"})
     */
    private $status = '0';

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
     * @var int|null
     *
     * @ORM\Column(name="seo_keyword_score", type="integer", nullable=true, options={"comment"="Điểm keyword - SEO score"})
     */
    private $seoKeywordScore;

    /**
     * @var int|null
     *
     * @ORM\Column(name="seo_content_score", type="integer", nullable=true, options={"comment"="Điểm content - Readability score"})
     */
    private $seoContentScore;

    /**
     * @var int
     *
     * @ORM\Column(name="view_count", type="integer", nullable=false, options={"comment"="Tổng số view của bài viết"})
     */
    private $viewCount = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="word_count", type="integer", nullable=true, options={"comment"="Số từ trong nội dung"})
     */
    private $wordCount = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="src_link", type="string", length=255, nullable=true, options={"comment"="Link bài gốc"})
     */
    private $srcLink;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="time_expired", type="datetime", nullable=true, options={"comment"="Thời hạn biên tập bài"})
     */
    private $timeExpired;

    /**
     * @var int|null
     *
     * @ORM\Column(name="focus_status", type="integer", nullable=true, options={"comment"="Trạng thái nổi bật: 0-Tin thường, 1-Nổi bật trang chủ, 2-Nổi bật chuyên mục"})
     */
    private $focusStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="focus_avatar", type="string", length=255, nullable=true, options={"comment"="Ảnh nổi bật"})
     */
    private $focusAvatar;

    /**
     * @var int|null
     *
     * @ORM\Column(name="point_value", type="integer", nullable=true, options={"comment"="Điểm cho BTV: 0:A, 1:B, 2:C, 3:O"})
     */
    private $pointValue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="point_note", type="string", length=500, nullable=true, options={"comment"="Note cho phần chấm điểm"})
     */
    private $pointNote;

    /**
     * @var int|null
     *
     * @ORM\Column(name="point_creator_id", type="integer", nullable=true, options={"comment"="Người chấm điểm"})
     */
    private $pointCreatorId;

    /**
     * @var int
     *
     * @ORM\Column(name="fb_like_count", type="integer", nullable=false, options={"comment"="Tổng số like facebook"})
     */
    private $fbLikeCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="fb_share_count", type="integer", nullable=false, options={"comment"="Tổng số share facebook"})
     */
    private $fbShareCount = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="review_points", type="integer", nullable=false, options={"comment"="Điểm bài review"})
     */
    private $reviewPoints = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_tags", type="string", length=100, nullable=true, options={"comment"="Tên thiết bị / Hệ điều hành cho Game"})
     */
    private $otherTags;

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

    public function getModifierId(): ?int
    {
        return $this->modifierId;
    }

    public function setModifierId(?int $modifierId): self
    {
        $this->modifierId = $modifierId;

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

    public function getPublisherId(): ?int
    {
        return $this->publisherId;
    }

    public function setPublisherId(int $publisherId): self
    {
        $this->publisherId = $publisherId;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTimeInterface $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

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

    public function getAvatarImageId(): ?int
    {
        return $this->avatarImageId;
    }

    public function setAvatarImageId(int $avatarImageId): self
    {
        $this->avatarImageId = $avatarImageId;

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

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getNotifyStatus(): ?int
    {
        return $this->notifyStatus;
    }

    public function setNotifyStatus(int $notifyStatus): self
    {
        $this->notifyStatus = $notifyStatus;

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

    public function getSeoKeywordScore(): ?int
    {
        return $this->seoKeywordScore;
    }

    public function setSeoKeywordScore(?int $seoKeywordScore): self
    {
        $this->seoKeywordScore = $seoKeywordScore;

        return $this;
    }

    public function getSeoContentScore(): ?int
    {
        return $this->seoContentScore;
    }

    public function setSeoContentScore(?int $seoContentScore): self
    {
        $this->seoContentScore = $seoContentScore;

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

    public function getWordCount(): ?int
    {
        return $this->wordCount;
    }

    public function setWordCount(?int $wordCount): self
    {
        $this->wordCount = $wordCount;

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

    public function getTimeExpired(): ?\DateTimeInterface
    {
        return $this->timeExpired;
    }

    public function setTimeExpired(?\DateTimeInterface $timeExpired): self
    {
        $this->timeExpired = $timeExpired;

        return $this;
    }

    public function getFocusStatus(): ?int
    {
        return $this->focusStatus;
    }

    public function setFocusStatus(?int $focusStatus): self
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

    public function getPointValue(): ?int
    {
        return $this->pointValue;
    }

    public function setPointValue(?int $pointValue): self
    {
        $this->pointValue = $pointValue;

        return $this;
    }

    public function getPointNote(): ?string
    {
        return $this->pointNote;
    }

    public function setPointNote(?string $pointNote): self
    {
        $this->pointNote = $pointNote;

        return $this;
    }

    public function getPointCreatorId(): ?int
    {
        return $this->pointCreatorId;
    }

    public function setPointCreatorId(?int $pointCreatorId): self
    {
        $this->pointCreatorId = $pointCreatorId;

        return $this;
    }

    public function getFbLikeCount(): ?int
    {
        return $this->fbLikeCount;
    }

    public function setFbLikeCount(int $fbLikeCount): self
    {
        $this->fbLikeCount = $fbLikeCount;

        return $this;
    }

    public function getFbShareCount(): ?int
    {
        return $this->fbShareCount;
    }

    public function setFbShareCount(int $fbShareCount): self
    {
        $this->fbShareCount = $fbShareCount;

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
