<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );
if( is_php("5.6") ) 
{
    return NULL;
}

if( !function_exists("hash_equals") ) 
{
/**
	 * hash_equals()
	 *
	 * @link	http://php.net/hash_equals
	 * @param	string	$known_string
	 * @param	string	$user_string
	 * @return	bool
	 */

function hash_equals($known_string, $user_string)
{
    if( !is_string($known_string) ) 
    {
        trigger_error("hash_equals(): Expected known_string to be a string, " . strtolower(gettype($known_string)) . " given", 512);
        return false;
    }

    if( !is_string($user_string) ) 
    {
        trigger_error("hash_equals(): Expected user_string to be a string, " . strtolower(gettype($user_string)) . " given", 512);
        return false;
    }

    if( ($length = strlen($known_string)) !== strlen($user_string) ) 
    {
        return false;
    }

    $diff = 0;
    for( $i = 0; $i < $length; $i++ ) 
    {
        $diff |= ord($known_string[$i]) ^ ord($user_string[$i]);
    }
    return $diff === 0;
}

}

if( is_php("5.5") ) 
{
    return NULL;
}

if( !function_exists("hash_pbkdf2") ) 
{
/**
	 * hash_pbkdf2()
	 *
	 * @link	http://php.net/hash_pbkdf2
	 * @param	string	$algo
	 * @param	string	$password
	 * @param	string	$salt
	 * @param	int	$iterations
	 * @param	int	$length
	 * @param	bool	$raw_output
	 * @return	string
	 */

function hash_pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
{
    if( !in_array(strtolower($algo), hash_algos(), true) ) 
    {
        trigger_error("hash_pbkdf2(): Unknown hashing algorithm: " . $algo, 512);
        return false;
    }

    if( ($type = gettype($iterations)) !== "integer" ) 
    {
        if( $type === "object" && method_exists($iterations, "__toString") ) 
        {
            $iterations = (string) $iterations;
        }

        if( is_string($iterations) && is_numeric($iterations) ) 
        {
            $iterations = (int) $iterations;
        }
        else
        {
            trigger_error("hash_pbkdf2() expects parameter 4 to be long, " . $type . " given", 512);
            return NULL;
        }

    }

    if( $iterations < 1 ) 
    {
        trigger_error("hash_pbkdf2(): Iterations must be a positive integer: " . $iterations, 512);
        return false;
    }

    if( ($type = gettype($length)) !== "integer" ) 
    {
        if( $type === "object" && method_exists($length, "__toString") ) 
        {
            $length = (string) $length;
        }

        if( is_string($length) && is_numeric($length) ) 
        {
            $length = (int) $length;
        }
        else
        {
            trigger_error("hash_pbkdf2() expects parameter 5 to be long, " . $type . " given", 512);
            return NULL;
        }

    }

    if( $length < 0 ) 
    {
        trigger_error("hash_pbkdf2(): Length must be greater than or equal to 0: " . $length, 512);
        return false;
    }

    $hash_length = (defined("MB_OVERLOAD_STRING") ? mb_strlen(hash($algo, NULL, true), "8bit") : strlen(hash($algo, NULL, true)));
    empty($length) and static $block_sizes = NULL;
    empty($block_sizes) and if( isset($block_sizes[$algo]) && isset($password[$block_sizes[$algo]]) ) 
{
    $password = hash($algo, $password, true);
}

    $hash = "";
    $bc = (int) ceil($length / $hash_length);
    for( $bi = 1; $bi <= $bc; $bi++ ) 
    {
        $key = $derived_key = hash_hmac($algo, $salt . pack("N", $bi), $password, true);
        for( $i = 1; $i < $iterations; $i++ ) 
        {
            $derived_key ^= $key = hash_hmac($algo, $key, $password, true);
        }
        $hash .= $derived_key;
    }
    if( !$raw_output ) 
    {
        $hash = bin2hex($hash);
    }

    return (defined("MB_OVERLOAD_STRING") ? mb_substr($hash, 0, $length, "8bit") : substr($hash, 0, $length));
}

}


