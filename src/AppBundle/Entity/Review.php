<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 8/25/15
 * Time: 5:44 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Review
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReviewRepository")
 * @ORM\Table(name="review")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $content;

    /**
     * @ORM\ManyToMany(targetEntity="Criteria", inversedBy="reviewList")
     * @ORM\JoinTable(name="review_has_criteria")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $score;

    /**
     * @ORM\Column(type="integer")
     */
    protected $total = 0;

    /**
     * Review constructor.
     */
    public function __construct()
    {
        $this->score = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }


    /**
     * Add score
     *
     * @param \AppBundle\Entity\Criteria $score
     * @return Review
     */
    public function addScore(\AppBundle\Entity\Criteria $score)
    {
        $this->score[] = $score;

        return $this;
    }

    /**
     * Remove score
     *
     * @param \AppBundle\Entity\Criteria $score
     */
    public function removeScore(\AppBundle\Entity\Criteria $score)
    {
        $this->score->removeElement($score);
    }
}
