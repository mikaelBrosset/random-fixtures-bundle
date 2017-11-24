<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 06/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Exception;

class OutOfRangeException extends \OutOfRangeException
{
    public function __construct($start, $end, $name)
    {
        parent::__construct(sprintf("Int out of range for %s. It should happen between %d and %d!", $name, $start, $end));
    }
}