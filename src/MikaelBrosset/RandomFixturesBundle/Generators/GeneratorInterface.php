<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 08/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

interface GeneratorInterface
{
    /**
     * Returns a processed random value according to the parameters (ex: some random firstnames)
     *
     * @param int $null
     * @param null $option
     * @return mixed
     */
    public function getValue($null = 0, $option = null);
}