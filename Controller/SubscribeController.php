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

use Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // used in annotations - do not remove
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity;
use Paustian\WebsiteFeeModule\Api\IpnListener;
use DateTime;
use Exception;

/**
 * This controller is for managing subscriptions with paypal. There is no admin or user interaction with
 * it and you don't really need to change the code at all.
 */
class SubscribeController extends AbstractController {

    private $paymentDate;
    private $response;
    private $request;
    private $listener;
    private $debug = true;

    /**
     * @Route("")
     *
     * @return Response
     */
    public function index() : Response {
        //securtiy check first
        if (!$this->hasPermission('quickcheck::', '::', ACCESS_OVERVIEW)) {
            throw new AccessDeniedException();
        }

        return $this->render('@PaustianWebsiteFeeModule/Subscribe/websitefee_subscribe_index.html.twig');
    }

    /**
     * @Route("/testsubscribe")
     *
     *
     * This function is for debugging the subscription.
     *
     * @author       Timothy Paustian
     * @param Request $request
     * @return Response
     */
    public function testsubscribe(Request $request) : Response {
        if($this->debug){
            return $this->render('@PaustianWebsiteFeeModule/Subscribe/websitefee_subscribe_testsubscribe.html.twig',
                ['txnID' => bin2hex(random_bytes(8))]);
        }
        return new Response($this->render('@PaustianWebsiteFeeModule/Subscribe/websitefee_subscribe_index.html.twig'));
    }

    /**
     * @Route("/testupdategroup")
     *
     * @param Request $request
     * @return Response
     */
    public function testupdategroup(Request $request) : Response {
        //This is a test function to try to debug update group
        if($this->debug){
            $this->_updateGroup(3, 3);
        }
        return $this->render('@PaustianWebsiteFeeModule/Subscribe/websitefee_subscribe_index.html.twig');
    }

    /**
     * @Route("/subscribepaypal")
     *
     * This it the routine that actually communicates with PayPal and manages the subscriptions
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function subscribepaypal(Request $request) : Response {
        $this->listener = new IpnListener();
        $this->paymentDate = urldecode($request->get('payment_date'));
        $paymentDateTmp = strtotime($this->paymentDate);
        $this->paymentDate = new DateTime(strftime('%Y-%m-%d %H:%M:%S', $paymentDateTmp));
        $verified = false;
        try {
            $this->listener->use_sandbox = false;
            $this->listener->debug = $this->debug;
            $verified = $this->listener->processIpn();
        } catch (Exception $e) {
            $this->response = $this->listener->getResponse();
            //if we get an exception in the IpnListener, I want to know what was posted.
            $this->request = $_POST;
            $this->_set_error($e->getMessage());
            exit(0);
        }
        $this->response = $this->listener->getResponse();

        if ($verified) {
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
            if ($this->_enterTransaction((int)$uid, $txn_id, $payer_email, $reciever_email, $payment_gross, $item_no, $txn_type)) {
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
            $this->_set_error("Transaction not verified.");

        }

        if($this->debug){
            return $this->render('@PaustianWebsiteFeeModule/Subscribe/websitefee_subscribe_testsubscribe.html.twig');
        }
        /*$response = new Response();

        $response->setContent('');
        $response->setStatusCode(Response::HTTP_OK);

        // sets a HTTP response header
        $response->headers->set('Content-Type', 'text/html');

        // prints the HTTP headers followed by the content
        return $response;*/
        http_response_code (200);
        exit();
    }

    /**
     * @param int $uid
     * @param int $item_no
     */
    private function _cancelSubscription(int $uid, int $item_no) : void {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();
        $e = "";
        if (!$this->_modifyUser($gid, $uid, false, $e)) {
            //write an error to the error log;
            $this->_set_error("Unable to cancel subscription: $e");
        }
    }

    private function _addSubscription(int $uid, int $item_no) : void {
        $subscription = $this->_get_sub($item_no);
        $gid = $subscription->getWsfgroupid();
        $e = "";
        if (!$this->_modifyUser($gid, $uid, true, $e)) {
            //write an error to the error log;
            $this->_set_error("Unable to add subscription: $e");
        }
    }

    /**
     * This is a hack that goes right into the entities for the Group/User module.
     * I was getting permission errors by trying to use the api, Hopefully this plays.
     * @param int $gid
     * @param int $uid
     * @param bool $add
     * @param string $error
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function _modifyUser(int $gid, int $uid, bool $add = true, string &$error="") : bool {
        // Argument check
        if ((!isset($gid)) || (!isset($uid))) {
            throw new \InvalidArgumentException($this->trans('Invalid arguments received'));
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
            $error = $e->getMessage();
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
     * @param string $line
     */
    private function _set_error(string $line) : void {
        $error = new WebsiteFeeErrorsEntity();
        $error->setWsferrdate($this->paymentDate);
        $error->setWsferroeexp($line);
        //$this->request = implode("\n", $_POST);
        $error->setWsfrequest($this->listener->entryData);
        $error->setWsfrespone($this->response);
        $em = $this->getDoctrine()->getManager();
        if(!$em->isOpen()){
            $em = $this->getDoctrine()->resetManager();
        }
        $em->persist($error);
        $em->flush();
    }

    /**
     * This is the guts of the application. It checks to make sure the payment is valid by talking to paypal
     * It then enters the transaction in the database for future referece.
     *
     * @param int $uid
     * @param string $txn_id
     * @param string $payer_email
     * @param string $receiver_email
     * @param string $payment_gross
     * @param string $item_number
     * @param string $subscr_type
     * @return bool
     */
    private function _enterTransaction(int $uid,
                                       string $txn_id,
                                       string $payer_email,
                                       string $receiver_email,
                                       string $payment_gross,
                                       string $item_number,
                                       string $subscr_type) :bool {

        // Argument check - make sure that all required arguments are present,
        // if not then set an appropriate error message and return
        if ((!isset($uid)) || (!isset($payer_email)) ||
            (!isset($txn_id))) {
            throw new NotFoundHttpException($this->trans('Variable error in _enter_transaction'));
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
        if (empty($subscript_info)) {
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

        if($subscr_type !== "subscr_cancel"){
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
        }

        $transaction = new WebsiteFeeTransEntity();
        $transaction->setWsfemail($payer_email);
        $transaction->setWsfpaydate($this->paymentDate);
        $transaction->setWsfsubtype($subscr_type);
        $transaction->setWsftxid($txn_id);
        $transaction->setWsfusername($uid);
        if(!$em->isOpen()){
            $em = $this->getDoctrine()->resetManager();
        }
        $em->persist($transaction);
        $em->flush();
        //Just record the information for now. Get rid of this code later
        $this->_set_error("transaction worked.");

        return true;
    }

    /**
     * getsub find the subscription data
     * again this is a duplicate function to what is in pnuserapi. I have to do
     * this because when calling from paypal it will not jump to another file.
     *
     * @param int $item_number
     * @return array
     */
    private function _get_sub(int $item_number) : ?WebsiteFeeSubsEntity {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        if (isset($item_number) && is_numeric($item_number)) {
            $qb->select('u')->from('PaustianWebsiteFeeModule:WebsiteFeeSubsEntity', 'u');
            $qb->where('u.wsfitem = ?1');
            $qb->setParameter(1, $item_number);
        } else {
            //either both are missing or there is a argument error.
            throw new NotFoundHttpException($this->trans('item_number incorrect in WebsiteFeeModule::_get_sub()'));
        }
        $query = $qb->getQuery();
        $the_item = $query->getResult();

        if (empty($the_item)) {
            return null;
        }
        return $the_item[0];
    }

}
