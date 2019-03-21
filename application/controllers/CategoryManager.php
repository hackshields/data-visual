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
class CategoryManager extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->smartyview->assign("categories", $this->_get_categories($creatorid));
        $this->smartyview->display("new/category.list.tpl");
    }
    public function del()
    {
        $categoryid = $this->input->post("cid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->delete("dc_category", array("creatorid" => $creatorid, "categoryid" => $categoryid));
        $this->db->update("dc_app", array("categoryid" => 0), array("creatorid" => $creatorid, "categoryid" => $categoryid));
        $categories = $this->_get_categories($creatorid);
        $this->load->library("smartyview");
        $this->smartyview->assign("categories", $categories);
        $this->smartyview->display("new/box.categories.table.tpl");
    }
    public function edit()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $cid = $this->input->post("pk");
        $cname = $this->input->post("value");
        if (!empty($cname)) {
            $this->load->database();
            $this->db->update("dc_category", array("name" => $cname), array("creatorid" => $creatorid, "categoryid" => $cid));
        }
        echo 1;
    }
    public function create2()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $name = $this->input->post("name");
        if (!empty($name)) {
            $query = $this->db->query("select 1 from dc_category where creatorid=? and name=?", array($creatorid, $name));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_category", array("creatorid" => $creatorid, "name" => $name));
            }
        }
        $categories = $this->_get_categories($creatorid);
        $this->smartyview->assign("categories", $categories);
        $this->smartyview->display("new/box.categories.table.tpl");
    }
    public function create()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $name = $this->input->post("name");
        if (!empty($name)) {
            $query = $this->db->query("select 1 from dc_category where creatorid=? and name=?", array($creatorid, $name));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_category", array("creatorid" => $creatorid, "name" => $name));
            }
        }
        $categories = $this->_get_categories($creatorid);
        $this->smartyview->assign("categories", $categories);
        $this->smartyview->assign("curcategoryid", $this->input->post("curcategoryid"));
        $this->smartyview->display("appbuilder/category.tpl");
    }
    public function update_permission()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $cid = $this->input->post("cid");
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "categoryid" => $cid))->get("dc_category");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0));
            } else {
                $query = $this->db->select("appid")->where(array("categoryid" => $cid, "creatorid" => $creatorid))->get("dc_app");
                $result_array = $query->result_array();
                foreach ($result_array as $row) {
                    $appid = $row["appid"];
                    $this->db->delete("dc_app_permission", array("appid" => $appid));
                    $users = $this->input->post("users");
                    if (!empty($users)) {
                        foreach ($users as $userid) {
                            $p = array("userid" => $userid, "appid" => $appid);
                            $this->db->insert("dc_app_permission", $p);
                        }
                    }
                }
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function show_users()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $this->load->library("smartyview");
            $this->smartyview->assign("builderboxstyle", "box box-solid");
            $this->smartyview->assign("hide_create", true);
            $cid = $this->input->post("cid");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("appid")->where(array("creatorid" => $creatorid, "categoryid" => $cid))->get("dc_app");
            $appid_in_cid = array();
            foreach ($query->result_array() as $app) {
                $appid_in_cid[] = $app["appid"];
            }
            $query = $this->db->query("select userid, name from dc_user where creatorid = ? and permission = 9", array($creatorid));
            if ($query && 0 < $query->num_rows()) {
                $users = $query->result_array();
                foreach ($users as &$user) {
                    $userid = $user["userid"];
                    $query = $this->db->select("appid")->where("userid", $userid)->get("dc_app_permission");
                    $appid_for_user = array();
                    foreach ($query->result_array() as $row) {
                        $appid_for_user[] = $row["appid"];
                    }
                    if (0 == count(array_diff($appid_in_cid, $appid_for_user))) {
                        $user["permission"] = 1;
                    } else {
                        $user["permission"] = 0;
                    }
                }
                $this->smartyview->assign("users", $users);
            }
            $this->smartyview->display("new/category.userpermission.tpl");
        }
    }
    public function update_order()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => 0));
        } else {
            $cids = $this->input->post("cids");
            if (empty($cids)) {
                echo json_encode(array("result" => 0));
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $sortIdx = 1;
                foreach ($cids as $cid) {
                    $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "categoryid" => $cid))->get("dc_category");
                    if ($query->num_rows() == 1) {
                        $this->db->update("dc_category", array("sort" => $sortIdx), array("creatorid" => $creatorid, "categoryid" => $cid));
                        $sortIdx++;
                    }
                }
                echo json_encode(array("result" => 1));
            }
        }
    }
    public function update_app_order()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appids = $this->input->post("appids");
            if (empty($appids)) {
                echo json_encode(array("result" => 0));
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $sortIdx = 1;
                foreach ($appids as $appid) {
                    $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
                    if ($query->num_rows() == 1) {
                        $this->db->update("dc_app", array("sort" => $sortIdx), array("creatorid" => $creatorid, "appid" => $appid));
                        $sortIdx++;
                    }
                }
                echo json_encode(array("result" => 1));
            }
        }
    }
    public function list_app()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $cid = $this->input->get("cid");
        $select = "appid, connid, type, name, title, format, categoryid, status";
        $this->db->select($select);
        $this->db->where("creatorid", $creatorid);
        $this->db->where("categoryid", $cid);
        $query = $this->db->get("dc_app");
        $this->load->library("smartyview");
        $apps = $query->result_array();
        $this->smartyview->assign("apps", $apps);
        $this->smartyview->display("new/category.app.list.tpl");
    }
}

?>