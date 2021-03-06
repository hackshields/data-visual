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
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
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
namespace FacebookAds\Object;

use FacebookAds\ApiRequest;
use FacebookAds\Cursor;
use FacebookAds\Http\RequestInterface;
use FacebookAds\TypeChecker;
use FacebookAds\Object\Fields\HotelFields;
/**
 * This class is auto-generated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 */
class Hotel extends AbstractCrudObject
{
    /**
     * @deprecated getEndpoint function is deprecated
     */
    protected function getEndpoint()
    {
        return 'hotels';
    }
    /**
     * @return HotelFields
     */
    public static function getFieldsEnum()
    {
        return HotelFields::getInstance();
    }
    protected static function getReferencedEnums()
    {
        $ref_enums = array();
        return $ref_enums;
    }
    public function getHotelRooms(array $fields = array(), array $params = array(), $pending = false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new ApiRequest($this->api, $this->data['id'], RequestInterface::METHOD_GET, '/hotel_rooms', new HotelRoom(), 'EDGE', HotelRoom::getFieldsEnum()->getValues(), new TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function createHotelRoom(array $fields = array(), array $params = array(), $pending = false)
    {
        $this->assureId();
        $param_types = array('room_id' => 'string', 'description' => 'string', 'name' => 'string', 'url' => 'string', 'currency' => 'string', 'base_price' => 'float', 'applinks' => 'Object', 'images' => 'list<Object>', 'margin_level' => 'unsigned int', 'pricing_variables' => 'list<Object>', 'sale_price' => 'float');
        $enums = array();
        $request = new ApiRequest($this->api, $this->data['id'], RequestInterface::METHOD_POST, '/hotel_rooms', new HotelRoom(), 'EDGE', HotelRoom::getFieldsEnum()->getValues(), new TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function deleteSelf(array $fields = array(), array $params = array(), $pending = false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new ApiRequest($this->api, $this->data['id'], RequestInterface::METHOD_DELETE, '/', new AbstractCrudObject(), 'NODE', array(), new TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function getSelf(array $fields = array(), array $params = array(), $pending = false)
    {
        $this->assureId();
        $param_types = array();
        $enums = array();
        $request = new ApiRequest($this->api, $this->data['id'], RequestInterface::METHOD_GET, '/', new Hotel(), 'NODE', Hotel::getFieldsEnum()->getValues(), new TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
    public function updateSelf(array $fields = array(), array $params = array(), $pending = false)
    {
        $this->assureId();
        $param_types = array('address' => 'Object', 'brand' => 'string', 'description' => 'string', 'name' => 'string', 'url' => 'string', 'images' => 'list<Object>', 'currency' => 'string', 'base_price' => 'unsigned int', 'applinks' => 'Object', 'phone' => 'string', 'star_rating' => 'float', 'guest_ratings' => 'list<Object>');
        $enums = array();
        $request = new ApiRequest($this->api, $this->data['id'], RequestInterface::METHOD_POST, '/', new Hotel(), 'NODE', Hotel::getFieldsEnum()->getValues(), new TypeChecker($param_types, $enums));
        $request->addParams($params);
        $request->addFields($fields);
        return $pending ? $request : $request->execute();
    }
}

?>