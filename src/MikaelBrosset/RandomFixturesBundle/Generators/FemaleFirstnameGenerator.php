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

        $actualNullables = (int) round(count($this->resourceList) / 100 * $nullable);
        $nulledKeys = [];

        for ($i=0; $i<$actualNullables; $i++) {
            $this->getRandomKeyFromArray($this->resourceList, $nulledKeys);
        }

        foreach ($nulledKeys as $key => $value) {
            $this->resourceList[$key] = null;
        }
        $this->setValue($this->selectRandom($this->resourceList));
    }
}