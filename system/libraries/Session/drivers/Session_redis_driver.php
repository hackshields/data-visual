<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * CodeIgniter Session Redis Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */

class CI_Session_redis_driver extends CI_Session_driver implements SessionHandlerInterface
{
/**
	 * phpRedis instance
	 *
	 * @var	Redis
	 */
    protected $_redis = NULL;
/**
	 * Key prefix
	 *
	 * @var	string
	 */
    protected $_key_prefix = "ci_session:";
/**
	 * Lock key
	 *
	 * @var	string
	 */
    protected $_lock_key = NULL;
/**
	 * Key exists flag
	 *
	 * @var bool
	 */
    protected $_key_exists = false;

    /**
	 * Class constructor
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */

    public function __construct(&$params)
    {
        parent::__construct($params);
        if( empty($this->_config["save_path"]) ) 
        {
            log_message("error", "Session: No Redis save path configured.");
        }
        else
        {
            if( preg_match("#^unix://([^\\?]+)(?<options>\\?.+)?\$#", $this->_config["save_path"], $matches) ) 
            {
                $save_path = array( "path" => $matches[1] );
            }
            else
            {
                if( preg_match("#(?:tcp://)?([^:?]+)(?:\\:(\\d+))?(?<options>\\?.+)?#", $this->_config["save_path"], $matches) ) 
                {
                    $save_path = array( "host" => $matches[1], "port" => (empty($matches[2]) ? NULL : $matches[2]), "timeout" => NULL );
                }
                else
                {
                    log_message("error", "Session: Invalid Redis save path format: " . $this->_config["save_path"]);
                }

            }

        }

        if( isset($save_path) ) 
        {
            if( isset($matches["options"]) ) 
            {
                $save_path["password"] = (preg_match("#auth=([^\\s&]+)#", $matches["options"], $match) ? $match[1] : NULL);
                $save_path["database"] = (preg_match("#database=(\\d+)#", $matches["options"], $match) ? (int) $match[1] : NULL);
                $save_path["timeout"] = (preg_match("#timeout=(\\d+\\.\\d+)#", $matches["options"], $match) ? (double) $match[1] : NULL);
                preg_match("#prefix=([^\\s&]+)#", $matches["options"], $match) and                 if( $this->_config["match_ip"] === true ) 
                {
                    $this->_key_prefix .= $_SERVER["REMOTE_ADDR"] . ":";
                }

            }

            $this->_config["save_path"] = $save_path;
        }

    }

    /**
	 * Open
	 *
	 * Sanitizes save_path and initializes connection.
	 *
	 * @param	string	$save_path	Server path
	 * @param	string	$name		Session cookie name, unused
	 * @return	bool
	 */

    public function open($save_path, $name)
    {
        if( empty($this->_config["save_path"]) ) 
        {
            return $this->_fail();
        }

        $redis = new Redis();
        $connected = (isset($this->_config["save_path"]["path"]) ? $redis->connect($this->_config["save_path"]["path"]) : $redis->connect($this->_config["save_path"]["host"], $this->_config["save_path"]["port"], $this->_config["save_path"]["timeout"]));
        if( $connected ) 
        {
            if( isset($this->_config["save_path"]["password"]) && !$redis->auth($this->_config["save_path"]["password"]) ) 
            {
                log_message("error", "Session: Unable to authenticate to Redis instance.");
            }
            else
            {
                if( isset($this->_config["save_path"]["database"]) && !$redis->select($this->_config["save_path"]["database"]) ) 
                {
                    log_message("error", "Session: Unable to select Redis database with index " . $this->_config["save_path"]["database"]);
                }
                else
                {
                    $this->_redis = $redis;
                    return $this->_success;
                }

            }

        }
        else
        {
            log_message("error", "Session: Unable to connect to Redis with the configured settings.");
        }

        return $this->_fail();
    }

    /**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */

