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
 * Copyright (c) 2014-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */
namespace FacebookAdsTest\Object;

use FacebookAds\Object\CustomAudienceMultiKey;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Fields\CustomAudienceMultikeySchemaFields;
use FacebookAds\Object\Values\CustomAudienceSubtypes;
use FacebookAds\Object\Values\CustomAudienceTypes;
class CustomAudienceMultikeyTest extends AbstractCrudObjectTestCase
{
    protected $customaudience;
    public function setup()
    {
        parent::setup();
        $ca = new CustomAudienceMultiKey(null, $this->getConfig()->accountId);
        $ca->{CustomAudienceFields::NAME} = $this->getConfig()->testRunId;
        $ca->{CustomAudienceFields::SUBTYPE} = CustomAudienceSubtypes::CUSTOM;
        $ca->create();
        $this->customaudience = $ca;
    }
    public function tearDown()
    {
        if ($this->customaudience) {
            $this->customaudience->delete();
        }
    }
    private function checkServerResponse(CustomAudienceMultiKey $ca, array $users, array $schema, $is_hashed, $is_normalized)
    {
        $add = $ca->addUsers($users, $schema, $is_hashed, $is_normalized);
        $this->assertClusterChangesResponse($ca, $users, $add);
        $remove = $ca->removeUsers($users, $schema, $is_hashed, $is_normalized);
        $this->assertClusterChangesResponse($ca, $users, $remove);
    }
    protected function assertClusterChangesResponse(CustomAudienceMultiKey $ca, array $users, $response)
    {
        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('audience_id', $response);
        $this->assertEquals($response['audience_id'], $ca->{CustomAudienceFields::ID});
        $this->assertArrayHasKey('num_received', $response);
        $this->assertEquals($response['num_received'], count($users));
        $this->assertArrayHasKey('num_invalid_entries', $response);
        $this->assertEquals($response['num_invalid_entries'], 0);
    }
    public function testMultikeyCustomAudiences()
    {
        $users = array(array('abc123def', 'fname', 'lname', 'someone@example.com'), array('abc234def', 'fname_new', 'lname_new', 'someone_new@example.com'));
        $schema = array(CustomAudienceMultikeySchemaFields::EXTERN_ID, CustomAudienceMultikeySchemaFields::FIRST_NAME, CustomAudienceMultikeySchemaFields::LAST_NAME, CustomAudienceMultikeySchemaFields::EMAIL);
        $is_hashed = false;
        $is_normalized = false;
        $this->checkServerResponse($this->customaudience, $users, $schema, $is_hashed, $is_normalized);
    }
    public function testSinglekeyExternIDCustomAudiences()
    {
        $users = array(array('abc123def'), array('abc234def'), array('abc345def'), array('abc456def'));
        $schema = array(CustomAudienceMultikeySchemaFields::EXTERN_ID);
        $is_hashed = false;
        $is_normalized = false;
        $this->checkServerResponse($this->customaudience, $users, $schema, $is_hashed, $is_normalized);
    }
}

?>