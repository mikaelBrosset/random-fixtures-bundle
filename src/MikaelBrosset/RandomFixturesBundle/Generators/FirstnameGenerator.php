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
    public function getValue(MBRFProp $MBRFPropFilled): string
    {
        $firstnames = array_merge($this->openFile('female-firstnames'), $this->openFile('male-firstnames'));
        return $this->selectRandom($firstnames);
    }


}