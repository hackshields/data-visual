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
// This file was auto-generated from sdk-root/src/data/eks/2017-11-01/waiters-2.json
return ['version' => 2, 'waiters' => ['ClusterActive' => ['delay' => 30, 'operation' => 'DescribeCluster', 'maxAttempts' => 40, 'acceptors' => [['expected' => 'DELETING', 'matcher' => 'path', 'state' => 'failure', 'argument' => 'cluster.status'], ['expected' => 'FAILED', 'matcher' => 'path', 'state' => 'failure', 'argument' => 'cluster.status'], ['expected' => 'ACTIVE', 'matcher' => 'path', 'state' => 'success', 'argument' => 'cluster.status']]], 'ClusterDeleted' => ['delay' => 30, 'operation' => 'DescribeCluster', 'maxAttempts' => 40, 'acceptors' => [['expected' => 'ACTIVE', 'matcher' => 'path', 'state' => 'failure', 'argument' => 'cluster.status'], ['expected' => 'CREATING', 'matcher' => 'path', 'state' => 'failure', 'argument' => 'cluster.status'], ['expected' => 'ResourceNotFoundException', 'matcher' => 'error', 'state' => 'success']]]]];

?>