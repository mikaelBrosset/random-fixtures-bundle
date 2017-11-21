<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;

class FemaleFirstnameGenerator extends Generator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function calculateValue(MBRFProp $MBRFPropFilled)
    {
        $nullable = $MBRFPropFilled->getNullable();
        $femaleFirstnames = $this->openFile($this->getResourceName());

        $actualNullables = (int) round(count($femaleFirstnames) / 100 * $nullable);

        $nulledKeys = [];
        for ($i=0; $i<$actualNullables; $i++) {
            $this->getRandomKeyFromArray($femaleFirstnames, $nulledKeys);
        }

        foreach ($nulledKeys as $key => $value) {
            $femaleFirstnames[$key] = null;
        }
        $this->setValue($this->selectRandom($femaleFirstnames));
    }
}