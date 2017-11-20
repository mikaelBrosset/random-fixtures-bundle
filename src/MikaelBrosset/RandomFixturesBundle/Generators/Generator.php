<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 20/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Generators;

use MikaelBrosset\RandomFixturesBundle\Exception\ResourceNotFoundException;

class Generator
{
    private $name = 'std';
    private $resourcePath;
    private $resource;
    private $leader = 0;

    public function setName($name) : Generator
    {
        $this->name = $name;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setResourcePath($path) : Generator
    {
        $this->name = $path;
        return $this;
    }

    public function getResourcePath() : array
    {
        return $this->resourcePath;
    }

    public function getResource($resource) : Generator
    {
        if (!is_resource($resource)) { throw new ResourceNotFoundException($this->name, $this->resourcePath); }
        $this->resource = $resource;
        return $this;
    }

    public function setLeader($status) :  Generator
    {
        $this->leader = $status;
    }

    public function isLeader() : bool
    {
        return $this->leader;
    }
}