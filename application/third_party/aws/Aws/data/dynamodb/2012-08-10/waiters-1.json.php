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
// This file was auto-generated from sdk-root/src/data/dynamodb/2012-08-10/waiters-1.json
return ['waiters' => ['__default__' => ['interval' => 20, 'max_attempts' => 25], '__TableState' => ['operation' => 'DescribeTable'], 'TableExists' => ['extends' => '__TableState', 'ignore_errors' => ['ResourceNotFoundException'], 'success_type' => 'output', 'success_path' => 'Table.TableStatus', 'success_value' => 'ACTIVE'], 'TableNotExists' => ['extends' => '__TableState', 'success_type' => 'error', 'success_value' => 'ResourceNotFoundException']]];

?>