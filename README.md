# WebsiteFee
## Synopsis
WebsiteFee is a Zikula module that enables subscription payments from PayPal and recieves notification by PayPal IPN. Once implemented, a user pays for a subscription and is added to a Zikula group. That group is configured to have special access to the site.

## Code Example

There are two parts two the implmentation. 

1. Create a PayPal button that points to http://mycoolsite.com/websitefee/subscribepaypal of your Zikula site. This is the routine that handles the instant payment notification. Here is an example of an appropriate PayPal button. Note the userid has to be generated dymanically.

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="2DJHSDF86SF6Y">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

In the hosted button, you have to edit the advanced features and put in a notify_url=http://www.mycoolsite.com/websitefee/subscribepaypal. This assumes that your Zikula site is in the root directory. When you create your button, you will need to remember the item number and the price you are charging. 

2. Create a subscription in the WebsiteFee module. Here you will enter the name of the subscription, the price and the item number. These will be checked when notifications come through. More to come.

## Motivation

A short description of the motivation behind the creation and maintenance of the project. This should explain **why** the project exists.

## Installation

Provide code examples and explanations of how to get the project.

## API Reference

Depending on the size of the project, if it is small and simple enough the reference docs can be added to the README. For medium size to larger projects it is important to at least provide a link to where the API reference docs live.

## Tests

Describe and show how to run the tests with code examples.

## Contributors

Let people know how they can dive into the project, include important links to things like issue trackers, irc, twitter accounts if applicable.

## License

A short snippet describing the license (MIT, Apache, etc.)
