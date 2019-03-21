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
namespace Rize\UriTemplate\Node;

/**
 * Description
 */
class Variable extends Abstraction
{
    /**
     * Variable name without modifier 
     * e.g. 'term:1' becomes 'term'
     */
    public $name, $options = array('modifier' => null, 'value' => null);
    public function __construct($token, array $options = array())
    {
        parent::__construct($token);
        $this->options = $options + $this->options;
        // normalize var name e.g. from 'term:1' becomes 'term'
        $name = $token;
        if ($options['modifier'] === ':') {
            $name = substr($name, 0, strpos($name, $options['modifier']));
        }
        $this->name = $name;
    }
}

?>