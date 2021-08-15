<?php

/**
 * Plugin Name: WP Plugin Template
 * Version: 1.0.0
 * Plugin URI:
 * Description: This is a simpel WordPress plugin template.
 * Author: Bruno Ferreira
 * Author URI: https://github.com/srbrunoferreira
 * Requires at least: 5.8
 * Tested up to: 5.8
 *
 * @package WordPress
 * @author Bruno Ferreira
 * @since 1.0.0
 */

defined('ABSPATH') or exit;

require_once 'helpers.php';

/**
 * Main class of the plugin.
 *
 * @author Bruno Ferreira <srbrunoferreira@outlook.com>
 * @version 1.0.0
 */
final class WPPTFrameworkHandler
{
    /**
     * Stores all the possible paths of a framework path.
     *
     * @var array
     */
    private $frameworksFiles;

    private $frameworksSystemPath;

    private $frameworkUrlPath;

    private $registeredFrameworkFilesIds;

    public function __construct($pluginSystemPath, $pluginUrlPath)
    {
        $this->loadFilesExtensions = ['css', 'js'];

        $this->frameworkUrlPath = $pluginUrlPath . 'includes/frameworks';
        $this->frameworksSystemPath = $pluginSystemPath . 'includes/frameworks';

        $this->frameworksFiles['dist'] = $this->getFrameworkFilesPaths($this->frameworksSystemPath);
    }

    /**
     * Checks, recursively the paths of the $dir param.
     *
     * @param string $target - A param used internally by the method.
     * @return array $paths A array containing all the possible paths of the $dir param.
     */
    private function getFrameworkFilesPaths($dir)
    {
        $paths = [];
        foreach (scandir($dir) as $filename) {
            if ($filename[0] === '.') {
                continue;
            }
            $filePath = $dir . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($filePath)) {
                foreach ($this->getFrameworkFilesPaths($filePath) as $childFilename) {
                    $paths[] = $filename . DIRECTORY_SEPARATOR . $childFilename;
                }
            } else {
                $paths[] = $filename;
            }
        }

        return $paths;
    }

    /**
     * Uses the function wp_enqueue to enqueue and register framework files.
     *
     * @see https://github.com/analubarreto/wp-quasar/blob/master/wp-quasar.php
     *
     * @param string $shortcode
     */
    public function enqueueFrameworkFiles()
    {
        foreach ($this->frameworksFiles as $frameworkFilePaths) {
            foreach ($frameworkFilePaths as $frameworkFilePath) {
                $filePath = $this->frameworksSystemPath . DIRECTORY_SEPARATOR . $frameworkFilePath;
                $fileUrl = $this->frameworkUrlPath . DIRECTORY_SEPARATOR . $frameworkFilePath;

                $filePath = str_replace('\\', '/', $filePath);
                $fileUrl = str_replace('\\', '/', $fileUrl);

                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                $identifier = wpptGenerateRandomString();

                // Checks the file extension to use the correct WordPress register expression.
                switch ($extension) {
                    case 'js':
                        wp_enqueue_script($identifier, $fileUrl);
                        $this->registeredFrameworkFilesIds[] = $identifier . '-js';
                        break;
                    case 'css':
                        wp_enqueue_style($identifier, $fileUrl);
                        $this->registeredFrameworkFilesIds[] = $identifier . '-css';
                        break;
                    case 'html':
                        require_once $filePath;
                        break;
                }
            }
        }

        return <<<HTML
            <div id="q-app"></div>
        HTML;
    }
}
