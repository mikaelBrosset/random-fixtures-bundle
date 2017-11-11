<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

use Exception;

class ListNotFoundException extends \Exception
{
    public function __construct($class)
    {
        parent::__construct(sprintf("List not found for %s", $class));
    }
}