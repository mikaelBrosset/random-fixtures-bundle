<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;

class LastnameGenerator extends Generator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getValue(MBRFProp $MBRFPropFilled): string
    {
        $lastnames = $this->openFile('lastnames');
        return $this->selectRandom($lastnames);
    }
}
