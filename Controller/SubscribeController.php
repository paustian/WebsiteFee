<?php

/**
 * WebsiteFee Module
 *
 * The WebsiteFee module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  user display functions --
 *                   This file contains all user GUI functions for the module
 *
 * @package      Paustian
 * @subpackage   WebsiteFee
 * @version      $Id: pnuser.php,v 1.19 2005/06/03 11:24:15 markwest Exp $
 * @author       Timothy Paustian
 * @author       Timothy Paustian
 * @link         http://www.microbiologytext.com
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

class SubscribeController extends AbstractController {

    /**
     * @Route("")
     * 
     * view
     * This routine allows for the user to perform the only function, creating a
     * quiz.
     * Using all (or a subset of) the questions available, create a multiple choice
     * quiz.
     *
     * Params: none
     * Returns: the quiz. This can be graded by the gradequiz funciton below
     */
    public function indexAction() {
        //securtiy check first
        if (!SecurityUtil::checkPermission('quickcheck::', '::', ACCESS_OVERVIEW)) {
            throw new AccessDeniedException();
        }

        return new Response($this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_index.html.twig'));
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
      
         return new Response($this->render('PaustianWebsiteFeeModule:Subscribe:websitefee_subscribe_testsubscribe.html.twig'));
    }


    /**
     * @Route("/subscribepaypal")
     * @param Request $request
     */
    public function subscribepaypalAction(Request $request) {          
        $listener = new IpnListener();
        $payment_date = urldecode($request->get('payment_date'));
        $paymentDateTmp = strtotime($payment_date);
        $payment_date = new DateTime(strftime('%Y-%m-%d %H:%M:%S', $paymentDateTmp));
        try {
            $listener->debug = false;
            $listener->force_ssl_v3 = false;
            $verified = $listener->processIpn();
        } catch (Exception $e) {
            $res = $listener->getResponse();
            $req = $listener->getPostUri();
            $this->_set_error($req, $res, $payment_date, $e->getMessage());
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
            $res = $listener->getResponse();
            $req = $listener->getPostUri();
            $u_vars = UserUtil::getVars($uid);
            //enter transcaction makes sure it has all the information that we need
            if ($this->_enterTransaction($uid, $txn_id, $payer_email, $payment_date, $req, $res, $reciever_email, $payment_gross, $item_no, $txn_type)) {
                if ($txn_type === 'subscr_cancel') {
                    $this->_cancelSubscription($uid, $item_no);
                } else if ($txn_type === 'subscr_payment' && $payment_status === 'Completed') {
                    //Paypal sends 3 message upon purchase. We only want to add the subscription 
                    //when the transaction is completed.
                    $this->_addSubscription($uid, $item_no);
                }
            }
        } else {
            //we have an invalid transaction, record it.
            $res = $listener->getResponse();
            $req = $listener->getPostUri();
            $this->_set_error($req, $res, $payment_date, "Transaction not verified");
        }
    }
    private function _writetofile($inText){
        $basedir = System::serverGetVar('DOCUMENT_ROOT');
        $handle = fopen($basedir . '/6th_ed/userdata/ipnlog.txt', 'w');        
        $result = fwrite($handle, $inText);
        fclose($handle);
    }
    
    private function _cancelSubscription($uid, $item_no) {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();
        
        $this->_updateGroup($uid, $gid, false);
    }

    private function _addSubscription($uid, $item_no) {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();
        $this->_updateGroup($uid, $gid);
    }

    private function _updateGroup($uid, $gid, $add = true) {
        $adminID = 2;
        //we have to do this here because the incoming uri is not going to have admin status.
        //we temporarily change it. This is safe because we verified the call was coming
        //from paypal
        UserUtil::setUserByUid($adminID);
        if ($add) {
            ModUtil::apiFunc('ZikulaGroupsModule', 'user', 'adduser', array('gid' => $gid, 'uid' => $uid));
        } else {
            ModUtil::apiFunc('ZikulaGroupsModule', 'user', 'removeuser', array('gid' => $gid, 'uid' => $uid));
        }
    }

    /**
     * Set an error in the database. We are breaking convention to make this a private function
     * there should be no way to call this from outside.
     *
     *  set_error
     *
     * @param $req - The initial request
     * @param $res - The reply from paypal
     * @param $line - A line explaining why an error was posted.
     *
     *
     */
    private function _set_error($req, $res, $payment_date, $line) {
        $error = new WebsiteFeeErrorsEntity();
        $error->setWsferrdate($payment_date);
        $error->setWsferroeexp($line);
        $error->setWsfrequest($req);
        $error->setWsfrespone($res);
        $em = $this->getDoctrine()->getManager();
        $em->persist($error);
        $em->flush();
    }

    private function _enterTransaction($uid, $txn_id, $payer_email, $payment_date, $req, $res, $receiver_email, $payment_gross, $item_number, $subscr_type) {

        // Argument check - make sure that all required arguments are present,
        // if not then set an appropriate error message and return
        if ((!isset($uid)) || (!isset($payer_email)) ||
                (!isset($txn_id)) ||
                (!isset($payment_date))) {
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
            $this->_set_error($req, $res, $payment_date, "Duplicate transaction ID:" . $txn_id);
            return false;
        }

        //now grab the data out of the subsciption table
        $subscript_info = $this->_get_sub($item_number);
        if($subscript_info == null){
            $this->_set_error($req, $res, $payment_date, "No item for the item number. Check your subscription setup to insure that your Subscription Item Number matches what you are putting in your paypal button");
            return false;
        }
        $email = $subscript_info->getWsfemail();
        // check that receiver_email is your Primary PayPal email
        if ($receiver_email !== $email) {
            //not the correct email, again log as a spoof.
            $this->_set_error($req, $res, $payment_date, "Incorrect reciever Email:" . $receiver_email . ", correct Email should be:" . $email);
            return false;
        }
        
        $payment_amt = $subscript_info->getWsfpaymentamount();
        if ($payment_gross == -1) {
            $payment_gross = $payment_amt;
        }
       
        // check that payment_amount/payment_currency are correct
        if ($payment_gross != $payment_amt) {
            //wrong amount payed
            $this->_set_error($req, $res, $payment_date, "payment inccorect: " . $payment_gross . ", correct amount should be: " . $payment_amt);
            return false;
        }

        $transaction = new WebsiteFeeTransEntity();
        $transaction->setWsfemail($payer_email);
        $transaction->setWsfpaydate($payment_date);
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
            throw new NotFoundHttpException($this->__('item_number incorrect in WebsiteFee::_get_sub()'));
        }
        $query = $qb->getQuery();
        $the_item = $query->getResult();

        if ($the_item === false) {
            throw new NotFoundHttpException($this->__('Unable to get subscriber.'));
        }
        return $the_item[0];
    }

}