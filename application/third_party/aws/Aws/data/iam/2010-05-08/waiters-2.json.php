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
// This file was auto-generated from sdk-root/src/data/iam/2010-05-08/waiters-2.json
return ['version' => 2, 'waiters' => ['InstanceProfileExists' => ['delay' => 1, 'operation' => 'GetInstanceProfile', 'maxAttempts' => 40, 'acceptors' => [['expected' => 200, 'matcher' => 'status', 'state' => 'success'], ['state' => 'retry', 'matcher' => 'status', 'expected' => 404]]], 'UserExists' => ['delay' => 1, 'operation' => 'GetUser', 'maxAttempts' => 20, 'acceptors' => [['state' => 'success', 'matcher' => 'status', 'expected' => 200], ['state' => 'retry', 'matcher' => 'error', 'expected' => 'NoSuchEntity']]]]];

?>