<?php

/**
 * WebsiteFee Module
 *
 * The WebsiteFee module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  administration display functions --
 *                   This file contains all administrative GUI functions
 *                   for the module
 *
 * @package      Paustian
 * @subpackage   WebsiteFee
 * @version      2.0 
 * @author       Timothy Paustian
 * @link            http://www.microbiologytext.com
 * @copyright    Copyright (c) 2016 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Paustian\WebsiteFeeModule\Controller;

use Zikula\Core\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Symfony\Component\Routing\RouterInterface;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity;
use SecurityUtil;
use Paustian\WebsiteFeeModule\Form\SubscriberForm;

/**
 * @Route("/admin")
 *
 * Administrative controllers for the quickcheck module
 */
class AdminController extends AbstractController {

    /**
     * @Route("")
     * 
     * show a list of all the transactions
     *
     * @author       Timothy Paustian
     * @return       output       The main module admin page.
     */
    public function indexAction() {
        // Security check
        if (!$this->hasPermission('WebsiteFee::', '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        return $this->render('PaustianWebsiteFeeModule:Admin:websitefee_admin_index.html.twig');
    }

    /**
     * @Route("/edit/{subscriber}")
     * 
     * form to add new item
     *
     * create a new subscriber account
     * to the viewer
     *
     * @author       Timothy Paustian
     * @return       output       The main module admin page.
     */
    public function editAction(Request $request, WebsiteFeeSubsEntity $subscriber = null) {
        // Security check - important to do this as early as possible to avoid
        // potential security holes or just too much wasted processing
        if (!$this->hasPermission('WebsiteFee::', '::', ACCESS_ADD)) {
            throw new AccessDeniedException();
        }

        //If the $subscriber already exists coming in, then we want to merge
        //if it doesn't we need to persist it instead.
        $doMerge = false;
        if (null === $subscriber) {
            $subscriber = new WebsiteFeeSubsEntity();
        } else {
            $doMerge = true;
        }
        //I need to add the use declaration for this class. 
        $form = $this->createForm(new SubscriberForm(), $subscriber);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($doMerge) {
                $em->merge($subscriber);
            } else {
                $em->persist($subscriber);
            }
            $em->flush();
            $this->addFlash('status', _('Subscription saved.'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_edit'));
            return $response;
        }

        return $this->render('PaustianWebsiteFeeModule:Admin:websitefee_admin_edit.html.twig', array(
                    'form' => $form->createView()));
    }

    /**
     * @Route ("/modify")
     * 
     * modify a subscription
     *
     * Set up a form to present all the exams and let the user choose
     * The one to modify
     */
    public function modifyAction(Request $request) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        // create a QueryBuilder instance
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

        // add select and from params
        $qb->select('u')
                ->from('PaustianWebsiteFeeModule:WebsiteFeeSubsEntity', 'u');
        // convert querybuilder instance into a Query object
        $query = $qb->getQuery();

        // execute query
        $subs = $query->getResult();
        if (!$subs) {
            $this->addFlash('error', _('There are no subscriptions to edit'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_index'));
            return $response;
        }
        return $this->render('PaustianWebsiteFeeModule:Admin:websitefee_admin_modify.html.twig', ['subs' => $subs]);
    }

    /**
     * @Route ("/modifytrans")
     * 
     * modify a subscription
     *
     * Set up a form to present all the exams and let the user choose
     * The one to modify
     */
    public function modifytransAction(Request $request) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        // create a QueryBuilder instance
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

        // add select and from params
        $qb->select('u')
                ->from('PaustianWebsiteFeeModule:WebsiteFeeTransEntity', 'u');
        // convert querybuilder instance into a Query object
        $query = $qb->getQuery();

        // execute query
        $trans = $query->getResult();
        if (!$trans) {
            $this->addFlash('error', _('There are no transactions to delete'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_index'));
            return $response;
        }
        return $this->render('PaustianWebsiteFeeModule:Admin:websitefee_admin_modifytrans.html.twig', ['trans' => $trans]);
    }

    /**
     * @Route ("/modifyerrs")
     * 
     * modify a subscription
     *
     * Set up a form to present all the exams and let the user choose
     * The one to modify
     */
    public function modifyerrsAction(Request $request) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_EDIT)) {
            throw new AccessDeniedException();
        }
        // create a QueryBuilder instance
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

        // add select and from params
        $qb->select('u')
                ->from('PaustianWebsiteFeeModule:WebsiteFeeErrorsEntity', 'u');
        // convert querybuilder instance into a Query object
        $query = $qb->getQuery();

        // execute query
        $errors = $query->getResult();
        if (!$errors) {
            $this->addFlash('error', _('There are no errors to delete'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_index'));
            return $response;
        }
        return $this->render('PaustianWebsiteFeeModule:Admin:websitefee_admin_modifyerrs.html.twig', ['errors' => $errors]);
    }

    /**
     * @Route("delete/{subscriber}")
     *
     * Delete a subscription.
     *
     * @author       Timothy Paustian
     * @param        subscriber           the item to be deleted of the item to be modified
     * @param        confirmation   confirmation that this item can be deleted
     */
    public function deleteAction(Request $request, WebsiteFeeSubsEntity $subscriber) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_DELETE)) {
            throw new AccessDeniedException();
        }
        //This code should never be reached, but I added it anyway, just in case.
        if (null === $subscriber) {
            $this->addFlash('error', _('That subscription does not exist'));
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscriber);
            $em->flush();
            $this->addFlash('status', _('Subscription Deleted'));
        }
        $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_modify'));
        return $response;
    }

