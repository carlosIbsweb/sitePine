<?php

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 * 
 * @author Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license GNU/GPLv3, See LICENSE file
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */
defined('_JCH_EXEC') or die('Restricted access');

class JchOptimizeAjax
{

        /**
         * 
         * @param JchPlatformSettings $params
         */
        public static function garbageCron(JchPlatformSettings $params)
        {
                JchPlatformCache::gc();
        }

        ##<procode>##

        /**
         * 
         * @return type
         * @throws type
         */
        public static function optimizeImages()
        {
                error_reporting(0);

                $root = JchPlatformPaths::rootPath();

                set_time_limit(0);

                $dir_array = JchPlatformUtility::get('dir', '', 'array');
                $subdirs   = JchPlatformUtility::get('subdirs', '', 'array');
                $params    = (object) JchPlatformUtility::get('params', '', 'array');
                $task      = JchPlatformUtility::get('task', '0', 'string');

                $dir = rtrim(JchPlatformUtility::decrypt($dir_array['path']), '/\\');

                if ($task == 'getfiles')
                {
                        $files = array();

                        if (count(array_filter($subdirs)))
                        {
                                foreach ($subdirs as $subdir)
                                {
                                        $subdir = rtrim(JchPlatformUtility::decrypt($subdir), '/\\');
                                        $files  = array_merge($files, self::getImageFiles($root . $subdir, TRUE));
                                }
                        }

                        if (!empty($files))
                        {
                                $files = array_map(function($v)
                                {
                                        return JchOptimizeHelper::prepareImageUrl($v);
                                }, $files);
                        }

                        $data = array(
                                'files'    => $files,
                                'log_path' => JchPlatformUtility::getLogsPath()
                        );

                        return new JchOptimizeJson($data);
                }

                $file = $dir;
                $data = array();

                $oJchio = new JchOptimize\ImageOptimizer($params->pro_downloadid, $params->hidden_api_secret);

                $options = array(
                        "file"  => $file,
                        "lossy" => true//(bool) $params->kraken_optimization_level
                );

                if (!empty($dir_array['width']) || !empty($dir_array['height']))
                {
                        $options['resize']['width']  = (int) (!empty($dir_array['width']) ? $dir_array['width'] : 0);
                        $options['resize']['height'] = (int) (!empty($dir_array['height']) ? $dir_array['height'] : 0);
                }

                if ($params->kraken_backup || !empty($options['resize']))
                {
                        $backup_file = self::getBackupFilename($file);
                        self::copy($file, $backup_file);
                }

                $message = '';
                $file    = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $file);

                try
                {
                        $response = $oJchio->upload($options);

                        if (isset($response->success))
                        {
                                if ($response->success)
                                {
                                        if (self::copy($response->data->kraked_url, $file))
                                        {
                                                $message .= 'Optimized! You saved ' . $response->data->saved_bytes . ' bytes.';
                                        }
                                        else
                                        {
                                                $data = new Exception('Could not copy optimized file.', 404);
                                        }
                                }
                                else
                                {
                                        $data = new Exception($response->message, $response->code);
                                }
                        }
                        else
                        {
                                JchOptimizeLogger::logInfo($response, 'Server error');

                                $data = new Exception('Unrecognizable response from server', 500);
                        }
                }
                catch (Exception $e)
                {
                        $data = $e;
                }

                $respond = new JchOptimizeJson($data, $message);

                if ($respond->success || $respond->code == 404)
                {
                        $respond->message = $file . ': ' . $respond->message;
                }

                try
                {
                        JchOptimizeLogger::logInfo($respond->message, 'INFO');
                }
                catch (Exception $e)
                {
                        
                }

