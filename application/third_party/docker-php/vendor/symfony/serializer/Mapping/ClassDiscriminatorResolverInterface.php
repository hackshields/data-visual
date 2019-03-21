<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Mapping;

/**
 * Knows how to get the class discriminator mapping for classes and objects.
 *
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
interface ClassDiscriminatorResolverInterface
{
    /**
     * @param string $class
     *
     * @return ClassDiscriminatorMapping|null
     */
    public function getMappingForClass(string $class) : ?ClassDiscriminatorMapping;
    /**
     * @param object|string $object
     *
     * @return ClassDiscriminatorMapping|null
     */
    public function getMappingForMappedObject($object) : ?ClassDiscriminatorMapping;
    /**
     * @param object|string $object
     *
     * @return string|null
     */
    public function getTypeForMappedObject($object) : ?string;
}

?>