    /**
     * @Route("deletetrans/{transaction}")
     * 
     * Delete accumulated transaction if you want. We do not add these, They are added by communication with
     * PayPal, but we can delete them.
     * 
     * @param Request $request
     * @param WebsiteFeeTransEntity $transaction
     * @return type
     */
    public function deletetransAction(Request $request, WebsiteFeeTransEntity $transaction = null) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_DELETE)) {
            throw new AccessDeniedException();
        }
        //This code should never be reached, but I added it anyway, just in case.
        $em = $this->getDoctrine()->getManager();
        if (null === $transaction) {
            //make sure we get here from someone hitting the delete all button
            $deleteAll = $request->get('deleteAll');
            if ($deleteAll === 'deleteAll') {
                //remove all the items. I am using the small memory footprint way
                $batchSize = 20;
                $i = 1;
                $qb = $em->createQuery('select u from Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity u');
                $iterableResult = $qb->iterate();
                while (($row = $iterableResult->next()) !== false) {
                    $em->remove($row[0]);
                    if (($i % $batchSize) === 0) {
                        $em->flush(); // Executes all deletions.
                        $em->clear(); // Detaches all objects from Doctrine!
                    }
                    ++$i;
                }
                $em->flush();
                $this->addFlash('status', _('All Transactions Deleted'));
                $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_index'));
            }
        } else {
            $em->remove($transaction);
            $em->flush();
            $this->addFlash('status', _('Transaction Deleted'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_modifytrans'));
        }
        return $response;
    }

    /**
     * @Route("deleteerror/{error}")
     * 
     * @param Request $request
     * @param WebsiteFeeErrorsEntity $error
     */
    public function deleteerrorAction(Request $request, WebsiteFeeErrorsEntity $error = null) {
        if (!$this->hasPermission('WebsiteFee::', "::", ACCESS_DELETE)) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $response = null;
        //This code should never be reached, but I added it anyway, just in case.
        if (null === $error) {
            //make sure we get here from someone hitting the delete all button
            $deleteAll = $request->get('deleteAll');
            if ($deleteAll === 'deleteAll') {
                //remove all the items. I am using the small memory footprint way
                $batchSize = 20;
                $i = 1;
                $qb = $em->createQuery('select u from Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity u');
                $iterableResult = $qb->iterate();
                while (($row = $iterableResult->next()) !== false) {
                    $em->remove($row[0]);
                    if (($i % $batchSize) === 0) {
                        $em->flush(); // Executes all deletions.
                        $em->clear(); // Detaches all objects from Doctrine!
                    }
                    ++$i;
                }
                $em->flush();
                $this->addFlash('status', _('All Error Deleted'));
                $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_index'));
            }
        } else {
            $em->remove($error);
            $em->flush();
            $this->addFlash('status', _('Error Deleted'));
            $response = $this->redirect($this->generateUrl('paustianwebsitefeemodule_admin_modifyerrs'));
        }
        
        return $response;
    }

}

