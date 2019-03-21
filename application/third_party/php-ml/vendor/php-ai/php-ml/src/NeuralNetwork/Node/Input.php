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
declare (strict_types=1);
namespace Phpml\NeuralNetwork\Node;

use Phpml\NeuralNetwork\Node;
class Input implements Node
{
    /**
     * @var float
     */
    private $input;
    public function __construct(float $input = 0.0)
    {
        $this->input = $input;
    }
    public function getOutput() : float
    {
        return $this->input;
    }
    public function setInput(float $input) : void
    {
        $this->input = $input;
    }
}

?>