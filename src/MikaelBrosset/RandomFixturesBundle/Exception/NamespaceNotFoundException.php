<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class NamespaceNotFoundException extends \Exception
{
    public function __construct($class)
    {
        parent::__construct(sprintf("Class not found for %s, class namespace MUST use PSR-4 standard", $class));
    }
}