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

use DateTime;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
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
    
    public function getId() : int {
        return $this->id;
    }

    public function getWsfusername() : string {
        return $this->wsfusername;
    }

    public function getWsftxid() : string {
        return $this->wsftxid;
    }

    public function getWsfemail() : string {
        return $this->wsfemail;
    }

    public function getWsfpaydate() : DateTime {
        return $this->wsfpaydate;
    }

    public function getWsfsubtype() : string {
        return $this->wsfsubtype;
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function setWsfusername(string $wsfusername) : void {
        $this->wsfusername = $wsfusername;
    }

    public function setWsftxid(string $wsftxid) : void {
        $this->wsftxid = $wsftxid;
    }

    public function setWsfemail(string $wsfemail) : void {
        $this->wsfemail = $wsfemail;
    }

    public function setWsfpaydate(DateTime $wsfpaydate) : void {
        $this->wsfpaydate = $wsfpaydate;
    }

    public function setWsfsubtype(string $wsfsubtype) : void {
        $this->wsfsubtype = $wsfsubtype;
    }
}
