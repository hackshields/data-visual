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
 * Copyright 2015-2017 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace MongoDB\Model;

/**
 * Collection information model class.
 *
 * This class models the collection information returned by the listCollections
 * command or, for legacy servers, queries on the "system.namespaces"
 * collection. It provides methods to access options for the collection.
 *
 * @api
 * @see \MongoDB\Database::listCollections()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-collections.rst
 */
class CollectionInfo
{
    private $info;
    /**
     * Constructor.
     *
     * @param array $info Collection info
     */
    public function __construct(array $info)
    {
        $this->info = $info;
    }
    /**
     * Return the collection info as an array.
     *
     * @see http://php.net/oop5.magic#language.oop5.magic.debuginfo
     * @return array
     */
    public function __debugInfo()
    {
        return $this->info;
    }
    /**
     * Return the maximum number of documents to keep in the capped collection.
     *
     * @return integer|null
     */
    public function getCappedMax()
    {
        return isset($this->info['options']['max']) ? (int) $this->info['options']['max'] : null;
    }
    /**
     * Return the maximum size (in bytes) of the capped collection.
     *
     * @return integer|null
     */
    public function getCappedSize()
    {
        return isset($this->info['options']['size']) ? (int) $this->info['options']['size'] : null;
    }
    /**
     * Return the collection name.
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->info['name'];
    }
    /**
     * Return the collection options.
     *
     * @return array
     */
    public function getOptions()
    {
        return isset($this->info['options']) ? (array) $this->info['options'] : [];
    }
    /**
     * Return whether the collection is a capped collection.
     *
     * @return boolean
     */
    public function isCapped()
    {
        return !empty($this->info['options']['capped']);
    }
}

?>