<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;

interface GeneratorInterface
{
    /**
     * Returns a processed random value according to the parameters (ex: some random firstnames)
     */
    public function getValue(MBRFProp $MBRFPropFilled);
}