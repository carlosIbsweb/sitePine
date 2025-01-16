<?php
/**
 * @package		Joomla Bamboo Zen Grid Framework
 * @Type        Core Framework Files
 * @version		v2.0
 * @author		Joomal Bamboo http://www.joomlabamboo.com
 * @copyright 	Copyright (C) 2007 - 2010 Joomla Bamboo
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * Adapted from Minify_CSS_UriRewriter class by Stephen Clay <steve@mrclay.org>
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
class ZenUriRewriter {
    
    /**
     * Defines which class to call as part of callbacks, change this
     * if you extend Minify_CSS_UriRewriter
     * @var string
     */
    protected static $className = 'ZenUriRewriter';
    
    public static function rewrite($css, $currentDir, $docRoot = null, $symlinks = array()) 
    {
        self::$_docRoot = self::_realpath(
            $docRoot ? $docRoot : $_SERVER['DOCUMENT_ROOT']
        );
        self::$_currentDir = self::_realpath($currentDir);
        self::$_symlinks = array();
        print_r( self::$_currentDir ); exit;
        // normalize symlinks
        foreach ($symlinks as $link => $target) {
            $link = ($link === '//')
                ? self::$_docRoot
                : str_replace('//', self::$_docRoot . '/', $link);
            $link = strtr($link, '/', DIRECTORY_SEPARATOR);
            self::$_symlinks[$link] = self::_realpath($target);
        }
        
        self::$debugText .= "docRoot    : " . self::$_docRoot . "\n"
                          . "currentDir : " . self::$_currentDir . "\n";
        if (self::$_symlinks) {
            self::$debugText .= "symlinks : " . var_export(self::$_symlinks, 1) . "\n";
        }
        self::$debugText .= "\n";
        
        $css = self::_trimUrls($css);
        
        // rewrite
        $css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/'
            ,array(self::$className, '_processUriCB'), $css);
        $css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
            ,array(self::$className, '_processUriCB'), $css);

        return $css;
    }
    
    /**
     * Prepend a path to relative URIs in CSS files
     * @param string $css
     * @param string $path The path to prepend.
     * @return string
     */
    public static function prepend($css, $path)
    {
        self::$_prependPath = $path;
        
        $css = self::_trimUrls($css);
        
        // append
        $css = preg_replace_callback('/@import\\s+([\'"])(.*?)[\'"]/'
            ,array(self::$className, '_processUriCB'), $css);
        $css = preg_replace_callback('/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
            ,array(self::$className, '_processUriCB'), $css);

        self::$_prependPath = null;
        return $css;
    }
    
    
    private static function _processUriCB($m)
    {
        // $m matched either '/@import\\s+([\'"])(.*?)[\'"]/' or '/url\\(\\s*([^\\)\\s]+)\\s*\\)/'
        $isImport = ($m[0][0] === '@');
        // determine URI and the quote character (if any)
        if ($isImport) {
            $quoteChar = $m[1];
            $uri = $m[2];
        } else {
            // $m[1] is either quoted or not
            $quoteChar = ($m[1][0] === "'" || $m[1][0] === '"')
                ? $m[1][0]
                : '';
            $uri = ($quoteChar === '')
                ? $m[1]
                : substr($m[1], 1, strlen($m[1]) - 2);
        }
        // analyze URI
        if ('/' !== $uri[0]                  // root-relative
            && false === strpos($uri, '//')  // protocol (non-data)
            && 0 !== strpos($uri, 'data:')   // data protocol
        ) {
            // URI is file-relative: rewrite depending on options
            $uri = (self::$_prependPath !== null)
                ? (self::$_prependPath . $uri)
                : self::rewriteRelative($uri, self::$_currentDir, self::$_docRoot, self::$_symlinks);
        }
        return $isImport
            ? "@import {$quoteChar}{$uri}{$quoteChar}"
            : "url({$quoteChar}{$uri}{$quoteChar})";
    }
    
}
	