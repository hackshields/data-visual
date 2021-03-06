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
class NetworksCreatePostBodyNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\NetworksCreatePostBody';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\NetworksCreatePostBody;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\NetworksCreatePostBody();
        if (property_exists($data, 'Name') && $data->{'Name'} !== null) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'CheckDuplicate') && $data->{'CheckDuplicate'} !== null) {
            $object->setCheckDuplicate($data->{'CheckDuplicate'});
        }
        if (property_exists($data, 'Driver') && $data->{'Driver'} !== null) {
            $object->setDriver($data->{'Driver'});
        }
        if (property_exists($data, 'Internal') && $data->{'Internal'} !== null) {
            $object->setInternal($data->{'Internal'});
        }
        if (property_exists($data, 'Attachable') && $data->{'Attachable'} !== null) {
            $object->setAttachable($data->{'Attachable'});
        }
        if (property_exists($data, 'Ingress') && $data->{'Ingress'} !== null) {
            $object->setIngress($data->{'Ingress'});
        }
        if (property_exists($data, 'IPAM') && $data->{'IPAM'} !== null) {
            $object->setIPAM($this->denormalizer->denormalize($data->{'IPAM'}, 'Docker\\API\\Model\\IPAM', 'json', $context));
        }
        if (property_exists($data, 'EnableIPv6') && $data->{'EnableIPv6'} !== null) {
            $object->setEnableIPv6($data->{'EnableIPv6'});
        }
        if (property_exists($data, 'Options') && $data->{'Options'} !== null) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Options'} as $key => $value) {
                $values[$key] = $value;
            }
            $object->setOptions($values);
        }
        if (property_exists($data, 'Labels') && $data->{'Labels'} !== null) {
            $values_1 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_1 => $value_1) {
                $values_1[$key_1] = $value_1;
            }
            $object->setLabels($values_1);
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getCheckDuplicate()) {
            $data->{'CheckDuplicate'} = $object->getCheckDuplicate();
        }
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        if (null !== $object->getInternal()) {
            $data->{'Internal'} = $object->getInternal();
        }
        if (null !== $object->getAttachable()) {
            $data->{'Attachable'} = $object->getAttachable();
        }
        if (null !== $object->getIngress()) {
            $data->{'Ingress'} = $object->getIngress();
        }
        if (null !== $object->getIPAM()) {
            $data->{'IPAM'} = $this->normalizer->normalize($object->getIPAM(), 'json', $context);
        }
        if (null !== $object->getEnableIPv6()) {
            $data->{'EnableIPv6'} = $object->getEnableIPv6();
        }
        if (null !== $object->getOptions()) {
            $values = new \stdClass();
            foreach ($object->getOptions() as $key => $value) {
                $values->{$key} = $value;
            }
            $data->{'Options'} = $values;
        }
        if (null !== $object->getLabels()) {
            $values_1 = new \stdClass();
            foreach ($object->getLabels() as $key_1 => $value_1) {
                $values_1->{$key_1} = $value_1;
            }
            $data->{'Labels'} = $values_1;
        }
        return $data;
    }
}

?>