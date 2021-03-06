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
class SwarmSpecNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\SwarmSpec';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\SwarmSpec;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\SwarmSpec();
        if (property_exists($data, 'Name') && $data->{'Name'} !== null) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Labels') && $data->{'Labels'} !== null) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key => $value) {
                $values[$key] = $value;
            }
            $object->setLabels($values);
        }
        if (property_exists($data, 'Orchestration') && $data->{'Orchestration'} !== null) {
            $object->setOrchestration($this->denormalizer->denormalize($data->{'Orchestration'}, 'Docker\\API\\Model\\SwarmSpecOrchestration', 'json', $context));
        }
        if (property_exists($data, 'Raft') && $data->{'Raft'} !== null) {
            $object->setRaft($this->denormalizer->denormalize($data->{'Raft'}, 'Docker\\API\\Model\\SwarmSpecRaft', 'json', $context));
        }
        if (property_exists($data, 'Dispatcher') && $data->{'Dispatcher'} !== null) {
            $object->setDispatcher($this->denormalizer->denormalize($data->{'Dispatcher'}, 'Docker\\API\\Model\\SwarmSpecDispatcher', 'json', $context));
        }
        if (property_exists($data, 'CAConfig') && $data->{'CAConfig'} !== null) {
            $object->setCAConfig($this->denormalizer->denormalize($data->{'CAConfig'}, 'Docker\\API\\Model\\SwarmSpecCAConfig', 'json', $context));
        }
        if (property_exists($data, 'EncryptionConfig') && $data->{'EncryptionConfig'} !== null) {
            $object->setEncryptionConfig($this->denormalizer->denormalize($data->{'EncryptionConfig'}, 'Docker\\API\\Model\\SwarmSpecEncryptionConfig', 'json', $context));
        }
        if (property_exists($data, 'TaskDefaults') && $data->{'TaskDefaults'} !== null) {
            $object->setTaskDefaults($this->denormalizer->denormalize($data->{'TaskDefaults'}, 'Docker\\API\\Model\\SwarmSpecTaskDefaults', 'json', $context));
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getLabels()) {
            $values = new \stdClass();
            foreach ($object->getLabels() as $key => $value) {
                $values->{$key} = $value;
            }
            $data->{'Labels'} = $values;
        }
        if (null !== $object->getOrchestration()) {
            $data->{'Orchestration'} = $this->normalizer->normalize($object->getOrchestration(), 'json', $context);
        }
        if (null !== $object->getRaft()) {
            $data->{'Raft'} = $this->normalizer->normalize($object->getRaft(), 'json', $context);
        }
        if (null !== $object->getDispatcher()) {
            $data->{'Dispatcher'} = $this->normalizer->normalize($object->getDispatcher(), 'json', $context);
        }
        if (null !== $object->getCAConfig()) {
            $data->{'CAConfig'} = $this->normalizer->normalize($object->getCAConfig(), 'json', $context);
        }
        if (null !== $object->getEncryptionConfig()) {
            $data->{'EncryptionConfig'} = $this->normalizer->normalize($object->getEncryptionConfig(), 'json', $context);
        }
        if (null !== $object->getTaskDefaults()) {
            $data->{'TaskDefaults'} = $this->normalizer->normalize($object->getTaskDefaults(), 'json', $context);
        }
        return $data;
    }
}

?>