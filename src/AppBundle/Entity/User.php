<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $firstName;
    /**
     * @ORM\Column(type="string")
     */
    private $lastName;

    public function getId(){
        return $this->id;
    }
    public function setFirstName($value){
        $this->firstName = $value;
        return $this;
    }
    public function getFirstName(){
        return $this->firstName;
    }
    public function setLastName($value){
        $this->lastName = $value;
        return $this;
    }
    public function getLastName(){
        return $this->lastName;
    }

}