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
// This file was auto-generated from sdk-root/src/data/ecs/2014-11-13/waiters-2.json
return ['version' => 2, 'waiters' => ['TasksRunning' => ['delay' => 6, 'operation' => 'DescribeTasks', 'maxAttempts' => 100, 'acceptors' => [['expected' => 'STOPPED', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'tasks[].lastStatus'], ['expected' => 'MISSING', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'failures[].reason'], ['expected' => 'RUNNING', 'matcher' => 'pathAll', 'state' => 'success', 'argument' => 'tasks[].lastStatus']]], 'TasksStopped' => ['delay' => 6, 'operation' => 'DescribeTasks', 'maxAttempts' => 100, 'acceptors' => [['expected' => 'STOPPED', 'matcher' => 'pathAll', 'state' => 'success', 'argument' => 'tasks[].lastStatus']]], 'ServicesStable' => ['delay' => 15, 'operation' => 'DescribeServices', 'maxAttempts' => 40, 'acceptors' => [['expected' => 'MISSING', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'failures[].reason'], ['expected' => 'DRAINING', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'services[].status'], ['expected' => 'INACTIVE', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'services[].status'], ['expected' => true, 'matcher' => 'path', 'state' => 'success', 'argument' => 'length(services[?!(length(deployments) == `1` && runningCount == desiredCount)]) == `0`']]], 'ServicesInactive' => ['delay' => 15, 'operation' => 'DescribeServices', 'maxAttempts' => 40, 'acceptors' => [['expected' => 'MISSING', 'matcher' => 'pathAny', 'state' => 'failure', 'argument' => 'failures[].reason'], ['expected' => 'INACTIVE', 'matcher' => 'pathAny', 'state' => 'success', 'argument' => 'services[].status']]]]];

?>