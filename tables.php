<?php
// $Id: pntables.php,v 1.10 2004/09/29 13:19:15 markwest Exp $
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (c) 2006 Timothy Paustian
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
/**
 * WebsiteFee Module
 *
 * The WebsiteFee module shows how to make a PostNuke module.
 * It can be copied over to get a basic file structure.
 *
 * Purpose of file:  Table information for WebsiteFee module --
 *                   This file contains all information on database
 *                   tables for the module
 *
 * @package      PostNuke_Commerce_Modules
 * @subpackage   WebsiteFee
 * @version      $Id: pntables.php,v 1.10 2006/12/03 13:19:15 markwest Exp $
 * @author       Timothy Paustian
 * @link         http://www.microbiologytextbook.com
 * @copyright    Copyright (c) 2006 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


/**
 * Populate pntables array for WebsiteFee module
 *
 * This function is called internally by the core whenever the module is
 * loaded. It delivers the table information to the core.
 * It can be loaded explicitly using the pnModDBInfoLoad() API function.
 *
 * @author       Timothy Paustian
 * @version      $Revision: 1.0 $
 * @return       array       The table information.
 */
function WebsiteFee_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the WebsiteFee item table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $WebsiteFee = DBUtil::getLimitedTablename('websitefee');

    // Set the table name
    $pntable['websiteFee_payees'] = $WebsiteFee;

    // Set the column names. 
    //payment_date: the date the transaction was recorded.
    $pntable['websiteFee_payees_column'] = array('id'      => $WebsiteFee . '_id',
                                       'user_name' => $WebsiteFee . '_user_name',
                                       'txn_id'   => $WebsiteFee . '_txnid',
                                       'payer_email' => $WebsiteFee . '_payer_email',
                                       'payment_date' => $WebsiteFee . '_payment_date',
                                       'subscr_type' => $WebsiteFee . '_subscr_type');

    //define the columns
    $pntable['websiteFee_payees_column_def'] = array('id'      => 'I(11) NOTNULL AUTO PRIMARY',
                                       'user_name' => "X NOTNULL DEFAULT ''",
                                       'txn_id'   => "X NOTNULL DEFAULT ''",
                                       'payer_email' => "X NOTNULL DEFAULT ''",
                                       'payment_date' => 'D NOTNULL DEFDATE',
                                       'subscr_type' => "X NOTNULL DEFAULT ''");

    $WebsiteFee = DBUtil::getLimitedTablename('websiteFee_errors');

    // Set the table name
    //If an error occurs the request that came in and the result from paypal
    //is recorded for future analysis. Line lists where exactly the error occured
    //when inserting information into this table, a unique description should be placed
    //for line.
    $pntable['websiteFee_errors'] = $WebsiteFee;
    $pntable['websiteFee_errors_column'] = array('id' => 'wsf_error_id',
                                                    'req' => 'wsf_req',
                                                    'res' => 'wsf_res',
                                                    'line' => 'wsf_line',
                                                    'date' => 'wsf_date');
    
    $pntable['websiteFee_errors_column_def'] = array('id' => 'I(11) NOTNULL AUTO PRIMARY',
                                                    'req' => "X  NOTNULL DEFAULT ''",
                                                    'res' => "X  NOTNULL DEFAULT ''",
                                                    'line' => "X NOTNULL DEFAULT ''",
                                                    'date' => "D NOTNULL DEFDATE");

    $WebsiteFee = DBUtil::getLimitedTablename('websiteFee_subs');

    // Set the table name
    //a table for setting up items that will be sold as subscriptions to this web site
    //this information should be identified by the tag. this will be searched for
    //when matching the item for creation of the correct button. It is important
    //to make sure that 
    $pntable['websiteFee_subs'] = $WebsiteFee;
    $pntable['websiteFee_subs_column'] = array('id' => 'wsf_subs_id',
                                                'item_name' => 'wsf_item_name',
                                                'item' => 'wsf_item',
                                                'payment_amount' => 'wsf_payment_amount',
                                                'seller_email' => 'wsf_seller_email',
                                                'group_id' => 'wsf_group_id');

    $pntable['websiteFee_subs_column_def'] = array('id' => 'I(11) NOTNULL AUTO PRIMARY',
                                                'item_name' => "X NOTNULL DEFAULT ''",
                                                'item' => 'I(11) NOTNULL DEFAULT 0',
                                                'payment_amount' => 'I(11) NOTNULL DEFAULT 0',
                                                'seller_email' => "X NOTNULL DEFAULT ''",
                                                'group_id' => 'I(11) NOTNULL DEFAULT 0');

    // Return the table information
    return $pntable;
}

?>