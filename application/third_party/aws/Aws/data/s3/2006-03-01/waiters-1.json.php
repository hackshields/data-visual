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
// This file was auto-generated from sdk-root/src/data/s3/2006-03-01/waiters-1.json
return ['waiters' => ['__default__' => ['interval' => 5, 'max_attempts' => 20], 'BucketExists' => ['operation' => 'HeadBucket', 'ignore_errors' => ['NoSuchBucket'], 'success_type' => 'output'], 'BucketNotExists' => ['operation' => 'HeadBucket', 'success_type' => 'error', 'success_value' => 'NoSuchBucket'], 'ObjectExists' => ['operation' => 'HeadObject', 'ignore_errors' => ['NoSuchKey'], 'success_type' => 'output'], 'ObjectNotExists' => ['operation' => 'HeadObject', 'success_type' => 'error', 'success_value' => 'NoSuchKey']]];

?>