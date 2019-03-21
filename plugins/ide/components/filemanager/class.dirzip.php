<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * @author umbalaconmeogia at NOSPAM dot gmail dot com
 * @link http://www.php.net/manual/de/class.ziparchive.php#110719
 */
class DirZip
{
    /**
     * Add files and sub-directories in a folder to zip file.
     * @param string $folder
     * @param ZipArchive $zipFile
     * @param int $exclusiveLength Number of text to be exclusived from the file path.
     */
    private static function folderToZip($folder, &$zipFile, $exclusiveLength)
    {
        $handle = opendir($folder);
        while ($f = readdir($handle)) {
            if ($f != "." && $f != "..") {
                $filePath = (string) $folder . "/" . $f;
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                    $zipFile->addFile($filePath, $localPath);
                } else {
                    if (is_dir($filePath)) {
                        $zipFile->addEmptyDir($localPath);
                        self::folderToZip($filePath, $zipFile, $exclusiveLength);
                    }
                }
            }
        }
        closedir($handle);
    }
    /**
     * Zip a folder (include itself).
     * Usage:
     *   DirZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
     *
     * @param string $sourcePath Path of directory to be zip.
     * @param string $outZipPath Path of output zip file.
     */
    public static function zipDir($sourcePath, $outZipPath)
    {
        $pathInfo = pathInfo($sourcePath);
        $parentPath = $pathInfo["dirname"];
        $dirName = $pathInfo["basename"];
        $z = new ZipArchive();
        $z->open($outZipPath, ZIPARCHIVE::CREATE);
        $z->addEmptyDir($dirName);
        self::folderToZip($sourcePath, $z, strlen((string) $parentPath . "/"));
        $z->close();
    }
}

?>