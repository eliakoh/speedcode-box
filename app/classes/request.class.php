<?php
/*
  speedcode-box - Laurent ASENSIO - laurent@mindescape.eu
  Copyright (C) 2011  Laurent ASENSIO

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * app/classes/request.class.php
 * 
 * speedcode-box request class
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Request {

    /**
     * @var String The current URI used 
     */
    private $uri = '/';
    /**
     * @var Request The singleton instance
     */
    private static $_instance;
    /**
     * @var Array Contains all $_POST data, sanitized
     */
    public $post;
    /**
     * @var Array Contains all $_GET data, sanitized
     */
    public $get;
    /**
     * @var Array Default uri
     */
    private $parsed_uri = array('module' => 'home', 'function' => 'index', 'parameters' => array(), 'admin' => false);

    /**
     * Class constructor
     */
    private function __construct() {
        $this->post = sanitize($_POST, true);
        $this->get = sanitize($_GET);

        if(isset($this->get['q'])) {
            if(!empty($this->get['q'])) {
                $this->set_uri($this->get['q']);
            }
            unset($this->get['q']);
        }
    }

    /**
     * @return Request The singleton instance
     */
    public static function getRequest() {
        if(is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    /**
     * $_GET getter
     * @param String $key The $key to get
     * @return mixed The value
     */
    public function get($key) {
        if(isset($this->get[$key])) {
            return $this->get[$key];
        }
        return false;
    }

    /**
     * $_POST getter
     * @param String $key The $key to get
     * @return mixed The value
     */
    public function post($key) {
        if(isset($this->post[$key])) {
            return $this->post[$key];
        }
        return false;
    }

    public function get_parsed_uri() {
        return $this->parsed_uri;
    }

    public function get_uri($absolute = false) {
        $current_uri = $this->uri;
        if($absolute) {
            $app = SpeedcodeBox::getApp();
            $current_uri = $app->setting('public.base_uri') . $current_uri;
        }
        return $current_uri;
    }

    public function set_uri($new_uri) {
        if(!empty($new_uri)) {
            /**
             * Removes the trailing / if present
             */
            if($new_uri{strlen($new_uri) - 1} == '/') {
                $new_uri = substr($new_uri, 0, -1);
            }

            $this->uri = $new_uri;
            parse_uri($new_uri, $this->parsed_uri);
        }
    }

}

?>
