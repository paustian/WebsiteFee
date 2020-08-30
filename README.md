# WebsiteFee
This is the Zikula 2.0 and below version. A new version for Zikula 3.0 is under development.

## Synopsis
WebsiteFee is a Zikula module that enables subscription payments from PayPal and recieves notification by PayPal IPN. Once implemented, a user pays for a subscription and is added to a Zikula group. That group is configured to have special access to the site.

## Code Example

There are two parts two the implmentation. 

1. Create a PayPal button that points to http://mycoolsite.com/websitefee/subscribepaypal of your Zikula site. This is the routine that handles the instant payment notification. Here is an example of an appropriate PayPal button. Note the userid has to be generated dymanically.
```
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="2DJHSDF86SF6Y">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
```
In the hosted button, you have to edit the advanced features and put in a notify_url=http://www.mycoolsite.com/websitefee/subscribepaypal. This assumes that your Zikula site is in the root directory. When you create your button, you will need to remember the item number and the price you are charging. 

2. Create a subscription in the WebsiteFee module. Here you will enter the name of the subscription, the price and the item number. These will be checked when notifications come through. 

## Motivation

This is a simple implementation of the PayPal IPN system. 

## Installation

This installs just like any other Zikula module. Further set up is as described in the code sample

## Tests

Test code is provided in the Subscrition testsubscribe method. You can change the twig template websitefee_subscribe_testsubscribe.html.twig. To fit your needs


## License

This code is released under the GNU General Public License (http://www.gnu.org/copyleft/gpl.html )
