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
    public $nullable = 0;

    /**
     * @var string
     */
    public $options;

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
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }
}