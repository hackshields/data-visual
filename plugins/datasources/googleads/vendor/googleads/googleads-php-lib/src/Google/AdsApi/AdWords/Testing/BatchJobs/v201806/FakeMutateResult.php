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
/**
 * Copyright 2016 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\AdsApi\AdWords\Testing\BatchJobs\v201806;

/**
 * A fake mutate result for testing purpose.
 */
class FakeMutateResult
{
    protected $errorList;
    protected $index;
    protected $result;
    /**
     * @return \Google\AdsApi\AdWords\Testing\BatchJobs\v201806\FakeErrorList
     */
    public function getErrorList()
    {
        return $this->errorList;
    }
    /**
     * @param \Google\AdsApi\AdWords\Testing\BatchJobs\v201806\FakeErrorList
     *     $errorList
     */
    public function setErrorList($errorList)
    {
        $this->errorList = $errorList;
    }
    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }
    /**
     * @param int $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }
    /**
     * @return \Google\AdsApi\AdWords\Testing\BatchJobs\v201806\FakeOperand
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * @param \Google\AdsApi\AdWords\Testing\BatchJobs\v201806\FakeOperand
     *     $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}

?>