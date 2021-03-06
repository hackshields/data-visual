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
namespace Symfony\Component\Serializer\Encoder;

/**
 * Adds the support of an extra $context parameter for the supportsEncoding method.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ContextAwareEncoderInterface extends EncoderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param array $context options that encoders have access to
     */
    public function supportsEncoding($format, array $context = array());
}

?>