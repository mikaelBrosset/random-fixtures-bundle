<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class MappingClassNotFoundException extends \Exception
{
    public function __construct($className)
    {
        parent::__construct(sprintf("No mapping class %s found", $className));
    }
}