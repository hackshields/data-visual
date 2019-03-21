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
require APPPATH . "/libraries/REST_Controller.php";
class DbFaceAPI extends REST_Controller
{
    protected $methods = array("get_market_items" => array("level" => 10, "limit" => 10), "search_market_item" => array("level" => 10), "get_market_item" => array("level" => 10), "remove_market_item" => array("level" => 10), "publish_market_item" => array("level" => 10), "get_market_tags" => array("level" => 10), "follow_market_item" => array("level" => 10), "market_item_info" => array("level" => 10), "market_item_detail" => array("level" => 10));
    public function _initialize_vendor_profile()
    {
        $userinfo = $this->input->post("vendor");
        if (!$userinfo) {
            return false;
        }
        $userinfo = json_decode($userinfo, true);
        $email = $userinfo["email"];
        $clientcode = $userinfo["clientcode"];
        $name = $userinfo["username"];
        $avatar = $userinfo["avatar"];
        $linked_userid = $userinfo["local_userid"];
        $refer = $userinfo["refer"];
        if (empty($clientcode) || empty($name)) {
            return false;
        }
        if (empty($avatar)) {
            $avatar = $this->config->item("df.static") . "/libs/mp/no-avatar.jpg";
        }
        $query = $this->db->select("1")->where(array("clientcode" => $clientcode))->get("dc_market_vendor");
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_market_vendor", array("name" => $name, "email" => $email, "avatar" => $avatar, "clientcode" => $clientcode, "refer" => $refer, "userid" => $linked_userid, "date" => time()));
        }
        $query = $this->db->where(array("clientcode" => $clientcode))->get("dc_market_vendor");
        return $query->row_array();
    }
    public function remove_market_item_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "INvalid vendor profile"));
        } else {
            $itemkey = $this->input->post("itemkey");
            $query = $this->db->select("creatorid")->where(array("itemkey" => $itemkey))->get("dc_market_item");
            if ($query->num_rows() == 1) {
                if ($query->row()->creatorid != $userinfo["creatorid"]) {
                    $this->response(array("status" => 0, "message" => "Permission Denied"));
                } else {
                    $this->db->delete("dc_market_item", array("itemkey" => $itemkey));
                    $this->db->delete("dc_market_tag", array("itemkey" => $itemkey));
                    $this->db->delete("dc_market_follow", array("itemkey" => $itemkey));
                    $this->db->delete("dc_market_attachment", array("itemkey" => $itemkey));
                    $this->response(array("status" => 1, "itemkey" => $itemkey));
                }
            } else {
                $this->response(array("status" => 0));
            }
        }
    }
    /**
     * get all marketplace items, will auto create vendor profile based on the REST request
     */
    public function get_market_items_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "Invalid vendor profile"));
        } else {
            $query = $this->db->get("dc_market_item");
            $items = $query->result_array();
            foreach ($items as &$item) {
                $creatorid = $item["creatorid"];
                $userinfo = $this->_get_userinfo($creatorid);
                $follownum = $this->_get_follow_num($item["itemkey"]);
                $item["creator"] = $userinfo;
                $item["liked_num"] = $follownum;
            }
            $this->response($items);
        }
    }
    public function market_item_info_post()
    {
        $itemkey = $this->input->post("itemkey");
        $query = $this->db->where("itemkey", $itemkey)->get("dc_market_item");
        $item = $query->row_array();
        $this->response($item);
    }
    public function get_market_item_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "INvalid vendor profile"));
        } else {
            $itemkey = $this->input->post("itemkey");
            $creatorid = $userinfo["creatorid"];
            $this->load->library("smartyview");
            $query = $this->db->where("itemkey", $itemkey)->get("dc_market_item");
            $item = $query->row_array();
            $is_owner = $item["creatorid"] == $creatorid;
            $description = $item["description"];
            require_once APPPATH . "libraries/Parsedown.php";
            $Parsedown = new Parsedown();
            $compiled_description = $Parsedown->text($description);
            $this->smartyview->assign("description", $compiled_description);
            $this->smartyview->assign("item", $item);
            $query = $this->db->select("tag")->where("itemkey", $itemkey)->get("dc_market_tag");
            $tags = $query->result_array();
            $this->smartyview->assign("tags", $tags);
            $query = $this->db->select("1")->where(array("itemkey" => $itemkey, "creatorid" => $item["creatorid"]))->get("dc_market_follow");
            $is_liked = 0 < $query->num_rows();
            $this->smartyview->assign("is_liked", $is_liked);
            $this->smartyview->assign("is_owner", $is_owner);
            $result = $this->smartyview->fetch("marketplace/marketitem.detail.content.tpl");
            $this->response($result);
        }
    }
    public function search_market_item_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "INvalid vendor profile"));
        } else {
            $owner = $this->input->post("owner");
            $type = $this->input->post("type");
            $tag = $this->input->post("tag");
            $u = $this->input->get_post("u");
            if (!empty($u)) {
                $this->db->where("creatorid", $u);
                $query = $this->db->get("dc_market_item");
                $items = $query->result_array();
            } else {
                if (empty($tag)) {
                    if ($type != "all") {
                        $this->db->where("type", $type);
                    }
                    if ($owner == "my") {
                        $creatorid = $userinfo["creatorid"];
                        $this->db->where("creatorid", $creatorid);
                        $query = $this->db->get("dc_market_item");
                        $items = $query->result_array();
                    } else {
                        if ($owner == "liked") {
                            $creatorid = $userinfo["creatorid"];
                            $this->db->select("dc_market_item.itemkey, dc_market_item.creatorid, dc_market_item.name, dc_market_item.type, dc_market_item.thumb, dc_market_item.summary, dc_market_item.description, dc_market_item.price, dc_market_item.status, dc_market_item.target, dc_market_item.createdate, dc_market_item.updatedate");
                            $this->db->from("dc_market_follow");
                            $this->db->join("dc_market_item", "dc_market_follow.itemkey = dc_market_item.itemkey");
                            $this->db->like("dc_market_follow.creatorid", $creatorid);
                            $query = $this->db->get();
                            $items = $query->result_array();
                        } else {
                            $query = $this->db->get("dc_market_item");
                            $items = $query->result_array();
                        }
                    }
                } else {
                    $this->db->select("dc_market_item.itemkey, dc_market_item.creatorid, dc_market_item.name, dc_market_item.type, dc_market_item.thumb, dc_market_item.summary, dc_market_item.description, dc_market_item.price, dc_market_item.status, dc_market_item.target, dc_market_item.createdate, dc_market_item.updatedate");
                    $this->db->from("dc_market_tag");
                    $this->db->join("dc_market_item", "dc_market_tag.itemkey = dc_market_item.itemkey");
                    $this->db->like("dc_market_tag.tag", $tag);
                    $query = $this->db->get();
                    $items = $query->result_array();
                }
            }
            foreach ($items as &$item) {
                $creatorid = $item["creatorid"];
                $userinfo = $this->_get_userinfo($creatorid);
                $follownum = $this->_get_follow_num($item["itemkey"]);
                $item["creator"] = $userinfo;
                $item["liked_num"] = $follownum;
            }
            $this->response($items);
        }
    }
    public function publish_market_item_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "INvalid vendor profile"));
        } else {
            $creatorid = $userinfo["creatorid"];
            $do = $this->input->post("do");
            $appid = $this->input->post("appid");
            $code = $this->input->post("code");
            $variable = $this->input->post("variable");
            $pid = $this->input->post("pid");
            $tpl = $this->input->post("tpl");
            $raw = $this->input->post("raw");
            $itemkey = $this->input->post("itemkey");
            $indialog = $this->input->post("indialog") == "1";
            $target = "";
            $type = "";
            $this->load->library("smartyview");
            if (!empty($appid)) {
                $this->smartyview->assign("appid", $appid);
                $target = $appid;
                $type = "app";
            }
            if (!empty($code)) {
                $this->smartyview->assign("code", $code);
                $target = $code;
                $type = "code";
            }
            if (!empty($variable)) {
                $this->smartyview->assign("variable", $variable);
                $target = $variable;
                $type = "variable";
            }
            if (!empty($pid)) {
                $this->smartyview->assign("pid", $pid);
                $target = $pid;
                $type = "product";
            }
            if (!empty($tpl)) {
                $this->smartyview->assign("tpl", $tpl);
                $target = $tpl;
                $type = "template";
            }
            if (!empty($raw)) {
                $this->smartyview->assign("raw", $raw);
                $target = "param1";
                $type = $raw;
            }
            if ($do == "confirm") {
                $itemkey = $this->input->post("itemkey");
                $name = $this->input->post("name");
                $summary = $this->input->post("summary");
                $description = $this->input->post("description");
                $thumbnail = $this->input->post("thumbnail");
                $regularPrice = $this->input->post("regularPrice");
                $type = $this->input->post("type");
                $target = $this->input->post("target");
                $param1 = $this->input->post("param1");
                $creatorid = $userinfo["creatorid"];
                $thumbnail = $this->input->post("thumbnail");
                $tags = $this->input->post("tags");
                $screenshots = $this->input->post("screenshots");
                $query = $this->db->select("1")->where("itemkey", $itemkey)->get("dc_market_item");
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_market_item", array("itemkey" => $itemkey, "type" => $type, "name" => $name, "summary" => $summary, "description" => $description, "thumb" => $thumbnail, "price" => $regularPrice, "target" => $target, "param1" => $param1, "creatorid" => $creatorid, "createdate" => time(), "updatedate" => time()));
                } else {
                    $this->db->update("dc_market_item", array("name" => $name, "type" => $type, "summary" => $summary, "description" => $description, "thumb" => $thumbnail, "price" => $regularPrice, "target" => $target, "param1" => $param1, "updatedate" => time()), array("itemkey" => $itemkey, "creatorid" => $creatorid));
                }
                $tags = array_unique(explode(",", $tags));
                $this->db->delete("dc_market_tag", array("itemkey" => $itemkey));
                $arr_tags = array();
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $arr_tags[] = array("itemkey" => $itemkey, "tag" => $tag, "date" => time());
                    }
                }
                if (0 < count($arr_tags)) {
                    insert_batch($this->db, "dc_market_tag", $arr_tags);
                }
                $screenshots = array_unique(preg_split("/\\r\\n|[\\r\\n]/", $screenshots));
                $this->db->delete("dc_market_screenshot", array("itemkey" => $itemkey));
                $arr_screenshots = array();
                foreach ($screenshots as $screenshot) {
                    if (!empty($screenshot)) {
                        $arr_screenshots[] = array("itemkey" => $itemkey, "image" => $screenshot, "date" => time());
                    }
                }
                if (0 < count($arr_screenshots)) {
                    insert_batch($this->db, "dc_market_screenshot", $arr_screenshots);
                }
                $attachment = $this->input->post("attach");
                if (!empty($attachment)) {
                    $query = $this->db->select("1")->where("itemkey", $itemkey)->get("dc_market_attachment");
                    if (0 < $query->num_rows()) {
                        $this->db->update("dc_market_attachment", array("content" => $attachment, "updatedate" => time()), array("itemkey" => $itemkey, "creatorid" => $creatorid));
                    } else {
                        $this->db->insert("dc_market_attachment", array("itemkey" => $itemkey, "creatorid" => $creatorid, "content" => $attachment, "createdate" => time(), "updatedate" => time()));
                    }
                }
                $this->response(array("status" => 1));
                return NULL;
            } else {
                if (!empty($itemkey)) {
                    $this->smartyview->assign("itemkey", $itemkey);
                    $target = $itemkey;
                    $type = "item";
                }
                if ($type == "item") {
                    $query = $this->db->select("*")->where(array("creatorid" => $creatorid, "itemkey" => $target))->get("dc_market_item");
                } else {
                    if ($target != "param1") {
                        $query = $this->db->select("*")->where(array("creatorid" => $creatorid, "target" => $target, "type" => $type))->get("dc_market_item");
                    }
                }
                if ($target == "param1" || $query->num_rows() == 0) {
                    $itemkey = uniqid("mp");
                } else {
                    $row = $query->row_array();
                    $itemkey = $row["itemkey"];
                    $this->smartyview->assign("market_item", $row);
                    $type = $row["type"];
                    $target = $row["target"];
                    if ($type == "app") {
                        $this->smartyview->assign("appid", $target);
                    } else {
                        if ($type == "code") {
                            $this->smartyview->assign("code", $target);
                        } else {
                            if ($type == "variable") {
                                $this->smartyview->assign("variable", $target);
                            } else {
                                if ($type == "product") {
                                    $this->smartyview->assign("pid", $target);
                                } else {
                                    if ($type == "tpl") {
                                        $this->smartyview->assign("tpl", $target);
                                    } else {
                                        $this->smartyview->assign("raw", $raw);
                                    }
                                }
                            }
                        }
                    }
                    $query = $this->db->select("tag")->where("itemkey", $itemkey)->get("dc_market_tag");
                    $tags = $query->result_array();
                    $arr_tags = array();
                    foreach ($tags as $tag) {
                        $arr_tags[] = $tag["tag"];
                    }
                    $this->smartyview->assign("tags", implode(",", $arr_tags));
                    $query = $this->db->select("image")->where("itemkey", $itemkey)->get("dc_market_screenshot");
                    $screenshots = $query->result_array();
                    $arr_screenshots = array();
                    foreach ($screenshots as $screenshot) {
                        $arr_screenshots[] = $screenshot["image"];
                    }
                    $this->smartyview->assign("screenshots", implode("&#13;&#10;", $arr_screenshots));
                }
                $this->smartyview->assign("itemkey", $itemkey);
                if ($indialog) {
                    $this->smartyview->assign("title", "Publish to Exchange Marketplace");
                    $output = $this->smartyview->fetch("marketplace/marketplace.publish.dialog.tpl");
                } else {
                    $output = $this->smartyview->fetch("marketplace/marketitem.create.tpl");
                }
                $this->response($output);
            }
        }
    }
    public function get_market_tags_post()
    {
        $max = $this->input->post("maxnum");
        $query = $this->db->select("tag")->distinct()->limit(is_number($max) ? $max : 20)->get("dc_market_tag");
        $tags = array();
        foreach ($query->result_array() as $row) {
            $tags[] = $row["tag"];
        }
        $this->response($tags);
    }
    public function follow_market_item_post()
    {
        $userinfo = $this->_initialize_vendor_profile();
        if (!$userinfo) {
            $this->response(array("status" => 0, "message" => "Invalid vendor profile"));
        } else {
            $itemkey = $this->input->post("itemkey");
            $creatorid = $userinfo["creatorid"];
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "itemkey" => $itemkey))->get("dc_market_follow");
            if (0 < $query->num_rows()) {
                $this->db->delete("dc_market_follow", array("creatorid" => $creatorid, "itemkey" => $itemkey));
                $this->response(array("status" => 2));
            } else {
                $this->db->insert("dc_market_follow", array("creatorid" => $creatorid, "itemkey" => $itemkey, "date" => time()));
                $this->response(array("status" => 1));
            }
        }
    }
    public function market_item_detail_post()
    {
        $itemkey = $this->input->post("itemkey");
        $query = $this->db->where("itemkey", $itemkey)->get("dc_market_item");
        $data = $query->row_array();
        $query = $this->db->where("itemkey", $itemkey)->get("dc_market_screenshot");
        $screenshots = $query->result_array();
        $arr_screenshots = array();
        foreach ($screenshots as $screenshot) {
            $arr_screenshots[] = $screenshot["image"];
        }
        $query = $this->db->where("itemkey", $itemkey)->get("dc_market_attachment");
        $content = $query->row()->content;
        $data["screenshots"] = $arr_screenshots;
        $data["attachment"] = $content;
        $this->response($data);
    }
    public function _get_userinfo($creatorid)
    {
        $query = $this->db->select("name,avatar")->where("creatorid", $creatorid)->get("dc_market_vendor");
        $userinfo = array();
        if ($query->num_rows() == 1) {
            $userinfo["uid"] = $creatorid;
            $userinfo["name"] = $query->row()->name;
            $avatar = $query->row()->avatar;
            if (empty($avatar)) {
                $avatar = $this->config->item("df.static") . "/libs/mp/no-avatar.jpg";
            }
            $userinfo["avatar"] = $avatar;
        }
        return $userinfo;
    }
    public function _get_follow_num($itemkey)
    {
        $query = $this->db->select("count(creatorid) as num")->where("itemkey", $itemkey)->get("dc_market_follow");
        return $query->row()->num;
    }
}

?>