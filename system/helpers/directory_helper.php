<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );
if( !function_exists("directory_map") ) 
{
/**
	 * Create a Directory Map
	 *
	 * Reads the specified directory and builds an array
	 * representation of it. Sub-folders contained with the
	 * directory will be mapped as well.
	 *
	 * @param	string	$source_dir		Path to source
	 * @param	int	$directory_depth	Depth of directories to traverse
	 *						(0 = fully recursive, 1 = current dir, etc)
	 * @param	bool	$hidden			Whether to show hidden files
	 * @return	array
	 */

function directory_map($source_dir, $directory_depth = 0, $hidden = false)
{
    if( $fp = @opendir($source_dir) ) 
    {
        $filedata = array(  );
        $new_depth = $directory_depth - 1;
        $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        while( false !== ($file = readdir($fp)) ) 
        {
            if( $file === "." || $file === ".." || $hidden === false && $file[0] === "." ) 
            {
                continue;
            }

            is_dir($source_dir . $file) and if( ($directory_depth < 1 || 0 < $new_depth) && is_dir($source_dir . $file) ) 
{
    $filedata[$file] = directory_map($source_dir . $file, $new_depth, $hidden);
}
else
{
    $filedata[] = $file;
}

        }
        closedir($fp);
        return $filedata;
    }

    return false;
}

}


