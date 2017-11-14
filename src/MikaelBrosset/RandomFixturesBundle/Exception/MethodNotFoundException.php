<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class MethodNotFoundException extends \Exception
{
    public function __construct($methodName, $className)
    {
        parent::__construct(sprintf("Public method \"%s()\" not found in %s", $methodName, $className));
    }
}