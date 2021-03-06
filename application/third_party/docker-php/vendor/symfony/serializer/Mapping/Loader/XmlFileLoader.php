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
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Serializer\Mapping\Loader;

use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Serializer\Exception\MappingException;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
/**
 * Loads XML mapping files.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class XmlFileLoader extends FileLoader
{
    /**
     * An array of {@class \SimpleXMLElement} instances.
     *
     * @var \SimpleXMLElement[]|null
     */
    private $classes;
    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromXml();
        }
        if (!$this->classes) {
            return false;
        }
        $attributesMetadata = $classMetadata->getAttributesMetadata();
        if (isset($this->classes[$classMetadata->getName()])) {
            $xml = $this->classes[$classMetadata->getName()];
            foreach ($xml->attribute as $attribute) {
                $attributeName = (string) $attribute['name'];
                if (isset($attributesMetadata[$attributeName])) {
                    $attributeMetadata = $attributesMetadata[$attributeName];
                } else {
                    $attributeMetadata = new AttributeMetadata($attributeName);
                    $classMetadata->addAttributeMetadata($attributeMetadata);
                }
                foreach ($attribute->group as $group) {
                    $attributeMetadata->addGroup((string) $group);
                }
                if (isset($attribute['max-depth'])) {
                    $attributeMetadata->setMaxDepth((int) $attribute['max-depth']);
                }
            }
            if (isset($xml->{'discriminator-map'})) {
                $mapping = array();
                foreach ($xml->{'discriminator-map'}->mapping as $element) {
                    $mapping[(string) $element->attributes()->type] = (string) $element->attributes()->class;
                }
                $classMetadata->setClassDiscriminatorMapping(new ClassDiscriminatorMapping((string) $xml->{'discriminator-map'}->attributes()->{'type-property'}, $mapping));
            }
            return true;
        }
        return false;
    }
    /**
     * Return the names of the classes mapped in this file.
     *
     * @return string[] The classes names
     */
    public function getMappedClasses()
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromXml();
        }
        return array_keys($this->classes);
    }
    /**
     * Parses a XML File.
     *
     * @param string $file Path of file
     *
     * @return \SimpleXMLElement
     *
     * @throws MappingException
     */
    private function parseFile($file)
    {
        try {
            $dom = XmlUtils::loadFile($file, __DIR__ . '/schema/dic/serializer-mapping/serializer-mapping-1.0.xsd');
        } catch (\Exception $e) {
            throw new MappingException($e->getMessage(), $e->getCode(), $e);
        }
        return simplexml_import_dom($dom);
    }
    private function getClassesFromXml()
    {
        $xml = $this->parseFile($this->file);
        $classes = array();
        foreach ($xml->class as $class) {
            $classes[(string) $class['name']] = $class;
        }
        return $classes;
    }
}

?>