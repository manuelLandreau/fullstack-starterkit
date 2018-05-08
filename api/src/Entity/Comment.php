<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "post"={"access_control"="is_granted('IS_AUTHENTICATED_FULLY')", "access_control_message"="Only authentified users can add comments."}
 *     },
 *     itemOperations={
 *         "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "put"={"access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.owner == user", "access_control_message"="Sorry, but you are not the comment owner."}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    private $article;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $body;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $author;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank
     */
    private $publicationDate;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param mixed $publicationDate
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;
    }
}
