<?php

/** This file is part of KCFinder project
  *
  *      @desc Directory helper class
  *   @package KCFinder
  *   @version 2.1
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

class dir {

  /** Checks if the given directory is really writable. The standard PHP
    * function is_writable() does not work properly on Windows servers
    * @param string $dir
    * @return bool */

    static function isWritable($dir) {
        $dir = path::normalize($dir);
        if (!is_dir($dir))
            return false;
        $i = 0;
        do {
            $file = "$dir/is_writable_" . md5($i++);
        } while (file_exists($file));
        if (!@touch($file))
            return false;
        unlink($file);
        return true;
    }

  /** Reqursively create all directories in given path if they are not exists.
    * Returns TRUE on success or false on fail.
    * @param string $dir
    * @param integer $mode
    * @return bool */

    static function rmkdir($dir, $mode=0755) {
        $dir = preg_replace('/\/{2,}/s', "/", $dir);
        $dirs = explode("/", $dir);
        $curr = "";
        foreach ($dirs as $i => $cdir) {
            if (($i == 0) && ($cdir == ""))
                $curr .= "/";
            elseif ($i == 0)
                $curr .= $cdir;
            else
                $curr .= "/$cdir";
            if (!is_dir($curr) &&
                !@mkdir($curr, $mode)
            )
                return false;
        }
        return true;
    }

  /** Recursively delete the given directory. Returns TRUE on success.
    * If $firstFailExit parameter is true (default), the method returns the
    * path to the first failed file or directory which cannot be deleted.
    * If $firstFailExit is false, the method returns an array with failed
    * files and directories which cannot be deleted. The third parameter
    * $failed is used for internal use only.
    * @param string $dir
    * @param bool $firstFailExit
    * @param array $failed
    * @return mixed */

    static function prune($dir, $firstFailExit=true, array $failed=null) {
        if ($failed === null) $failed = array();
        $files = @glob("$dir/*");
        if ($files === false) {
            if ($firstFailExit)
                return $dir;
            $failed[] = $dir;
            return $failed;
        }

        foreach ($files as $file) {
            if (is_dir($file)) {
                $failed_in = self::prune($file, $firstFailExit, $failed);
                if ($failed_in !== true) {
                    if ($firstFailExit)
                        return $failed_in;
                    if (is_array($failed_in))
                        $failed = array_merge($failed, $failed_in);
                    else
                        $failed[] = $failed_in;
                }
            } elseif (!@unlink($file)) {
                if ($firstFailExit)
                    return $file;
                $failed[] = $file;
            }
        }

        if (!@rmdir($dir)) {
            if ($firstFailExit)
                return $dir;
            $failed[] = $dir;
        }

        return count($failed) ? $failed : true;
    }
}

?>