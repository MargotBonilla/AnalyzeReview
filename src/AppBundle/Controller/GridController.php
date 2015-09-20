<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Review;

class GridController extends Controller
{
    /**
     * @Route("/grid")
     * @Template()
     */
    public function gridAction() {
        return array();
    }

    /**
     * @Route("/review/grid", name="_grid_content_review")
     */
    public function reviewGridAction() {

        $reviewList = $this->getDoctrine()

            ->getRepository('AppBundle:Review')
            ->findAll();

        $reviewArray = array();
        for($i = 0; $i < count($reviewList); $i++)
        {
            $scoreArray = array();

            $topic = "";
            foreach($reviewList[$i]->getScore() as $score)
            {
                if($score->getTopic() != null) {
                    $topic = $score->getTopic();
                } else {
                    $scoreArray[] = $topic . " " . $score->getName();
                }
            }

            $reviewArray[] = array('id'=>$i, 'cell'=>array(
                $reviewList[$i]->getId(),
                $reviewList[$i]->getContent(),
                $scoreArray,
                $reviewList[$i]->getTotal(),
            ));

        }

        $contentJqGrid = array(
            'total'=>1,
            'page'=>1,
            'records'=>1,
            'rows'=>$reviewArray
        );
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($contentJqGrid)
            , 200
            , array('content-type'=>'application/json')
        );
    }

    /**
     * @Route("/criteria/grid", name="_grid_content_criteria")
     */
    public function criteriaGridAction() {

        $criteriaList = $this->getDoctrine()

            ->getRepository('AppBundle:Criteria')
            ->findAll();

        $criteriaArray = array();
        for($i = 0; $i < count($criteriaList); $i++)
        {
            $scoreArray = array();

            $criteriaArray[] = array('id'=>$i, 'cell'=>array(
                $criteriaList[$i]->getId(),
                $criteriaList[$i]->getTopic(),
                $criteriaList[$i]->getName(),
                $criteriaList[$i]->getType(),
            ));

        }

        $contentJqGrid = array(
            'total'=>1,
            'page'=>1,
            'records'=>1,
            'rows'=>$criteriaArray
        );
        return new \Symfony\Component\HttpFoundation\Response(
            json_encode($contentJqGrid)
            , 200
            , array('content-type'=>'application/json')
        );
    }

}