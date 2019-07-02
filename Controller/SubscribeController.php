<?php

/**
 * WebsiteFeeModule Module
 *
 * The WebsiteFeeModule module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  user display functions --
 *                   This file contains all user GUI functions for the module
 *
 * @package      Paustian
 * @subpackage   WebsiteFeeModule
 * @version      2.0
 * @author       Timothy Paustian
 * @author       Timothy Paustian
 * @link         http://www.microbiologytext.com
 * @copyright    Copyright (c) 2019 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Paustian\WebsiteFeeModule\Controller;

use Zikula\Core\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // used in annotations - do not remove
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method; // used in annotations - do not remove
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity;
use SecurityUtil;
use Paustian\WebsiteFeeModule\Api\IpnListener;
use UserUtil;
use ModUtil;
use DateTime;
use Exception;
use System;

/**
 * This controller is for managing subscriptions with paypal. There is no admin or user interaction with
 * it and you don't really need to change the code at all.
 */
class SubscribeController extends AbstractController {

    private $paymentDate;
    private $response;
    private $request;
    private $listener;
    private $debug;
    /**
     * @Route("")
     *
     *
     * Params: none
     * Returns: nothing
     */
    public function indexAction() {
        //securtiy check first
        if (!$this->hasPermission('quickcheck::', '::', ACCESS_OVERVIEW)) {
            throw new AccessDeniedException();
        }

        return $this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_index.html.twig');
    }

