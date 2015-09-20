<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 8/25/15
 * Time: 6:23 PM
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Criteria;

class CriteriaController extends Controller
{
    /**
     * @Route("/criteria-list", name="criteria_list")
     */
    public function listAction(Request $request)
    {
        $message = $request->query->get("message");

        return $this->render('criteria/criteria-list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'message' => $message
        ));
    }

    /**
     * @Route("/criteria/form",name="criteria_form")
     */
    public function formAction(Request $request)
    {
        return $this->render('criteria/criteria-form.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'messageError' => null
            ));
    }

    /**
     * @Route("/criteria/add",name="criteria_add")
     */
    public function createAction(Request $request)
    {

        $form = $request->request->get('form');
        if (!ctype_space($form['topic']) && !ctype_space($form['name'])) {

            $this->get('review.service')->addCriteria($form);

            return $this->redirect($this->generateUrl(
                'criteria_list',
                array(
                    'message'=>"Criteria added or updated correctly",
                )
            ));
        }
        else {

            return $this->render('criteria/criteria-form.html.twig',
                array(
                    'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
                    'messageError' => "The form is not valid",
                ));
        }
    }

    /**
     * @Route("/criteria/remove/{criteriaId}",name="criteria_remove")
     */
    public function removeAction(Request $request)
    {
        $criteriaId = $request->attributes->get('criteriaId');

        $this->get('review.service')->removeCriteria($criteriaId);

        return $this->redirect($this->generateUrl(
            'criteria_list',
            array(
                'message'=>"Criteria removed correctly",
            )
        ));
    }

}