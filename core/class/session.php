<?php

/** This file is part of KCFinder project
 *
 *      @desc Session class
 *   @package KCFinder
 *   @version 3.12
 *    @author Pavel Tzonkov <sunhater@sunhater.com>
 * @copyright 2010-2014 KCFinder Project
 *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
 *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
 *      @link http://kcfinder.sunhater.com
 */

namespace kcfinder;

class session {

    const SESSION_VAR = "_sessionVar";
    public $values;
    protected $config;

    /**
     * @param $configFile
     */
    public function __construct($configFile)
    {
        /*********************************************************
         * Include ZF2
         */
        $cwd = getcwd();
        $zf2Root = sprintf('%s/..', $_SERVER['DOCUMENT_ROOT']);
        chdir($zf2Root);

        require 'vendor/autoload.php';
        $app = \Zend\Mvc\Application::init(require 'config/application.config.php');
        $login = $app->getServiceManager()->get('Zend\Authentication\AuthenticationService')->getStorage()->read();
        $login = null === $login ? $login : (string)$login;

        chdir($cwd);
        $config  = require($configFile);
        $config['disabled']  = null === $login;

        if (!$config['disabled']) {
            $config['uploadDir'] = str_replace(':login:', $login, KCFINDER_UPLOAD_DIR);
            $config['uploadURL'] = str_replace(':login:', $login, KCFINDER_UPLOAD_URL);
        }


        // Load session configuration
        foreach ($config as $key => $val)
            $this->config[$key] = ((substr($key, 0, 1) != "_") && isset($session[$key]))
                ? $session[$key]
                : $val;

        // Session data goes to 'self' element
        if (!isset($session['self']))
            $session['self'] = array();
        $this->values = &$session['self'];
    }

    public function getConfig() {
        return $this->config;
    }
}
