<?php

/**
 * WebsiteFee Module
 *
 * The WebsiteFee module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  administration API --
 *                   The file that contains all administrative
 *                   operational functions for the module
 *
 * @package      Paustian
 * @subpackage   WebsiteFee
 * @version      2.0
 * @author       Timothy Paustian
 * @link         http://www.microbiologytext.com
 * @copyright    Copyright (c) 2016 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


namespace Paustian\WebsiteFeeModule\Api;

class AdminApi extends \Zikula_AbstractApi {

   public function getLinks() {
        $links = array();
        //create exam link
        $links[] = array(
                    'url' => $this->get('router')->generate('paustianwebsitefeemodule_admin_edit'),
                    'text' => $this->__('Create Subscription'), 
                    'icon' => 'plus');
        $links[] = array(
                    'url' => $this->get('router')->generate('paustianwebsitefeemodule_admin_modify'),
                    'text' => $this->__('Modify Subscription'), 
                    'icon' => 'list');
        $links[] = array(
                    'url' => $this->get('router')->generate('paustianwebsitefeemodule_admin_modifytrans'),
                    'text' => $this->__('View/Delete Transactions'), 
                    'icon' => 'list');
        $links[] = array(
                    'url' => $this->get('router')->generate('paustianwebsitefeemodule_admin_modifyerrs'),
                    'text' => $this->__('View/Delete Errors'), 
                    'icon' => 'list');
        return $links;
    }
}

?>