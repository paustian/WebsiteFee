<?php
/**
 * WebsiteFee Module
 * 
 * The WebsiteFee module shows how to make a PostNuke module. 
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  Provide version and credit information about the module
 *
 * @package      PostNuke_Miscellaneous_Modules
 * @subpackage   WebsiteFee
 * @version      $Id: pnversion.php,v 1.14 2005/02/27 11:01:01 landseer Exp $
 * @author       Timothy Paustian
 * @link         http://www.microbiologytext.com
 * @copyright    Copyright (c) 2006 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
namespace Paustian\WebsiteFeeModule;

class WebsiteFeeModuleVersion extends \Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $meta = array();
        $meta['name'] = __('WebsiteFee');
        $meta['version']  = '2.0.0';
        $meta['displayname'] = __('WebsiteFee');
        $meta['description'] = __('A module for collecting payments using PayPal for subscriptions.');
        // this defines the module's url and should be in lowercase without space
        $meta['url'] = $this->__('websitefee');
        $meta['core_min'] = '1.4.0'; // Fixed to >1.4.x range
        $meta['securityschema'] = array('WebsiteFee::' => 'WebsiteFee item name::WebsiteFee item ID');
        $meta['author'] = 'Timothy Paustian';
        $meta['contact'] = 'http://http://www.bact.wisc.edu/faculty/paustian/';
        
        return $meta;
    }

}

