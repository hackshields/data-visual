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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Command extends CI_Controller
{
    public function check_url()
    {
        $url = $this->input->get_post("url");
        if (extension_loaded("curl")) {
            $ch = curl_init();
            $headers = array();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $headers = curl_exec($ch);
            curl_close($ch);
            $data = array();
            $headers = explode(PHP_EOL, $headers);
            foreach ($headers as $row) {
                $parts = explode(":", $row);
                if (count($parts) === 2) {
                    $data[trim($parts[0])] = trim($parts[1]);
                }
            }
            echo json_encode($data);
        } else {
            stream_context_set_default(array("http" => array("method" => "HEAD")), array("https" => array("method" => "HEAD")));
            $headers = get_headers($url, 1);
            echo json_encode($headers);
        }
    }
    public function _check_command_api()
    {
        $enable_command_api = $this->config->item("enable_command_api");
        if (!$enable_command_api) {
            echo json_encode(array("error" => "Command API disabled."));
            return false;
        }
        $security_key = $this->config->item("command_security_key");
        if (empty($security_key)) {
            echo json_encode(array("error" => "Security key is empty."));
            return false;
        }
        $key = $this->input->get_post("k");
        if (empty($key) || $key !== $security_key) {
            echo json_encode(array("error" => "Not allowed, that is all we know."));
            return false;
        }
        return true;
    }
    public function create_account()
    {
        if (!$this->_check_command_api()) {
            return NULL;
        }
        $query = $this->db->query("select userid, plan from dc_user where permission=0 and creatorid=0");
        $creatorid = $query->row()->userid;
        $plan = $query->row()->plan;
        $username = trim($this->input->get_post("username"));
        $email = trim($this->input->get_post("email"));
        $password = $this->input->get_post("password");
        $permission = $this->input->get_post("permission");
        if ($permission == "developer") {
            $permission = 1;
        } else {
            $permission = 9;
        }
        $query = $this->db->query("select 1 from dc_user where name =?", array($username));
        if (0 < $query->num_rows()) {
            echo json_encode(array("error" => "username already exists"));
        } else {
            $query = $this->db->query("select 1 from dc_user where email =?", array($email));
            if (0 < $query->num_rows()) {
                echo json_encode(array("error" => "Email already exists"));
            } else {
                if (empty($password)) {
                    echo json_encode(array("error" => "Password should not be empty."));
                } else {
                    $this->db->insert("dc_user", array("creatorid" => $creatorid, "email" => $email, "name" => $username, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => $permission, "status" => 1, "regip" => $this->input->ip_address(), "regdate" => time(), "plan" => $plan, "expiredate" => time() + $this->config->item("trial_period_secs")));
                    $gen_userid = $this->db->insert_id();
                    echo json_encode(array("userid" => $gen_userid));
                }
            }
        }
    }
}

?>