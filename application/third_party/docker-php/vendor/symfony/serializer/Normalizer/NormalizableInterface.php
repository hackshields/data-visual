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
namespace Symfony\Component\Serializer\Normalizer;

/**
 * Defines the most basic interface a class must implement to be normalizable.
 *
 * If a normalizer is registered for the class and it doesn't implement
 * the Normalizable interfaces, the normalizer will be used instead.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface NormalizableInterface
{
    /**
     * Normalizes the object into an array of scalars|arrays.
     *
     * It is important to understand that the normalize() call should normalize
     * recursively all child objects of the implementor.
     *
     * @param NormalizerInterface $normalizer The normalizer is given so that you
     *                                        can use it to normalize objects contained within this object
     * @param string|null         $format     The format is optionally given to be able to normalize differently
     *                                        based on different output formats
     * @param array               $context    Options for normalizing this object
     *
     * @return array|string|int|float|bool
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array());
}

?>