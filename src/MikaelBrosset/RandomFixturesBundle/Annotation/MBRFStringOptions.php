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
class MBRFStringOptions
{
    /**
     * @var string
     */
    public $regex;
    /**
     * @var int
     */
    public $fixedLength;
    /**
     * @var string
     */
    public $rangeLength;
    /**
     * @var bool
     */
    public $randLength;
    /**
     * @var bool
     */
    public $upper;
    /**
     * @var bool
     */
    public $lower;
    /**
     * @var bool
     */
    public $cap;


    /**
     * @return int
     */
    public function getRegex()
    {
        return $this->regex;
    }
}