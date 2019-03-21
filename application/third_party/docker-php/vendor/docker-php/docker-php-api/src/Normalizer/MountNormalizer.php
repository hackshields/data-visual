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
class MountNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\Mount';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\Mount;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\Mount();
        if (property_exists($data, 'Target') && $data->{'Target'} !== null) {
            $object->setTarget($data->{'Target'});
        }
        if (property_exists($data, 'Source') && $data->{'Source'} !== null) {
            $object->setSource($data->{'Source'});
        }
        if (property_exists($data, 'Type') && $data->{'Type'} !== null) {
            $object->setType($data->{'Type'});
        }
        if (property_exists($data, 'ReadOnly') && $data->{'ReadOnly'} !== null) {
            $object->setReadOnly($data->{'ReadOnly'});
        }
        if (property_exists($data, 'Consistency') && $data->{'Consistency'} !== null) {
            $object->setConsistency($data->{'Consistency'});
        }
        if (property_exists($data, 'BindOptions') && $data->{'BindOptions'} !== null) {
            $object->setBindOptions($this->denormalizer->denormalize($data->{'BindOptions'}, 'Docker\\API\\Model\\MountBindOptions', 'json', $context));
        }
        if (property_exists($data, 'VolumeOptions') && $data->{'VolumeOptions'} !== null) {
            $object->setVolumeOptions($this->denormalizer->denormalize($data->{'VolumeOptions'}, 'Docker\\API\\Model\\MountVolumeOptions', 'json', $context));
        }
        if (property_exists($data, 'TmpfsOptions') && $data->{'TmpfsOptions'} !== null) {
            $object->setTmpfsOptions($this->denormalizer->denormalize($data->{'TmpfsOptions'}, 'Docker\\API\\Model\\MountTmpfsOptions', 'json', $context));
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getTarget()) {
            $data->{'Target'} = $object->getTarget();
        }
        if (null !== $object->getSource()) {
            $data->{'Source'} = $object->getSource();
        }
        if (null !== $object->getType()) {
            $data->{'Type'} = $object->getType();
        }
        if (null !== $object->getReadOnly()) {
            $data->{'ReadOnly'} = $object->getReadOnly();
        }
        if (null !== $object->getConsistency()) {
            $data->{'Consistency'} = $object->getConsistency();
        }
        if (null !== $object->getBindOptions()) {
            $data->{'BindOptions'} = $this->normalizer->normalize($object->getBindOptions(), 'json', $context);
        }
        if (null !== $object->getVolumeOptions()) {
            $data->{'VolumeOptions'} = $this->normalizer->normalize($object->getVolumeOptions(), 'json', $context);
        }
        if (null !== $object->getTmpfsOptions()) {
            $data->{'TmpfsOptions'} = $this->normalizer->normalize($object->getTmpfsOptions(), 'json', $context);
        }
        return $data;
    }
}

?>