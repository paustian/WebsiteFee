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
 * @ORM\Table(name="websitefee_trans")
 */
class WebsiteFeeTransEntity extends EntityAccess {

    /**
     * WebsiteFeeSubs id
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=20)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * WebsiteFeeSubs wsfsubsname -- the name of the subscription.
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * 
     */
    private $wsfusername;

    /**
     * wsftxid the transaction ID, it always has this type of format
     *  9HP94611NG199133T
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $wsftxid;
    
     /**
     * wsfemail - payer's email
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $wsfemail;
    
    /**
     * WebsiteFeeSubs wsfsubsname -- the name of the subscription.
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $wsfpaydate;
    
    /**
     * wsfsubtype - subscription type
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $wsfsubtype;
    
    public function getId() {
        return $this->id;
    }

    public function getWsfusername() {
        return $this->wsfusername;
    }

    public function getWsftxid() {
        return $this->wsftxid;
    }

    public function getWsfemail() {
        return $this->wsfemail;
    }

    public function getWsfpaydate() {
        return $this->wsfpaydate;
    }

    public function getWsfsubtype() {
        return $this->wsfsubtype;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setWsfusername($wsfusername) {
        $this->wsfusername = $wsfusername;
    }

    public function setWsftxid($wsftxid) {
        $this->wsftxid = $wsftxid;
    }

    public function setWsfemail($wsfemail) {
        $this->wsfemail = $wsfemail;
    }

    public function setWsfpaydate($wsfpaydate) {
        $this->wsfpaydate = $wsfpaydate;
    }

    public function setWsfsubtype($wsfsubtype) {
        $this->wsfsubtype = $wsfsubtype;
    }
}
