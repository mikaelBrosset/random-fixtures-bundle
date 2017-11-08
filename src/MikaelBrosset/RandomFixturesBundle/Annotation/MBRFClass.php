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
class MBRFClass
{
    /**
     * @var int
     */
    public $times;

    /**
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }
}