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
class TaskSpecContainerSpecSecretsItemNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Docker\\API\\Model\\TaskSpecContainerSpecSecretsItem';
    }
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Docker\API\Model\TaskSpecContainerSpecSecretsItem;
    }
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!is_object($data)) {
            return null;
        }
        $object = new \Docker\API\Model\TaskSpecContainerSpecSecretsItem();
        if (property_exists($data, 'File') && $data->{'File'} !== null) {
            $object->setFile($this->denormalizer->denormalize($data->{'File'}, 'Docker\\API\\Model\\TaskSpecContainerSpecSecretsItemFile', 'json', $context));
        }
        if (property_exists($data, 'SecretID') && $data->{'SecretID'} !== null) {
            $object->setSecretID($data->{'SecretID'});
        }
        if (property_exists($data, 'SecretName') && $data->{'SecretName'} !== null) {
            $object->setSecretName($data->{'SecretName'});
        }
        return $object;
    }
    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getFile()) {
            $data->{'File'} = $this->normalizer->normalize($object->getFile(), 'json', $context);
        }
        if (null !== $object->getSecretID()) {
            $data->{'SecretID'} = $object->getSecretID();
        }
        if (null !== $object->getSecretName()) {
            $data->{'SecretName'} = $object->getSecretName();
        }
        return $data;
    }
}

?>