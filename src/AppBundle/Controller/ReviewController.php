<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 8/25/15
 * Time: 6:23 PM
 */

namespace AppBundle\Controller;


use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Review;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewController extends Controller
{
    /**
     * @Route("/", name="review_list")
     */
    public function indexAction(Request $request)
    {
        $message = $request->query->get("message");

        $reviewService = $this->get('review.service');
        $reviewList = $reviewService->findAll();

        return $this->render('review/review-list.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'reviewList' => $reviewList,
                'message'=> $message)
        );
    }

    /**
     * @Route("/review/add",name="review_add")
     * @param Request $request
     * @return review list with message
     */
    public function createAction(Request $request)
    {

        $review = new Review();

        $form = $this->createFormBuilder($review)
            ->add('content', 'textarea', array(
                'attr' => array('rows' => '5'),
                'constraints' => array(
                        new NotBlank(array('groups' => array('create', 'update'))),
                    ),
                ))
            ->add('save', 'submit', array(
                'label' => 'Create Review',
                'attr'  => array('class' => 'btn btn-success pull-left')
                ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $reviewService = $this->get('review.service');
            $reviewService->addOne($review);

            return $this->redirect(
                $this->generateUrl('review_list',
                array('message' => "The review added correctly")
            ));
        }


        return $this->render('review/review-form.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/review/remove/{reviewId}",name="review_remove")
     */
    public function removeAction(Request $request)
    {
        $reviewId = $request->attributes->get('reviewId');

        $reviewService = $this->get('review.service');
        $reviewService->removeReview($reviewId);

        return $this->redirect(
            $this->generateUrl('review_list',
            array('message' => "The review removed correctly")
            ));

    }

    /**
     * @Route("/review/import",name="review_import")
     */
    public function importAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('submitFile', 'file', array('label' => 'File to Submit'))
            ->getForm();

        if ($request->getMethod('post') == 'POST') {
            // Bind request to the form
            $form->submit($request);

            // If form is valid
            if ($form->isValid()) {
                // Get file
                $file = $form->get('submitFile');

                try {
                    $reviewService = $this->get('review.service');
                    $reviewList = $reviewService->importReviewList($file);

                    return $this->redirect(
                        $this->generateUrl('review_list',
                        array('message' => "The review list imported correctly")
                        ));

                } catch(ORMException $e) {
                    $this->get('session')->getFlashBag()->add('error', 'The import file has not been uploaded successfully');
                    $this->get('logger')->error($e->getMessage());

                    return $this->redirect($request->request->headers->get('referer'));
                }
            }
        }
            return $this->render('review/review-import.html.twig',
                array(
                    'form' => $form->createView(),
                ));

        }

    /**
     * @Route("/review/run",name="review_run")
     * @param Request $request
     * @return review list analyzed
     */
    public function runAction(Request $request)
    {
        $reviewService = $this->get('review.service');
        $reviewService->runAnalyze();

        return $this->redirect(
            $this->generateUrl('review_list',
            array('message' => "The review list has been analyzed")
            ));
    }
}