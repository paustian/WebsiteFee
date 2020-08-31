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

use Symfony\Component\Validator\Constraints\DateTime;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
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
    
    public function getId() : int {
        return $this->id;
    }

    public function getWsfrequest() : string {
        return $this->wsfrequest;
    }

    public function getWsfrespone() : string {
        return $this->wsfrespone;
    }

    public function getWsferroeexp() : string {
        return $this->wsferroeexp;
    }

    public function getWsferrdate() : datetime {
        return $this->wsferrdate;
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function setWsfrequest(string $wsfrequest) : void {
        $this->wsfrequest = $wsfrequest;
    }

    public function setWsfrespone(string $wsfrespone) : void {
        $this->wsfrespone = $wsfrespone;
    }

    public function setWsferroeexp(string $wsferroeexp) : void {
        $this->wsferroeexp = $wsferroeexp;
    }

    public function setWsferrdate(datetime $wsferrdate) : void  {
        $this->wsferrdate = $wsferrdate;
    }




}
