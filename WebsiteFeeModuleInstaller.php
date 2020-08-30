<?php

/**
 * WebsiteFeeModule Module
 * @package      Paustian
 * @subpackage   WebsiteFeeModule
 * The WebsiteFeeModule module
 * @version      $Id: pninit.php,v 1.20 2005/08/09 12:22:08 markwest Exp $
 * @author       Timothy Paustian
 * @link			http://www.microbiologytext.com
 * @copyright    Copyright (c) 2006 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Paustian\WebsiteFeeModule;

use Zikula\ExtensionsModule\Installer\AbstractExtensionInstaller;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity;
use Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity;

class WebsiteFeeModuleInstaller extends AbstractExtensionInstaller {
    
    private $entities = array(
            WebsiteFeeSubsEntity::class,
            WebsiteFeeTransEntity::class,
            WebsiteFeeErrorsEntity::class
        );

    /**
     * initialise the WebsiteFeeModule module
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function install() : bool{
        try {
            $this->schemaTool->create($this->entities);
        } catch (\Exception $e) {
            print($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * upgrade the WebsiteFeeModule module from an old version
     *
     * This function can be called multiple times
     * This function MUST exist in the pninit file for a module
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function upgrade($oldversion) : bool {
        //right now there are no old versions
        // Upgrade dependent on old version number
        switch ($oldversion) {
            
        }

        // Update successful
        return true;
    }

    /**
     * delete the WebsiteFeeModule module
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance
     * This function MUST exist in the pninit file for a module
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function uninstall() : bool {
        try {
            $this->schemaTool->drop($this->entities);
        } catch (\PDOException $e) {
            print($e->getMessage());
            return false;
        }

        // Deletion successful
        return true;
    }
}

