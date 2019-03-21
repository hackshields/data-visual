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
 * DenormalizerAware trait.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
trait DenormalizerAwareTrait
{
    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;
    /**
     * Sets the Denormalizer.
     *
     * @param DenormalizerInterface $denormalizer A DenormalizerInterface instance
     */
    public function setDenormalizer(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }
}

?>