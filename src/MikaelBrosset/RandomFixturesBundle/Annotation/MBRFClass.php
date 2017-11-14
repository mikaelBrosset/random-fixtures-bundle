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

    public $toto;

    /**
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }

    public function getToto()
    {
        return $this->toto;
    }

    public function getMandatoryProperties(): array
    {
        return [
            "times"
        ];
    }

    public function linkedMandatoryProperties(): array
    {
        return [];
    }
}