<?php

namespace AppBundle\Service;

use AppBundle\Entity\Criteria;
use AppBundle\Entity\Review;
use Doctrine\ORM\EntityManager;

class ReviewService
{
    /**
     * @var ReviewManager
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function findAll()
    {
        return $this->em
            ->getRepository('AppBundle:Review')
            ->findAll();
    }

    public function addOne($review)
    {
        $this->analyzeOne($review);

        $this->em->persist($review);
        $this->em->flush();

        return $review;

    }

    public function removeReview($reviewId)
    {
        $review = $this->em
            ->getRepository('AppBundle:Review')
            ->find($reviewId);

        if($review != null) {
            $this->em->remove($review);
            $this->em->flush();
        }
    }

    /**
     * Method to analyze one review
     * Return $review with the score updated
     * @param $review
     * @return $mixed
     */
    public function analyzeOne($review)
    {
        $criteriaList = $this->em
            ->getRepository('AppBundle:Criteria')
            ->findBy(
                array(),
                array('name' => 'ASC')
            );

        $text = preg_replace("/\n+|\"+|\/+|\(+|\)+/"," ", $review->getContent());

        //fusion negative words
        $text = preg_replace("/not /","not",$text);
        $text = preg_replace("/\'t /","'t",$text);

        $text = strtoupper($text);

        $text = preg_split("/ +/", $text);

        $score = array();
        $total = 0;
        foreach($text as $word) {
            $found = $this->findWord($criteriaList,0, count($criteriaList) - 1, $word);
            if($found != null) //If we want no repeat criteria evaluations && !in_array($found, $score))
            {
                if(!in_array($found, $score))
                {
                    $score[] = $found;
                }
                if($found->getType() == 'POSITIVE') {
                    $total++;
                } else if($found->getType() == 'NEGATIVE') {
                    $total--;
                }
            }
        }
        $review->setScore($score);
        $review->setTotal($total);

        return $review;
    }

    /**
     * Method to set score for each review List
     * @return reviewList
     */
    public function runAnalyze() {
        $reviewList = $this->em
            ->getRepository('AppBundle:Review')
            ->findAll();

        foreach($reviewList as $review) {
            $review = $this->analyzeOne($review);

            $this->em->persist($review);
            $this->em->flush();
        }

        return $reviewList;
    }

    /**
     * Method to import a review List
     * Add imported added to the previous list
     * @return reviewList
     */
    public function importReviewList($file)
    {
        $csv = $file->getData();

        $ignoreFirstLine = true;
        $rows = array();
        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $i++;
                if ($ignoreFirstLine && $i == 1) {
                    continue;
                }
                $rows[] = $data;
            }
            fclose($handle);
        }

        $reviewList = array();

        foreach ($rows as $row)
        {
            // Update if exists
            $review = $this->em
            ->getRepository('AppBundle:Review')
            ->find($row[1]);

            if(!$review)
            {
                $review = new Review();
                $review->setId(intval($row[1]));
            }
            $review->setContent($row[2]);

            $this->analyzeOne($review);

            $this->em->persist($review);

            // getMetadataFactory()->getMetadataFor('className','class') don't work
            $metadata = $this->em->getClassMetadata(get_class($review));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $this->em->flush();

            $reviewList[] = $review;
        }

        return $reviewList;
    }

    /**
     * Recursive Method to find a word in a list
     * @param $list
     * @param $ini
     * @param $end
     * @param $word
     * @return null if not found
     */
    public function findWord($list, $ini, $end, $word)
    {
        if($ini <= $end) {
            $m = round((($ini + $end)/ 2), 0, PHP_ROUND_HALF_DOWN);

            $name = strtoupper(preg_replace('/\s+/', '', $list[$m]->getName()));

            if(preg_match("/\b$name/i",$word) || preg_match("/\b$word/i",$name)) {
                $res = $list[$m];
            }
            else if(strcmp($word,$name) < 0) {
                $res = $this->findWord($list, $ini, $m - 1, $word);
            } else if(strcmp($word,$name) > 0) {
                $res = $this->findWord($list, $m + 1, $end, $word);
            }
        } else {
            $res = null;
        }
        return $res;
    }

    /*****************************************************************
     *
     *                      Criteria function
     *
     * ***************************************************************/


    /**
     * Add Criteria
     * @param $form
     * @return Criteria
     */
    public function addCriteria($form) {



        $criteriaList = $this->em
            ->getRepository('AppBundle:Criteria')
            ->findByName($form['name']);

        if(!$criteriaList) {

            $criteria = new Criteria();
        } else {
            $criteria = $criteriaList[0];
        }
        $criteria->setName($form['name']);
        $criteria->setType($form['type']);

        if ($form['type'] == 'ALTER_NAME') {
            $criteria->setTopic($form['topic']);
        }

        $this->em->persist($criteria);
        $this->em->flush();

        return $criteria;

    }

    /**
     * Remove Criteria
     * @param $criteriaId
     */
    public function removeCriteria($criteriaId)
    {
        $criteria = $this->em
            ->getRepository('AppBundle:Criteria')
            ->find($criteriaId);

        if($criteria != null) {
            $this->em->remove($criteria);
            $this->em->flush();
        }
    }

}