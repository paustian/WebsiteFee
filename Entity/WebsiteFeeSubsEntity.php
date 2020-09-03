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

use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
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
     * @ORM\Column(type="integer", length=11)
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
     * @ORM\Column(type="integer", length=11)
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
     * WebsiteFeeSubs wsfemail -- the name of the subscription.
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

    public function getId() : int {
        return $this->id;
    }

    public function getWsfsubsname() : string {
        return $this->wsfsubsname;
    }

    public function getWsfitem() : int {
        return $this->wsfitem;
    }

    public function getWsfpaymentamount() : int {
        return $this->wsfpaymentamount;
    }

    public function getWsfemail() : string {
        return $this->wsfemail;
    }

    public function getWsfgroupid() : int {
        return $this->wsfgroupid;
    }
    
    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function setWsfsubsname(string $wsfsubsname) : void {
        $this->wsfsubsname = $wsfsubsname;
    }

    public function setWsfitem(int $wsfitem) : void {
        $this->wsfitem = $wsfitem;
    }

    public function setWsfpaymentamount(int $wsfpaymentamount) : void {
        $this->wsfpaymentamount = $wsfpaymentamount;
    }

    public function setWsfemail(string $wsfemail) : void {
        $this->wsfemail = $wsfemail;
    }

    public function setWsfgroupid(int $wsfgroupid) : void {
        $this->wsfgroupid = $wsfgroupid;
    }
}


