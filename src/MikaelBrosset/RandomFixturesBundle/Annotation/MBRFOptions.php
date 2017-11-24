<?php
/**
 * Author: Mikael Brosset
 * Email: mikael.brosset@gmail.com
 * Date: 07/11/17
 */
namespace MikaelBrosset\RandomFixturesBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @target("CLASS")
 */
class MBRFOptions
{
    const ordered = 'bool';
    const random = 'bool';
    const range = 'string';
    const regex = 'string';
    const fixedLength = 'int';
    const rangeLength = 'string';
    const randLength = 'bool';
    const upper = 'bool';
    const lower = 'bool';
    const cap = 'bool';
    const group = 'string';
}