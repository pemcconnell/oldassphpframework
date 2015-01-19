<?php

/**
 * Simple class that allows you to perform some checking on a file type.
 */

class Filetype {
    static public $aImgExts, $aDocExts;

    /**
     * __construct
     * 
     * Builds array of accepted image and document extensions in lowercase.
     * 
     * @return null
     */
    public function __construct() {
        self::$aImgExts = array(
            'jpg', 'jpeg', 'gif', 'bmp', 'png', 'tiff', 'tga', 'gd', 'gd2'
        );
        self::$aDocExts = array(
            'doc', 'docx', 'csv', 'xls', 'xlsx', 'rtf', 'txt', 'pdf', 'ppt', 'pptx'
        );
    }

    /**
     * img
     * 
     * @param string $filename Filename (may include path)
     * 
     * @return bool Returns boolean if the filename is an accepted format
     */
    static public function img($filename) {
        $ext = strtolower(HTML::getFileExt($filename));
        return in_array($ext, self::$aImgExts);
    }

    /**
     * doc
     * 
     * @param string $filename Filename (may include path)
     * 
     * @return bool Returns boolean if the filename is an accepted format
     */
    static public function doc($filename) {
        $ext = strtolower(HTML::getFileExt($filename));
        return in_array($ext, self::$aImgExts);
    }

    /**
     * match
     * 
     * @param string $filename         Filename (may include path)
     * @param string $desiredExtension Desired extension
     * 
     * @return bool Returns boolean if the filename matches the desired extension
     */
    static public function match($filename, $desiredExtension) {
        return (strtolower(HMTL::getFileExt($filename)) == strtolower($desiredExtension));
    }

}
