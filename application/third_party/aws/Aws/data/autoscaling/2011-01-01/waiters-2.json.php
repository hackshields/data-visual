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
// This file was auto-generated from sdk-root/src/data/autoscaling/2011-01-01/waiters-2.json
return ['version' => 2, 'waiters' => ['GroupExists' => ['acceptors' => [['argument' => 'length(AutoScalingGroups) > `0`', 'expected' => true, 'matcher' => 'path', 'state' => 'success'], ['argument' => 'length(AutoScalingGroups) > `0`', 'expected' => false, 'matcher' => 'path', 'state' => 'retry']], 'delay' => 5, 'maxAttempts' => 10, 'operation' => 'DescribeAutoScalingGroups'], 'GroupInService' => ['acceptors' => [['argument' => 'contains(AutoScalingGroups[].[length(Instances[?LifecycleState==\'InService\']) >= MinSize][], `false`)', 'expected' => false, 'matcher' => 'path', 'state' => 'success'], ['argument' => 'contains(AutoScalingGroups[].[length(Instances[?LifecycleState==\'InService\']) >= MinSize][], `false`)', 'expected' => true, 'matcher' => 'path', 'state' => 'retry']], 'delay' => 15, 'maxAttempts' => 40, 'operation' => 'DescribeAutoScalingGroups'], 'GroupNotExists' => ['acceptors' => [['argument' => 'length(AutoScalingGroups) > `0`', 'expected' => false, 'matcher' => 'path', 'state' => 'success'], ['argument' => 'length(AutoScalingGroups) > `0`', 'expected' => true, 'matcher' => 'path', 'state' => 'retry']], 'delay' => 15, 'maxAttempts' => 40, 'operation' => 'DescribeAutoScalingGroups']]];

?>