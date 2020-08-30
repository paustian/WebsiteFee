<?php


/**
 * WebsiteFeeModule Module
 *
 * The WebsiteFeeModule module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  User API --
 *                   The file that contains all user operational
 *                   functions for the module
 *
 * @package      Paustian
 * @subpackage   WebsiteFeeModule
 * @version      $Id: pnuserapi.php,v 1.15 2004/09/18 21:30:28 markwest Exp $
 * @author       Timothy Paustian
 * @link              http://www.microbiologytext.com
 * @copyright    Copyright (c) 2006 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Paustian\WebsiteFeeModule\Api;

class UserApi extends \Zikula_AbstractApi {

    /**
     * I don't think this is used anywhere.
     *
     * @param $date
     * @return false|string
     */
    function date_convert($date) {
        $date_year = substr($date, 0, 4);
        $date_month = substr($date, 5, 2);
        $date_day = substr($date, 8, 2);
        $date = date("F jS, Y", mktime(0, 0, 0, $date_month, $date_day, $date_year));
        return $date;
    }
}

