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
// This file was auto-generated from sdk-root/src/data/kinesis/2013-12-02/paginators-1.json
return ['pagination' => ['DescribeStream' => ['input_token' => 'ExclusiveStartShardId', 'limit_key' => 'Limit', 'more_results' => 'StreamDescription.HasMoreShards', 'output_token' => 'StreamDescription.Shards[-1].ShardId', 'result_key' => 'StreamDescription.Shards'], 'ListStreamConsumers' => ['input_token' => 'NextToken', 'limit_key' => 'MaxResults', 'output_token' => 'NextToken'], 'ListStreams' => ['input_token' => 'ExclusiveStartStreamName', 'limit_key' => 'Limit', 'more_results' => 'HasMoreStreams', 'output_token' => 'StreamNames[-1]', 'result_key' => 'StreamNames']]];

?>