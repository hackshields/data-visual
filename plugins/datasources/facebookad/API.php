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
namespace Plugin\Datasource\FacebookAd;

/**
 * Facebook Ad Plugin
 *
 * @package		Plugin\Datasource\FacebookAd
 * @author		DbFace 
 * @copyright	Copyright (c) 2018 DbFace, Inc.
 * @link		https://www.dbface.com
 * @since		Version 1.0
 */
class API
{
    /**
     * accept data source configuration
     *
     * @param $config
     * @return string pluging url
     */
    public function __construct($config = array())
    {
    }
    /**
     * sync data for this plugin. Can be invoked from clicking sync button or scheduled cron job
     */
    public function sync()
    {
        return TRUE;
    }
}

?>