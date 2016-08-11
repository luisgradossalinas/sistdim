<?php


/**
 * S view Helpers is a Loader for Static files
 * providing ability to read prefix from config 
 * and suffix a versioning query string 
 *
 */
class App_View_Helper_S
    extends Zend_View_Helper_HtmlElement
{

    protected static $_lastCommit;

    /**
     * @param  String
     * @return string
     */
    public function S($file)
    {
        /**
         * @todo cache to avoid file reading frequently
         * @tutorial use a post-commit hook  to execute:
         * git log -1 --format=%h > last_commit
         */
        $lc_file = APPLICATION_PATH . '/../last_commit';

        if (is_readable($lc_file)) {
            if (!isset(self::$_lastCommit)) {
                self::$_lastCommit = trim(file_get_contents($lc_file));
            }
        }

        return MEDIA_URL . $file . '?v=' . self::$_lastCommit;
    }

}