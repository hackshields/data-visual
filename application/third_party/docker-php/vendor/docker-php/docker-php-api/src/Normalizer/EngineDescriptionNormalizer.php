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
class EngineDescriptionNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\EngineDescription';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\EngineDescription;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\EngineDescription();
        if (property_exists($data, 'EngineVersion') && $data->{'EngineVersion'} !== null) {
            $object->setEngineVersion($data->{'EngineVersion'});
        }
        if (property_exists($data, 'Labels') && $data->{'Labels'} !== null) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key => $value) {
                $values[$key] = $value;
            }
            $object->setLabels($values);
        }
        if (property_exists($data, 'Plugins') && $data->{'Plugins'} !== null) {
            $values_1 = [];
            foreach ($data->{'Plugins'} as $value_1) {
                $values_1[] = $this->denormalizer->denormalize($value_1, 'Docker\\API\\Model\\EngineDescriptionPluginsItem', 'json', $context);
            }
            $object->setPlugins($values_1);
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getEngineVersion()) {
            $data->{'EngineVersion'} = $object->getEngineVersion();
        }
        if (null !== $object->getLabels()) {
            $values = new \stdClass();
            foreach ($object->getLabels() as $key => $value) {
                $values->{$key} = $value;
            }
            $data->{'Labels'} = $values;
        }
        if (null !== $object->getPlugins()) {
            $values_1 = [];
            foreach ($object->getPlugins() as $value_1) {
                $values_1[] = $this->normalizer->normalize($value_1, 'json', $context);
            }
            $data->{'Plugins'} = $values_1;
        }
        return $data;
    }
}

?>