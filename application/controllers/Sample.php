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
defined("BASEPATH") or exit("No direct script access.");
class Sample extends CI_Controller
{
    public function sso()
    {
        $creatorid = 5;
        $query = $this->db->select("email, name")->where("userid", $creatorid)->get("dc_user");
        $row = $query->row();
        $email = $row->email;
        $name = $row->name;
        $ssocallback = $this->input->get_post("ssocallback");
        $query = $this->db->select("value")->where("creatorid", $creatorid)->where("name", "sso_secret_token")->get("dc_user_options");
        if ($query->num_rows() == 0) {
            exit("Invalid sso login URL. Code: 10004");
        }
        $key = $query->row()->value;
        require APPPATH . "third_party/php-jwt/vendor/autoload.php";
        $token = array("email" => $email, "name" => $name);
        $jwt = Firebase\JWT\JWT::encode($token, $key);
        $this->load->helper("url");
        redirect($ssocallback . "?token=" . urlencode($jwt));
    }
    public function chain_tabular()
    {
        $result = array();
        $result[] = array("field1" => "data11", "field2" => "data12", "field3" => "data13");
        $result[] = array("field1" => "data21", "field2" => "data22", "field3" => "data23");
        $result[] = array("field1" => "data31", "field2" => "data32", "field3" => "data33");
        $result[] = array("field1" => "data41", "field2" => "data42", "field3" => "data43");
        $result[] = array("field1" => "data51", "field2" => "data52", "field3" => "data53");
        echo json_encode($result);
    }
    public function singlenumber()
    {
        $result = array("name" => "Text Label", "value" => "350000");
        echo json_encode($result);
    }
    public function testcsv()
    {
        require_once APPPATH . "third_party" . DIRECTORY_SEPARATOR . "parsecsv" . DIRECTORY_SEPARATOR . "parsecsv.lib.php";
        $csv = new ParseCsv\Csv();
        $csv->auto("e:/Active+Connections.csv");
        print_r($csv->titles);
    }
    public function chart()
    {
        $result = array();
        $result[] = array("date" => "2018-01-01", "value" => "100");
        $result[] = array("date" => "2018-01-02", "value" => "120");
        $result[] = array("date" => "2018-01-03", "value" => "140");
        $result[] = array("date" => "2018-01-04", "value" => "160");
        $result[] = array("date" => "2018-01-05", "value" => "180");
        $result[] = array("date" => "2018-01-06", "value" => "200");
        $result[] = array("date" => "2018-01-07", "value" => "500");
        echo json_encode($result);
    }
}

?>