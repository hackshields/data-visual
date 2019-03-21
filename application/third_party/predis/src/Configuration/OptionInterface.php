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
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Predis\Configuration;

/**
 * Defines an handler used by Predis\Configuration\Options to filter, validate
 * or return default values for a given option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface OptionInterface
{
    /**
     * Filters and validates the passed value.
     *
     * @param OptionsInterface $options Options container.
     * @param mixed            $value   Input value.
     *
     * @return mixed
     */
    public function filter(OptionsInterface $options, $value);
    /**
     * Returns the default value for the option.
     *
     * @param OptionsInterface $options Options container.
     *
     * @return mixed
     */
    public function getDefault(OptionsInterface $options);
}

?>