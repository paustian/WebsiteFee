<?php

/**
 * WebsiteFee Module
 * @package      Paustian
 * @subpackage   WebsiteFee
 * The WebsiteFee module 
 * @version      $Id: pninit.php,v 1.20 2005/08/09 12:22:08 markwest Exp $
 * @author       Timothy Paustian
 * @link			http://www.microbiologytext.com
 * @copyright    Copyright (c) 2006 Timothy Paustian
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Paustian\WebsiteFeeModule;

use Zikula\Core\ExtensionInstallerInterface;
use Zikula\Core\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DoctrineHelper;

class WebsiteFeeModuleInstaller implements ExtensionInstallerInterface, ContainerAwareInterface {
    
    private $entities = array(
            'Paustian\WebsiteFeeModule\Entity\WebsiteFeeSubsEntity',
            'Paustian\WebsiteFeeModule\Entity\WebsiteFeeTransEntity',
            'Paustian\WebsiteFeeModule\Entity\WebsiteFeeErrorsEntity'
        );
    private $entityManager;
    /**
     * @var ContainerInterface
     * You need this to get the entitymanager.
     */
    private $container;
    
    private $bundle;
    /**
     * initialise the WebsiteFee module
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance.
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function install() {
        $this->entityManager = $this->container->get('doctrine.entitymanager');
        
        try {
            DoctrineHelper::createSchema($this->entityManager, $this->entities);
        } catch (\Exception $e) {
            print($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * upgrade the WebsiteFee module from an old version
     *
     * This function can be called multiple times
     * This function MUST exist in the pninit file for a module
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function upgrade($oldversion) {
        //right now there are no old versions
        // Upgrade dependent on old version number
        switch ($oldversion) {
            
        }

        // Update successful
        return true;
    }

    /**
     * delete the WebsiteFee module
     *
     * This function is only ever called once during the lifetime of a particular
     * module instance
     * This function MUST exist in the pninit file for a module
     *
     * @author       Timothy Paustian
     * @return       bool       true on success, false otherwise
     */
    public function uninstall() {
        $this->entityManager = $this->container->get('doctrine.entitymanager');
        try {
            DoctrineHelper::dropSchema($this->entityManager, $this->entities);
        } catch (\PDOException $e) {
            print($e->getMessage());
            return false;
        }

        // Deletion successful
        return true;
    }
    
    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->setTranslator($container->get('translator'));
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
    
    public function setBundle(AbstractBundle $bundle)
    {
        $this->bundle = $bundle;
    }
}

