<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFProp;
use MikaelBrosset\RandomFixturesBundle\Exception\UnknownPropertyOptionException;
use MikaelBrosset\RandomFixturesBundle\Exception\WrongCharacterNumberException;
use MikaelBrosset\RandomFixturesBundle\Exception\WrongOptionFormatException;
use MikaelBrosset\RandomFixturesBundle\Annotation\MBRFOptions;

class TextGenerator extends Generator implements GeneratorInterface
{
    protected $regex = null;
    protected $fixedLength = null;
    protected $rangeLength = null;
    protected $randLength = false;
    protected $upper = false;
    protected $lower = false;
    protected $cap = true;

    protected $availableOptions;

    /**
     * @inheritdoc
     */
    public function calculateValue(MBRFProp $MBRFPropFilled) : Generator
    {
        if (!is_null($MBRFPropFilled->getOptions())) {
            $options = $this->chunkOptions($MBRFPropFilled->getOptions());
            $this->checkOptionsExists($options);
        } else {
            $options = null;
        }
        return $this;
    }
}