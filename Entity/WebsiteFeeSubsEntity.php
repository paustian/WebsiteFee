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
 * @ORM\Table(name="websitefee_subs")
 */
class WebsiteFeeSubsEntity extends EntityAccess {

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
    private $wsfsubsname;

    /**
     * wsfitem item number that is ordered
     *
     * @ORM\Column(type="integer", length=20)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * 
     */
    private $wsfitem;
    
     /**
     * wsfpaymentamount - payment amount
     *
     * @ORM\Column(type="integer", length=11)
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     */
    private $wsfpaymentamount;
    
    /**
     * WebsiteFeeSubs wsfsubsname -- the name of the subscription.
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     * 
     */
    private $wsfemail;
    
    /**
     * wsfpaymentamount - payment amount
     *
     * @ORM\Column(type="integer", length=11)
     *  @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * 
     */
    private $wsfgroupid;

    public function getId() {
        return $this->id;
    }

    public function getWsfsubsname() {
        return $this->wsfsubsname;
    }

    public function getWsfitem() {
        return $this->wsfitem;
    }

    public function getWsfpaymentamount() {
        return $this->wsfpaymentamount;
    }

    public function getWsfemail() {
        return $this->wsfemail;
    }

    public function getWsfgroupid() {
        return $this->wsfgroupid;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setWsfsubsname($wsfsubsname) {
        $this->wsfsubsname = $wsfsubsname;
    }

    public function setWsfitem($wsfitem) {
        $this->wsfitem = $wsfitem;
    }

    public function setWsfpaymentamount($wsfpaymentamount) {
        $this->wsfpaymentamount = $wsfpaymentamount;
    }

    public function setWsfemail($wsfemail) {
        $this->wsfemail = $wsfemail;
    }

    public function setWsfgroupid($wsfgroupid) {
        $this->wsfgroupid = $wsfgroupid;
    }
}


