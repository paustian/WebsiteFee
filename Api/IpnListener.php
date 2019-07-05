<?php

/**
 *  PayPal IPN Listener
 *
 *  A class to listen for and handle Instant Payment Notifications (IPN) from
 *  the PayPal server. I modified the code from that provided by Micah.
 * This update to the code points at the new paypal sites and should conform
 * to the new IPN protocol that PayPal is implmenting in Fall of 2016
 *
 *  @package    PHP-PayPal-IPN
 *  @author     Timothy Paustian
 *  @copyright  (c) 2019 - Timothy Paustian
 *  @version    3.1.0
 */

namespace Paustian\WebsiteFeeModule\Api;

use Symfony\Component\Config\Definition\Exception\Exception;

class IpnListener {

    /**
     *  If true, cURL will use the CURLOPT_FOLLOWLOCATION to follow any
     *  "Location: ..." headers in the response.
     *
     *  @var boolean
     */
    public $follow_location = false;


    /**
     *  If true, the paypal sandbox URI www.sandbox.paypal.com is used for the
     *  post back. If false, the live URI www.paypal.com is used. Default false.
     *
     *  @var boolean
     */
    public $use_sandbox = false;
    public $debug = false;

    /**
     *  The amount of time, in seconds, to wait for the PayPal server to respond
     *  before timing out. Default 30 seconds.
     *
     *  @var int
     */
    public $timeout = 30;
    private $post_data = array();
    private $post_uri = '';
    private $response_status = '';
    private $response = '';
    public $entryData;

    const PAYPAL_HOST = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    const SANDBOX_HOST = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    /**
     *  Post Back Using cURL
     *
     *  Sends the post back to PayPal using the cURL library. Called by
     *  the processIpn() method if the use_curl property is true. Throws an
     *  exception if the post fails. Populates the response, response_status,
     *  and post_uri properties on success.
     *
     *  @param  string  The post data as a URL encoded string
     */
    protected function curlPost($encoded_data) {

        $uri = $this->getPaypalHost();

        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");

        $this->response = curl_exec($ch);
        $this->response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($this->response === false || $this->response_status == '0') {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: [$errno] $errstr");
        }
        curl_close($ch);
    }


    private function getPaypalHost() {
        if ($this->use_sandbox)
            return self::SANDBOX_HOST;
        else
            return self::PAYPAL_HOST;
    }

    /**
     *  Get POST URI
     *
     *  Returns the URI that was used to send the post back to PayPal. This can
     *  be useful for troubleshooting connection problems. The default URI
     *  would be "ssl://www.sandbox.paypal.com:443/cgi-bin/webscr"
     *
     *  @return string
     */
    public function getPostUri() {
        return $this->post_uri;
    }

    /**
     *  Get Response
     *
     *  Returns the entire response from PayPal as a string including all the
     *  HTTP headers.
     *
     *  @return string
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     *  Get Response Status
     *
     *  Returns the HTTP response status code from PayPal. This should be "200"
     *  if the post back was successful.
     *
     *  @return string
     */
    public function getResponseStatus() {
        return $this->response_status;
    }

    /**
     *  Get Text Report
     *
     *  Returns a report of the IPN transaction in plain text format. This is
     *  useful in emails to order processors and system administrators. Override
     *  this method in your own class to customize the report.
     *
     *  @return string
     */
    public function getTextReport() {

        $r = '';

        // date and POST url
        for ($i = 0; $i < 80; $i++) {
            $r .= '-';
        }
        $r .= "\n[" . date('m/d/Y g:i A') . '] - ' . $this->getPostUri();

        // HTTP Response
        for ($i = 0; $i < 80; $i++) {
            $r .= '-';
        }
        $r .= "\n{$this->getResponse()}\n";

        // POST vars
        for ($i = 0; $i < 80; $i++) {
            $r .= '-';
        }
        $r .= "\n";

        foreach ($this->post_data as $key => $value) {
            $r .= str_pad($key, 25) . "$value\n";
        }
        $r .= "\n\n";

        return $r;
    }

    /**
     *  Process IPN
     *
     *  Handles the IPN post back to PayPal and parsing the response. Call this
     *  method from your IPN listener script. Returns true if the response came
     *  back as "VERIFIED", false if the response came back "INVALID", and
     *  throws an exception if there is an error.
     *
     *  @param array
     *
     *  @return boolean
     */
    public function processIpn($post_data = null) {

        $encoded_data = 'cmd=_notify-validate';

        if ($post_data === null) {
            // use raw POST data
            if (!empty($_POST)) {
                $raw_post_data = file_get_contents('php://input');
                $ray_post_array = explode('&', $raw_post_data);
                $myPost = [];
                foreach($ray_post_array as $keyval){
                    $keyval = explode('=', $keyval);
                    if(count($keyval) == 2){
                        $myPost[$keyval[0]] = urldecode($keyval[1]);
                    }
                }
                foreach($myPost as $key => $value){
                    $encoded_data .= "&$key=" . urlencode($value);
                }
                $this->entryData = $encoded_data;
            } else {
                throw new Exception("No POST data found.");
            }
        } else {
            // use provided data array
            $this->post_data = $post_data;

            foreach ($this->post_data as $key => $value) {
                $encoded_data .= "&$key=" . urlencode($value);
            }
        }
        if ($this->debug) {
            $this->response = "VERIFIED";
            $this->response_status = '200 OK';
        } else {
            $this->curlPost($encoded_data);
        }
        if (strpos($this->response_status, '200') === false) {
            throw new Exception("Invalid response status: " . $this->response_status . $encoded_data);
        }

        if (strpos($this->response, "VERIFIED") !== false) {
            return true;
        } elseif (strpos($this->response, "INVALID") !== false) {
            return false;
        } else {
            throw new Exception("Unexpected response from PayPal. " . $encoded_data);
        }
    }

    /**
     *  Require Post Method
     *
     *  Throws an exception and sets a HTTP 405 response header if the request
     *  method was not POST.
     */
    public function requirePostMethod() {
        // require POST requests
        if ($_SERVER['REQUEST_METHOD'] && $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Allow: POST', true, 405);
            throw new Exception("Invalid HTTP request method.");
        }
    }

}