                return $respond;
        }

        /**
         * 
         * @param type $file
         * @return type
         */
        protected static function getBackupFilename($file)
        {
                $backup_parent_folder = JchPlatformPaths::backupImagesParentFolder();

                $backup_file = $backup_parent_folder . 'jch_optimize_backup_images/' .
                        str_replace(array(JchPlatformPaths::rootPath(), '_', '/'), array('', '', '_'), $file);

                if (@file_exists($backup_file))
                {
                        $backup_file = self::incrementBackupFileName($backup_file);
                }

                return $backup_file;
        }

        /**
         * 
         * @param type $file
         * @return type
         */
        protected function incrementBackupFileName($file)
        {
                $backup_file = preg_replace_callback('#(?:(_)(\d++))?(\.[^.\s]++)$#',
                                                     function($m)
                {
                        $m[1] = $m[1] == '' ? '_' : $m[1];
                        $m[2] = $m[2] == '' ? 0 : (int) $m[2];

                        return $m[1] . (string) ++$m[2] . $m[3];
                }, rtrim($file));

                if (@file_exists($backup_file))
                {
                        $backup_file = self::incrementBackupFileName($backup_file);
                }

                return $backup_file;
        }

        /**
         * 
         * @param type $scr
         * @param type $dest
         * @return type
         */
        public static function copy($src, $dest)
        {
                $dest_dir = dirname($dest);

                if (!@file_exists($dest_dir))
                {
                        JchPlatformUtility::createFolder($dest_dir);
                }

                $context = stream_context_create(array('ssl' => array(
                                'verify_peer' => true,
                                'cafile'      => dirname(__FILE__) . '/libs/cacert.pem'
                )));
                
                $src_stream = fopen($src, 'rb', false, $context);

                if ($src_stream === false)
                {
                        return false;
                }

                $dest_stream = fopen($dest, 'wb');

                

                return stream_copy_to_stream($src_stream, $dest_stream);
        }

        /**
         * 
         * @return string
         */
        public static function fileTree()
        {
                $root = rtrim(JchPlatformPaths::rootPath(), '/\\');

                $dir     = urldecode(JchPlatformUtility::get('dir', '', 'string', 'get'));
                $view    = urldecode(JchPlatformUtility::get('jchview', '', 'string', 'get'));
                $initial = urldecode(JchPlatformUtility::get('initial', '0', 'string', 'get'));

                $dir = JchPlatformUtility::decrypt($dir) . '/';

                if ($view != 'tree')
                {
                        $header  = '<div id="files-container-header"><ul class="jqueryFileTree"><li><span>&lt;root&gt;' . $dir . '</span></li>';
                        $header .= '<li class="check-all"><span><input type="checkbox"></span><span><em>Check all</em></span>'
                                . '<span><em>' . JchPlatformUtility::translate('Width') . ' (px)</em></span>'
                                . '<span><em>' . JchPlatformUtility::translate('Height') . ' (px)</em></span></li></ul></div><div class="files-content">';
                        $display = '';
                }
                else
                {
                        $display = 'style="display: none;"';
                        $header  = '';
                }

                $response = '';



                if (@file_exists($root . $dir))
                {
                        $files = scandir($root . $dir);
//                        $files = JchPlatformUtility::lsFiles($root . $dir, '\.(?:gif|jpe?g|png)$', FALSE);
                        natcasesort($files);
                        if (count($files) > 2)
                        { /* The 2 accounts for . and .. */
                                $response .= '';

                                $response = $header;

                                if ($initial && $view == 'tree')
                                {
                                        $response .= '<div class="files-content"><ul class="jqueryFileTree">';
                                        $response .= '<li class="directory expanded"><a href="#" rel="">&lt;root&gt;</a>';
                                }

                                $response .= '<ul class="jqueryFileTree" ' . $display . '>';

                                foreach ($files as $file)
                                {
                                        if (@file_exists($root . $dir . $file) && $file != '.' && $file != '..' && is_dir($root . $dir . $file))
                                        {
                                                $response .= '<li class="directory collapsed">'
                                                        . self::item($file, $dir, $view, 'dir') . '</li>';
                                        }
                                }
                                // All files
                                if ($view != 'tree')
                                {
                                        foreach ($files as $file)
                                        {
                                                if (@file_exists($root . $dir . $file) && preg_match('#\.(?:gif|jpe?g|png|GIF|JPE?G|PNG)$#', $file) && !is_dir($root . $dir . $file))
                                                {
                                                        $ext = preg_replace('/^.*\./', '', $file);
                                                        $response .= '<li class="file ext_' . strtolower($ext) . '">'
                                                                . self::item($file, $dir, $view, 'file')
                                                                . '</li>';
                                                }
                                        }
                                }

                                $response .= '</ul>';

                                if ($initial && $view == 'tree')
                                {
                                        $response .= '</li></ul></div>';
                                }
                        }
                }

                return $response;
        }

        /**
         * 
         * @param type $file
         * @param type $dir
         * @param type $view
         * @param type $path
         * @return string
         */
        private static function item($file, $dir, $view, $path)
        {
                $encrypt_dir  = JchPlatformUtility::encrypt($dir . $file);
                $encrypt_file = JchPlatformUtility::encrypt(rtrim(JchPlatformPaths::rootPath(), '/\\') . $dir . $file);

                $anchor = '<a href="#" rel="' . $encrypt_dir . '">'
                        . htmlentities($file)
                        . '</a>';

                $html = '';

                if ($view == 'tree')
                {
                        $html .= $anchor;
                }
                else
                {
                        if ($path == 'dir')
                        {
                                $html .= '<span><input type="checkbox" value="' . $encrypt_dir . '"></span>';
                                $html .= $anchor;
                        }
                        else
                        {
                                $html .= '<span><input type="checkbox" value="' . $encrypt_file . '"></span>';
                                $html .= '<span>' . htmlentities($file) . '</span>'
                                        . '<span><input type="text" size="10" maxlength="5" name="width" ></span>'
                                        . '<span><input type="text" size="10" maxlength="5" name="height" ></span>';
                        }
                }

                return $html;
        }

        private static function getImageFiles($dir, $recursive = false)
        {
                return JchPlatformUtility::lsFiles($dir, '\.(?:gif|jpe?g|png|GIF|JPE?G|PNG)$', $recursive);
        }

        ##</procode>##
}