    /**
     * @Route("/testsubscribe")
     *
     *
     * This function is for debugging the subscription.
     *
     * @author       Timothy Paustian
     * @return       output       The main module page
     */
    public function testsubscribeAction(Request $request) {
        if($this->debug){
            return $this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_testsubscribe.html.twig');
        }
        return new Response($this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_index.html.twig'));
    }

    /**
     * @Route("/testupdategroup")
     */
    public function testupdategroupAction(Request $request) {
        //This is a test function to try to debug update group
        if($this->debug){
            $this->_updateGroup('3', '3');
        }
        return $this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_index.html.twig');
    }

    /**
     * @Route("/subscribepaypal")
     *
     * This it the routine that actually communicates with PayPal and manages the subscriptions
     * @param Request $request
     */
    public function subscribepaypalAction(Request $request) {
        $this->listener = new IpnListener();
        $this->debug = false;
        $this->paymentDate = urldecode($request->get('payment_date'));
        $paymentDateTmp = strtotime($this->paymentDate);
        $this->paymentDate = new DateTime(strftime('%Y-%m-%d %H:%M:%S', $paymentDateTmp));
        try {
            $this->listener->use_sandbox = true;
            $this->listener->debug = false;
            $verified = $this->listener->processIpn();
        } catch (Exception $e) {
            $this->response = $this->listener->getResponse();
            //if we get an exception in the IpnListener, I want to know what was posted.
            $this->request = $_POST;
            $this->_set_error($e->getMessage());
            exit(0);
        }
        if ($verified) {
            //note you can quickly get the user id using
            //SessionUtil::getVar('uid')
            $uid = $request->get('custom');
            $txn_id = $request->get('txn_id');
            $reciever_email = urldecode($request->get('receiver_email'));
            $item_no = $request->get('item_number');
            $txn_type = $request->get('txn_type');
            $payment_gross = $request->get('mc_gross');
            $payment_status = $request->get('payment_status');
            $payer_email = urldecode($request->get('payer_email'));
            $this->response = $this->listener->getResponse();
            $this->request = $this->listener->getPostUri();
            //enter transcaction makes sure it has all the information that we need
            if ($this->_enterTransaction($uid, $txn_id, $payer_email, $reciever_email, $payment_gross, $item_no, $txn_type)) {
                if ($txn_type === 'subscr_cancel') {
                    $this->_cancelSubscription($uid, $item_no);
                } else if (($txn_type === 'subscr_payment' && $payment_status === 'Completed') || ($txn_type == 'web_accept')) {
                    //Paypal sends 3 message upon purchase. We only want to add the subscription
                    //when the transaction is completed.
                    $this->_addSubscription($uid, $item_no);
                }
            }
        } else {
            //we have an invalid transaction, record it.
            $this->response = $this->listener->getResponse();
            $this->request = $_POST;
            $this->_set_error("Transaction not verified");
        }
        if($this->debug){
            return $this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_testsubscribe.html.twig');
        }
    }

    private function _cancelSubscription($uid, $item_no) {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();

        if (!$this->_modifyUser($gid, $uid, false, $e)) {
            //write an error to the error log;
            $this->_set_error("Unable to cancel subscription: $e");
        }
    }

    private function _addSubscription($uid, $item_no) {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();
        if (!$this->_modifyUser($gid, $uid, true, $e)) {
            //write an error to the error log;
            $this->_set_error("Unable to add subscription: $e");
        }
    }

    /**
     * This is a hack that goes right into the entities for the Group/User module.
     * I was getting permission errors by trying to use the api, Hopefully this plays.
     * @return boolean
     * @throws \InvalidArgumentException
     * @throws AccessDeniedException
     */
    private function _modifyUser($gid, $uid, $add = true, &$error="") {
        // Argument check
        if ((!isset($gid)) || (!isset($uid))) {
            throw new \InvalidArgumentException(__('Invalid arguments array received'));
        }
        $em = $this->getDoctrine()->getManager();

        // get group
        $group = $em->find('ZikulaGroupsModule:GroupEntity', $gid);

        if (!$group) {
            return false;
        }

        $user = $em->find('ZikulaUsersModule:UserEntity', $uid);
        if (!$user) {
            return false;
        }

        try {
            // Add user to group
            if ($add) {
                $groups = $user->getGroups();
                if(!$groups->contains($group)){
                    $user->addGroup($group);
                }
            } else {
                $user->removeGroup($group);
            }
            $em->flush();
        } catch (\Exception $e) {

            return false;
        }
        // Let the calling process know that we have finished successfully
        return true;
    }

    /**
     * Set an error in the database. We are breaking convention to make this a private function
     * there should be no way to call this from outside.
     *
     *  set_error
     *
     * @param $line - A line explaining why an error was posted.
     *
     *
     */
    private function _set_error($line) {
        $error = new WebsiteFeeErrorsEntity();
        $error->setWsferrdate($this->paymentDate);
        $error->setWsferroeexp($line);
        $error->setWsfrequest($this->request);
        $error->setWsfrespone($this->response);
        $em = $this->getDoctrine()->getManager();
        if(!$em->isOpen()){
            $em = $this->getDoctrine()->resetManager();
        }
        $em->persist($error);
        $em->flush();
    }

    private function _enterTransaction($uid, $txn_id, $payer_email, $receiver_email, $payment_gross, $item_number, $subscr_type) {

        // Argument check - make sure that all required arguments are present,
        // if not then set an appropriate error message and return
        if ((!isset($uid)) || (!isset($payer_email)) ||
            (!isset($txn_id))) {
            throw new NotFoundHttpException($this->__('Variable error in _enter_transaction'));
            ;
        }

        //see if we can find an item with the same txn_id in the
        //database. If we can this is a problem.
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u')
            ->from('PaustianWebsiteFeeModule:WebsiteFeeTransEntity', 'u');
        $qb->where('u.wsftxid = ?1');
        $qb->setParameter(1, $txn_id);
        $query = $qb->getQuery();
        // execute query
        $dup_trans = $query->getResult();

        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if (!empty($dup_trans)) {
            //if we find it, someone is trying to spoof us
            //log this as an error and then return false
            $this->_set_error("Duplicate transaction ID:" . $txn_id);
            return false;
        }

        //now grab the data out of the subsciption table
        $subscript_info = $this->_get_sub($item_number);
        if ($subscript_info == null) {
            $this->_set_error("No item for the item number. Check your subscription setup to insure that your Subscription Item Number matches what you are putting in your paypal button");
            return false;
        }
        $email = $subscript_info->getWsfemail();
        // check that receiver_email is your Primary PayPal email
        if ($receiver_email !== $email) {
            //not the correct email, again log as a spoof.
            $this->_set_error("Incorrect reciever Email:" . $receiver_email . ", correct Email should be:" . $email);
            return false;
        }

        $payment_amt = $subscript_info->getWsfpaymentamount();
        //I added a range because Paypal was being cute and adding tax
        if (($payment_gross == -1) || ((($payment_amt - 2) < $payment_gross) && (($payment_amt + 2) > $payment_gross))) {
            $payment_gross = $payment_amt;
        }

        // check that payment_amount/payment_currency are correct
        if ($payment_gross != $payment_amt) {
            //wrong amount payed
            $this->_set_error("payment inccorect: " . $payment_gross . ", correct amount should be: " . $payment_amt);
            return false;
        }

        $transaction = new WebsiteFeeTransEntity();
        $transaction->setWsfemail($payer_email);
        $transaction->setWsfpaydate($this->paymentDate);
        $transaction->setWsfsubtype($subscr_type);
        $transaction->setWsftxid($txn_id);
        $transaction->setWsfusername($uid);
        $em->persist($transaction);
        $em->flush();
        return true;
    }

    /**
     * getsub find the subscription data
     * again this is a duplicate function to what is in pnuserapi. I have to do
     * this because when calling from paypal it will not jump to another file.
     */
    private function _get_sub($item_number) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        if (isset($item_number) && is_numeric($item_number)) {
            $qb->select('u')->from('PaustianWebsiteFeeModule:WebsiteFeeSubsEntity', 'u');
            $qb->where('u.wsfitem = ?1');
            $qb->setParameter(1, $item_number);
        } else {
            //either both are missing or there is a argument error.
            throw new NotFoundHttpException($this->__('item_number incorrect in WebsiteFeeModule::_get_sub()'));
        }
        $query = $qb->getQuery();
        $the_item = $query->getResult();

        if (empty($the_item)) {
            throw new NotFoundHttpException($this->__('Unable to get subscriber.'));
        }
        return $the_item[0];
    }

}
