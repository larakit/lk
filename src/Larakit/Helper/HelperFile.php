<?php
//Inspired by Kohana
namespace Larakit\Helper;

use Illuminate\Support\Arr;

/**
 * File helper class.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class HelperFile {
    /**
     * Attempt to get the mime type from a file. This method is horribly
     * unreliable, due to PHP being horribly unreliable when it comes to
     * determining the mime type of a file.
     *
     *     $mime = File::mime($file);
     *
     * @param   string $filename file name or path
     *
     * @return  string  mime type on success
     * @return  FALSE   on failure
     */
    public static function mime($filename) {
        // Get the complete path to the file
        $filename = realpath($filename);

        // Get the extension from the filename
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (preg_match('/^(?:jpe?g|png|[gt]if|bmp|swf)$/', $extension)) {
            // Use getimagesize() to find the mime type on images
            try{
                $file = getimagesize($filename);
            }catch(\Exception $e){

            }

            if (isset($file['mime']))
                return $file['mime'];
        }

        if (class_exists('finfo', false)) {
            if ($info = new \finfo(defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME)) {
                return $info->file($filename);
            }
        }

        if (ini_get('mime_magic.magicfile') AND function_exists('mime_content_type')) {
            // The mime_content_type function is only useful with a magic file
            return mime_content_type($filename);
        }

        if (!empty($extension)) {
            return self::mime_by_ext($extension);
        }

        // Unable to find the mime-type
        return false;
    }

    /**
     * Return the mime type of an extension.
     *
     *     $mime = File::mime_by_ext('png'); // "image/png"
     *
     * @param   string $extension php, pdf, txt, etc
     *
     * @return  string  mime type on success
     * @return  FALSE   on failure
     */
    public static function mime_by_ext($extension) {
        // Load all of the mime types
        $mimes = \Config::get('larakit::mimes');
        return Arr::get($mimes, $extension, false);
    }

    /**
     * Lookup MIME types for a file
     *
     * @see Kohana_File::mime_by_ext()
     *
     * @param string $extension Extension to lookup
     *
     * @return array Array of MIMEs associated with the specified extension
     */
    public static function mimes_by_ext($extension) {
        // Load all of the mime types
        $mimes = \Config::get('larakit::mimes');
        return Arr::get($mimes, $extension, []);
    }

    /**
     * Lookup file extensions by MIME type
     *
     * @param   string $type File MIME type
     *
     * @return  array   File extensions matching MIME type
     */
    public static function exts_by_mime($type) {
        static $types = [];

        // Fill the static array
        if (empty($types)) {
            foreach (\Config::get('larakit::mimes') as $ext => $mimes) {
                foreach ($mimes as $mime) {
                    if ($mime == 'application/octet-stream') {
                        // octet-stream is a generic binary
                        continue;
                    }

                    if (!isset($types[$mime])) {
                        $types[$mime] = [(string)$ext];
                    } elseif (!in_array($ext, $types[$mime])) {
                        $types[$mime][] = (string)$ext;
                    }
                }
            }
        }
        return Arr::get($types, $type, false);
    }

    /**
     * Lookup a single file extension by MIME type.
     *
     * @param   string $type MIME type to lookup
     *
     * @return  mixed          First file extension matching or false
     */
    public static function ext_by_mime($type) {
        return current(self::exts_by_mime($type));
    }
    public static function normalizeFilePath($type) {
        return str_replace('\\', '/', $type);
    }
}
