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
 * @target("PROPERTY")
 */
class MBRFProp
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $null = 0;

    /**
     * @var string
     */
    public $option;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getNull()
    {
        return $this->null;
    }

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }
}