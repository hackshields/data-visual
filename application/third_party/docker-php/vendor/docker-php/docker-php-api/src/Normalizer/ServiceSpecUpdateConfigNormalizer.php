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
/*
 * This file has been auto generated by Jane,
 *
 * Do no edit it directly.
 */
namespace Docker\API\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class ServiceSpecUpdateConfigNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\ServiceSpecUpdateConfig';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\ServiceSpecUpdateConfig;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\ServiceSpecUpdateConfig();
        if (property_exists($data, 'Parallelism') && $data->{'Parallelism'} !== null) {
            $object->setParallelism($data->{'Parallelism'});
        }
        if (property_exists($data, 'Delay') && $data->{'Delay'} !== null) {
            $object->setDelay($data->{'Delay'});
        }
        if (property_exists($data, 'FailureAction') && $data->{'FailureAction'} !== null) {
            $object->setFailureAction($data->{'FailureAction'});
        }
        if (property_exists($data, 'Monitor') && $data->{'Monitor'} !== null) {
            $object->setMonitor($data->{'Monitor'});
        }
        if (property_exists($data, 'MaxFailureRatio') && $data->{'MaxFailureRatio'} !== null) {
            $object->setMaxFailureRatio($data->{'MaxFailureRatio'});
        }
        if (property_exists($data, 'Order') && $data->{'Order'} !== null) {
            $object->setOrder($data->{'Order'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getParallelism()) {
            $data->{'Parallelism'} = $object->getParallelism();
        }
        if (null !== $object->getDelay()) {
            $data->{'Delay'} = $object->getDelay();
        }
        if (null !== $object->getFailureAction()) {
            $data->{'FailureAction'} = $object->getFailureAction();
        }
        if (null !== $object->getMonitor()) {
            $data->{'Monitor'} = $object->getMonitor();
        }
        if (null !== $object->getMaxFailureRatio()) {
            $data->{'MaxFailureRatio'} = $object->getMaxFailureRatio();
        }
        if (null !== $object->getOrder()) {
            $data->{'Order'} = $object->getOrder();
        }
        return $data;
    }
}

?>