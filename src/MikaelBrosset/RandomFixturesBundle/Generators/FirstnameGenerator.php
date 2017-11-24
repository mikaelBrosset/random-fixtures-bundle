<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;

class FirstnameGenerator extends Generator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function calculateValue(MBRFProp $MBRFPropFilled) : Generator
    {
        $firstnames = array_merge($this->openAndSetResourceList('female-firstname'), $this->openAndSetResourceList('male-firstname'));
        $this->setValue(trim($this->selectRandom($firstnames)));

        return $this;
    }


}