    public function read($session_id)
    {
        if( isset($this->_redis) && $this->_get_lock($session_id) ) 
        {
            $this->_session_id = $session_id;
            $session_data = $this->_redis->get($this->_key_prefix . $session_id);
            is_string($session_data);
            (is_string($session_data) ? ($this->_key_exists = true) : ($session_data = ""));
            $this->_fingerprint = md5($session_data);
            return $session_data;
        }

        return $this->_fail();
    }

    /**
	 * Write
	 *
	 * Writes (create / update) session data
	 *
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */

    public function write($session_id, $session_data)
    {
        if( !(isset($this->_redis) && isset($this->_lock_key)) ) 
        {
            return $this->_fail();
        }

        if( $session_id !== $this->_session_id ) 
        {
            if( !$this->_release_lock() || !$this->_get_lock($session_id) ) 
            {
                return $this->_fail();
            }

            $this->_key_exists = false;
            $this->_session_id = $session_id;
        }

        $this->_redis->setTimeout($this->_lock_key, 300);
        if( $this->_fingerprint !== ($fingerprint = md5($session_data)) || $this->_key_exists === false ) 
        {
            if( $this->_redis->set($this->_key_prefix . $session_id, $session_data, $this->_config["expiration"]) ) 
            {
                $this->_fingerprint = $fingerprint;
                $this->_key_exists = true;
                return $this->_success;
            }

            return $this->_fail();
        }

        return ($this->_redis->setTimeout($this->_key_prefix . $session_id, $this->_config["expiration"]) ? $this->_success : $this->_fail());
    }

    /**
	 * Close
	 *
	 * Releases locks and closes connection.
	 *
	 * @return	bool
	 */

    public function close()
    {
        if( isset($this->_redis) ) 
        {
            try
            {
                if( $this->_redis->ping() === "+PONG" ) 
                {
                    $this->_release_lock();
                    if( $this->_redis->close() === false ) 
                    {
                        return $this->_fail();
                    }

                }

            }
            catch( RedisException $e ) 
            {
                log_message("error", "Session: Got RedisException on close(): " . $e->getMessage());
            }
            $this->_redis = NULL;
            return $this->_success;
        }

        return $this->_success;
    }

    /**
	 * Destroy
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */

    public function destroy($session_id)
    {
        if( isset($this->_redis) && isset($this->_lock_key) ) 
        {
            if( ($result = $this->_redis->delete($this->_key_prefix . $session_id)) !== 1 ) 
            {
                log_message("debug", "Session: Redis::delete() expected to return 1, got " . var_export($result, true) . " instead.");
            }

            $this->_cookie_destroy();
            return $this->_success;
        }

        return $this->_fail();
    }

    /**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */

    public function gc($maxlifetime)
    {
        return $this->_success;
    }

    /**
	 * Get lock
	 *
	 * Acquires an (emulated) lock.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */

    protected function _get_lock($session_id)
    {
        if( $this->_lock_key === $this->_key_prefix . $session_id . ":lock" ) 
        {
            return $this->_redis->setTimeout($this->_lock_key, 300);
        }

        $lock_key = $this->_key_prefix . $session_id . ":lock";
        $attempt = 0;
        if( 0 < ($ttl = $this->_redis->ttl($lock_key)) ) 
        {
            sleep(1);
            continue;
        }

        $result = ($ttl === -2 ? $this->_redis->set($lock_key, time(), array( "nx", "ex" => 300 )) : $this->_redis->setex($lock_key, 300, time()));
        if( !$result ) 
        {
            log_message("error", "Session: Error while trying to obtain lock for " . $this->_key_prefix . $session_id);
            return false;
        }

        $this->_lock_key = $lock_key;
        break;
    }

    /**
	 * Release lock
	 *
	 * Releases a previously acquired lock
	 *
	 * @return	bool
	 */

    protected function _release_lock()
    {
        if( isset($this->_redis) && isset($this->_lock_key) && $this->_lock ) 
        {
            if( !$this->_redis->delete($this->_lock_key) ) 
            {
                log_message("error", "Session: Error while trying to free lock for " . $this->_lock_key);
                return false;
            }

            $this->_lock_key = NULL;
            $this->_lock = false;
        }

        return true;
    }

}


