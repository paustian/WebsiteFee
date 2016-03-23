<?php

/**
 * Copyright Timothy Paustian 2016
 *
 * This work is contributed to the Zikula Project by Timothy Paustian under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

namespace Paustian\WebsiteFeeModule\Entity;

use Zikula\Core\Doctrine\EntityAccess;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quickcheck entity class.
 *
 * We use annotations to define the entity mappings to database (see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html).
 *
 * @ORM\Entity
 * @ORM\Table(name="websitefee_errors")
 */
class WebsiteFeeErrorsEntity extends EntityAccess {

    /**
     * WebsiteFeeSubs id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * wsfrequest -- the request line that was sent from paypal. this can be long
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * 
     */
    private $wsfrequest;

    /**
     * wsfrespone -- the response
     *
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $wsfrespone;
    
     /**
     * wsferroeexp - Details about the error
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $wsferroeexp;
    
    /**
     * wsferrdate -- the date and time of the error.
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * 
     */
    private $wsferrdate;
    
    public function getId() {
        return $this->id;
    }

    public function getWsfrequest() {
        return $this->wsfrequest;
    }

    public function getWsfrespone() {
        return $this->wsfrespone;
    }

    public function getWsferroeexp() {
        return $this->wsferroeexp;
    }

    public function getWsferrdate() {
        return $this->wsferrdate;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setWsfrequest($wsfrequest) {
        $this->wsfrequest = $wsfrequest;
    }

    public function setWsfrespone($wsfrespone) {
        $this->wsfrespone = $wsfrespone;
    }

    public function setWsferroeexp($wsferroeexp) {
        $this->wsferroeexp = $wsferroeexp;
    }

    public function setWsferrdate($wsferrdate) {
        $this->wsferrdate = $wsferrdate;
    }




}
