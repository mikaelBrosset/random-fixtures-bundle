<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class ResourceNotFoundException extends \Exception
{
    public function __construct($resourceName, $resourceDir)
    {
        parent::__construct(sprintf("No resource file %s found in %s", $resourceName, $resourceDir));
    }
}