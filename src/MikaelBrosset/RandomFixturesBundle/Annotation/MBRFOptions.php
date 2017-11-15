<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @target("CLASS")
 */
class MBRFOptions
{
    /**
     * @var int
     */
    public $regex;

    /**
     * @return int
     */
    public function getRegex()
    {
        return $this->times;
    }
}