<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

function smarty_modifier_db_display_name($conn)
{
    if (!is_array($conn)) {
        return "Unknow Source";
    }
    // [{else}][{$conns[i].username}]@[{$conns[i].hostname}][{/if}]
    if ($conn['dbdriver'] == 'sqlite' || $conn['dbdriver'] == 'dsn') {
        return $conn['database'];
    }
    $str = '';
    if (!empty($conn['username'])) {
        $str .= $conn['username'] . '@';
    }
    $str .= $conn['hostname'];
    return $str;
}

?>