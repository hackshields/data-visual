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
namespace Phpml\NeuralNetwork\Training\Backpropagation;

use Phpml\NeuralNetwork\Node\Neuron;
class Sigma
{
    /**
     * @var Neuron
     */
    private $neuron;
    /**
     * @var float
     */
    private $sigma;
    public function __construct(Neuron $neuron, float $sigma)
    {
        $this->neuron = $neuron;
        $this->sigma = $sigma;
    }
    public function getNeuron() : Neuron
    {
        return $this->neuron;
    }
    public function getSigma() : float
    {
        return $this->sigma;
    }
    public function getSigmaForNeuron(Neuron $neuron) : float
    {
        $sigma = 0.0;
        foreach ($this->neuron->getSynapses() as $synapse) {
            if ($synapse->getNode() == $neuron) {
                $sigma += $synapse->getWeight() * $this->getSigma();
            }
        }
        return $sigma;
    }
}

?>