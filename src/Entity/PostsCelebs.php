<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PostsCelebs
 *
 * @ORM\Table(name="posts_celebs")
 * @ORM\Entity
 */
class PostsCelebs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="post_id", type="integer", nullable=false)
     */
    private $postId;

    /**
     * @var int
     *
     * @ORM\Column(name="celeb_id", type="integer", nullable=false)
     */
    private $celebId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
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


}
