<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "post"={"access_control"="is_granted('IS_AUTHENTICATED_FULLY')", "access_control_message"="Only authentified users can add articles."}
 *     },
 *     itemOperations={
 *         "get"={"access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"},
 *         "put"={"access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.owner == user", "access_control_message"="Sorry, but you are not the article owner."}
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;

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

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="article")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments[] = $comments;
    }
}
