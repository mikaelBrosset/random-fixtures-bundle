<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;

class MaleFirstnameGenerator extends Generator implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function calculateValue(MBRFProp $MBRFPropFilled) : Generator
    {
        $this->setSomeListElementsAsNull($MBRFPropFilled->getNullable());
        $this->setValue($this->selectRandom($this->resourceList));

        return $this;
    }
}
