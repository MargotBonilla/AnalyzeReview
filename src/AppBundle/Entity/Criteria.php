<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 8/25/15
 * Time: 5:58 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Criteria
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CriteriaRepository")
 * @ORM\Table(name="criteria")
 */
class Criteria
{
    const TYPE_ALTER_NAME = "ALTER_NAME";
    const TYPE_POSITIVE = "POSITIVE";
    const TYPE_NEGATIVE = "NEGATIVE";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\NotBlank()
     */
    protected $topic;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('ALTER_NAME','POSITIVE','NEGATIVE')")
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Review", mappedBy="score")
     */
    protected $reviewList;

    /**
     * Criteria constructor.
     */
    public function __construct()
    {
        $this->reviewList = new ArrayCollection();
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
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getReviewList()
    {
        return $this->reviewList;
    }

    /**
     * @param mixed $reviewList
     */
    public function setReviewList($reviewList)
    {
        $this->reviewList = $reviewList;
    }

    /**
     * Add reviewList
     *
     * @param \AppBundle\Entity\Review $reviewList
     * @return Criteria
     */
    public function addReviewList(\AppBundle\Entity\Review $reviewList)
    {
        $this->reviewList[] = $reviewList;

        return $this;
    }

    /**
     * Remove reviewList
     *
     * @param \AppBundle\Entity\Review $reviewList
     */
    public function removeReviewList(\AppBundle\Entity\Review $reviewList)
    {
        $this->reviewList->removeElement($reviewList);
    }
}
