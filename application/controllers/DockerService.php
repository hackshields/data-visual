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
require_once APPPATH . "third_party/docker-php/vendor/autoload.php";
class DockerService extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $is_master = $this->config->item("dbface_master_host");
        if (empty($is_master)) {
            exit("Not master installation");
        }
    }
    public function findall()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 1, "message" => "invalid permission"));
        } else {
            $docker = $this->_get_docker_client();
            $containers = $docker->containerList(array("all" => true));
            $this->load->library("smartyview");
            $this->smartyview->assign("containers", $containers);
            $this->smartyview->display("docker/docker.list.tpl");
        }
    }
    public function stop()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 1, "message" => "invalid permission"));
        } else {
            $container_id = $this->input->post("id");
            if (empty($container_id)) {
                echo json_encode(array("status" => 1, "message" => "invalid container name"));
            } else {
                $docker = $this->_get_docker_client();
                try {
                    $docker->containerStop($container_id);
                    echo json_encode(array("status" => 1));
                } catch (Http\Client\Common\Exception\ClientErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                } catch (Http\Client\Common\Exception\ServerErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                }
            }
        }
    }
    public function destroy()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 1, "message" => "invalid permission"));
        } else {
            $container_id = $this->input->post("id");
            if (empty($container_id)) {
                echo json_encode(array("status" => 1, "message" => "invalid container name"));
            } else {
                $docker = $this->_get_docker_client();
                try {
                    $containers = $docker->containerList();
                    $is_running = false;
                    foreach ($containers as $container) {
                        if ($container->getId() == $container_id) {
                            $is_running = true;
                            break;
                        }
                    }
                    if ($is_running) {
                        $docker->containerStop($container_id);
                    }
                    $docker->containerDelete($container_id);
                    echo json_encode(array("status" => 1));
                } catch (Http\Client\Common\Exception\ClientErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                } catch (Http\Client\Common\Exception\ServerErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                }
            }
        }
    }
    public function start()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 1, "message" => "invalid permission"));
        } else {
            $container_id = $this->input->post("id");
            if (empty($container_id)) {
                echo json_encode(array("status" => 1, "message" => "invalid container name"));
            } else {
                $docker = $this->_get_docker_client();
                try {
                    $response = $docker->containerStart($container_id);
                    echo json_encode(array("status" => 1));
                } catch (Http\Client\Common\Exception\ClientErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                } catch (Http\Client\Common\Exception\ServerErrorException $exception) {
                    $result = $exception->getResponse()->getBody()->getContents();
                    $result = json_decode($result, true);
                    $result["status"] = 0;
                    echo json_encode($result);
                }
            }
        }
    }
    public function _get_docker_client()
    {
        $docker_host = $this->config->item("docker_remote_host");
        $client = Docker\DockerClientFactory::create(array("remote_socket" => $docker_host, "ssl" => false));
        $docker = Docker\Docker::create($client);
        return $docker;
    }
    public function _create_docker_container($container_name_or_id, $alternate_domain = NULL)
    {
        if (empty($container_name_or_id)) {
            return false;
        }
        $image = $this->config->item("docker_image");
        if (empty($image)) {
            $image = "dbface/dbface-docker";
        }
        $docker = $this->_get_docker_client();
        $containerConfig = new Docker\API\Model\ContainersCreatePostBody();
        $containerConfig->setImage($image);
        $containerConfig->setTty(true);
        $frontend_rule = "PathPrefixStrip:/" . $container_name_or_id;
        if (!empty($alternate_domain)) {
            $frontend_rule .= ";Host:" . $alternate_domain;
        }
        $labels = new ArrayObject(array("traefik.port" => "80", "traefik.frontend.rule" => $frontend_rule));
        $containerConfig->setLabels($labels);
        $volumes = new ArrayObject();
        $volumes[] = array("/var/www/user" => (object) array());
        $containerConfig->setVolumes($volumes);
        $hostConfig = new Docker\API\Model\HostConfig();
        $hostConfig->setBinds(array("/data/dbface/" . $container_name_or_id . ":/var/www/user"));
        $containerConfig->setHostConfig($hostConfig);
        try {
            $containers = $docker->containerList(array("all" => true, "filters" => json_encode(array("name" => array($container_name_or_id)))));
            if (0 < count($containers)) {
                return $containers[0]->getId();
            }
            log_message("debug", "Start create container", array("container_name" => $container_name_or_id));
            $containerCreateResult = $docker->containerCreate($containerConfig, array("name" => $container_name_or_id));
            $id = $containerCreateResult->getId();
            return $id;
        } catch (Exception $exception) {
            log_message("error", $exception->getTraceAsString());
            return false;
        }
        return false;
    }
    public function _get_hosting_info($userid)
    {
        $query = $this->db->where("userid", $userid)->get("dc_user_premium");
        if (0 < $query->num_rows()) {
            return $query->row_array();
        }
        return false;
    }
    /**
     * 用户自主启动DbFace Docker
     */
    public function m_launch()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "fail"));
        } else {
            $slug = $hosting_info["slug"];
            $customdomain = $hosting_info["customdomain"];
            $id = $this->_create_docker_container($slug, $customdomain);
            $result = array();
            if ($id) {
                $this->db->update("dc_user_premium", array("container_id" => $id), array("userid" => $login_userid));
                $result["result"] = "ok";
                $result["id"] = $id;
            } else {
                $result["result"] = "error";
            }
            echo json_encode($result);
        }
    }
    public function m_start()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "fail"));
        } else {
            $container_id = $hosting_info["container_id"];
            $docker = $this->_get_docker_client();
            $containers = $docker->containerList(array("all" => true, "filters" => json_encode(array("id" => array($container_id)))));
            if (0 < count($containers)) {
                $state = $containers[0]->getState();
                if ($state != "running") {
                    $docker->containerStart($container_id);
                }
            }
            echo json_encode(array("result" => "ok"));
        }
    }
    public function m_stop()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "fail"));
        } else {
            $container_id = $hosting_info["container_id"];
            $docker = $this->_get_docker_client();
            $containers = $docker->containerList(array("all" => true, "filters" => json_encode(array("id" => array($container_id)))));
            if (0 < count($containers)) {
                $state = $containers[0]->getState();
                if ($state != "stop") {
                    $docker->containerStop($container_id);
                }
            }
            echo json_encode(array("result" => "ok"));
        }
    }
    public function m_remove()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "fail"));
        } else {
            $container_id = $hosting_info["container_id"];
            $docker = $this->_get_docker_client();
            $containers = $docker->containerList(array("all" => true, "filters" => json_encode(array("id" => array($container_id)))));
            if (0 < count($containers)) {
                $state = $containers[0]->getState();
                if ($state == "running") {
                    $docker->containerStop($container_id);
                }
                $docker->containerDelete($container_id);
            }
            $this->db->update("dc_user_premium", array("container_id" => ""), array("userid" => $login_userid));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function m_updateprofile()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "fail"));
        } else {
            $slug = $this->input->post("slug");
            $alternate_domain = $this->input->post("alternate_domain");
            $docker_remote_base_url = $this->config->item("docker_remote_base_url");
            $full_url = $docker_remote_base_url . $slug;
            $this->db->update("dc_user_premium", array("slug" => $slug, "customdomain" => $alternate_domain, "full_url" => $full_url), array("userid" => $login_userid));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function m_state()
    {
        $login_userid = $this->session->userdata("login_userid");
        $hosting_info = $this->_get_hosting_info($login_userid);
        if (!$hosting_info) {
            echo json_encode(array("result" => "none"));
        } else {
            $container_id = $hosting_info["container_id"];
            $docker = $this->_get_docker_client();
            $containers = $docker->containerList(array("all" => true, "filters" => json_encode(array("id" => array($container_id)))));
            $this->load->library("smartyview");
            if (0 < count($containers)) {
                $container = $containers[0];
                $this->smartyview->assign("container_launchtime", date("Y-m-d H:i:s", $container->getCreated()));
                $this->smartyview->assign("container_status", $container->getStatus());
                $this->smartyview->assign("container_state", $container->getState());
            }
            $this->smartyview->assign("container_id", $container_id);
            $this->smartyview->assign("app_url", $hosting_info["full_url"]);
            $this->smartyview->assign("slug", $hosting_info["slug"]);
            $this->smartyview->assign("customdomain", $hosting_info["customdomain"]);
            $expired = $hosting_info["expiredate"] <= time();
            $docker_remote_base_url = $this->config->item("docker_remote_base_url");
            $this->smartyview->assign("docker_remote_base_url", $docker_remote_base_url);
            $this->smartyview->assign("expired", $expired);
            $this->smartyview->display("docker/docker.container.info.tpl");
        }
    }
}

?>