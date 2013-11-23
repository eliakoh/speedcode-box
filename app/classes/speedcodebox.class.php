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
 * app/classes/speedcodebox.class.php
 * 
 * speedcode-box main class
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2012
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class SpeedcodeBox {

    /**
     * An array containing the application settings
     * @var Array
     */
    private $settings;
    /**
     * true if the application is loaded, false otherwise
     * @var boolean
     */
    private $loaded = false;
    /**
     * The global data to inject into templates
     * @var Array
     */
    private $app_data = array();
    /**
     * An array containing the loaded modules
     * @var Array
     */
    private $loaded_modules = array();
    /**
     * The singleton instance
     * @var type SpeedcodeBox
     */
    private static $_instance = null;
    /**
     * A link to the global language object
     * @var Language
     */
    public $language;
    /**
     * A link to the global database object
     * @var Database
     */
    public $db;
    /**
     * A link to the global user object
     * @var User
     */
    public $user;
    /**
     * A link to the global request object
     * @var Request
     */
    public $request;
    /**
     * @var Template The main template object 
     */
    public $template;
    /**
     * @var Array An array containing the current breadcrumb 
     */
    public $breadcrumb = array();

    /**
     * Private constructor
     */
    private function __construct() {}

    /**
     * Returns an instance of SpeedcodeBox
     */
    public static function getApp() {
        if(is_null(self::$_instance)) {
            self::$_instance = new SpeedcodeBox();
        }
        return self::$_instance;
    }

    /**
     * Emergency stop function
     * @param String $reason An error to display
     */
    public function stop($reason) {
        $this->app_data['app.content'] = $reason;
        $this->render();
        exit;
    }

    /**
     * @return Language The global language object
     */
    public static function getLanguage() {
        $app = self::getApp();

        return $app->language;
    }

    /**
     * Initialize the speedcode-box
     * @param Array $settings Settings located in app/settings/$_SERVER['SERVER_NAME'].php
     * or app/settings/default.php 
     */
    public function initialize(array $settings) {
        $this->settings = $settings;

        $this->db = Database::getDB();
        $this->db->connect(
                $this->settings['db_dsn'], 
                $this->settings['db_user'], 
                $this->settings['db_pwd']
        );

        if(!isset($this->settings['default_language'])) {
            $this->settings['default_language'] = 'fr_FR';
        }
        
        if(!isset($this->settings['theme'])) {
            $this->settings['theme'] = 'default';
        }

        $this->language = new Language($this->settings['default_language']);

        $this->request = Request::getRequest();
        $this->user = User::getUser();

        $this->app_data['app.version']  = APP_VERSION;
        $this->app_data['app.name']     = APP_NAME;
        $this->app_data['app.title']    = $this->setting('public.base_name');
        $this->app_data['app.theme']    = $this->setting('public.base_uri') . 'templates/' . $this->setting('theme') . '/';

        $this->template = new Template('layout.tpl');

        $this->loaded = true;
    }

    /**
     * App status getter
     * @return boolean True if app is loaded, false otherwise
     */
    public function is_loaded() {
        return $this->loaded;
    }

    /**
     * Execution of the application
     * Loads the userbar, the admin menu, and the current module and function to call
     */
    public function execute() {
        /**
         * Check if current URI is an alias
         */
        $this->db->query("SELECT M.path FROM `{menus}` M, `{aliases}` A WHERE A.path = :alias AND A.menu_id = M.id", array('alias' => $this->request->get_uri()));
        if($this->db->count() > 0) {
            $real_path = $this->db->row('path');
            $this->request->set_uri($real_path);
        }
        
        /**
         * Parse current URI
         */
        $parsed_uri = $this->request->get_parsed_uri();
        if($parsed_uri['admin']) {
            /**
             * Special treatment for admin URLs here
             */
            $parsed_uri['function'] = 'admin_' . $parsed_uri['function'];
        }

        /**
         * Loads module, test function and parameters
         * 
         * Call the module's function
         */
        if($parsed_uri['module'] == 'home' && $this->setting('default_module') != false) {
            $parsed_uri['module'] = $this->setting('default_module');
        }
        
        if ($this->user->uid != 0 && $this->user->language != $this->settings['default_language']) {
            $this->language->set_language($this->user->language);
        }
        else {
            $this->user->default_language($this->settings['default_language']);
        }
        
        /**
         * Cache management
         * @todo Find a way to save title, metas and breadcrumb with the cache
         */
        if(count($this->request->post) == 0 && $this->setting('use_cache')) {
            $module_hash = base64_encode(serialize(
                    array(  'module'    => $parsed_uri['module'],
                            'function'  => $parsed_uri['function'],
                            'params'    => $parsed_uri['parameters'],
                            'uid'       => $this->user->uid,
                        )));
            $result = cache_get($module_hash);
            if($result !== false) {
                $module_tpl = $result;
            }
            else {
                $module_tpl = $this->module_exec($parsed_uri['module'], $parsed_uri['function'], $parsed_uri['parameters']);
                cache_set($module_hash, $module_tpl);
            }
        }
        else {
            $module_tpl = $this->module_exec($parsed_uri['module'], $parsed_uri['function'], $parsed_uri['parameters']);
        }
            
        /**
         * Render blocks
         */
        $blocks = $this->db->query("SELECT B.name as block_name, M.name as module_name FROM `{blocks}` B, `{modules}` M WHERE B.module_id = M.id AND B.status = 1 AND M.status = 1")->rows();
        foreach($blocks as $block) {
            $tpl_varname = 'block.' . $block['module_name'] . '.' . $block['block_name'];
            $block_tpl = $this->render_block($block['module_name'], $block['block_name']);
            $this->template->data($block_tpl, $tpl_varname);
        }
        
        $this->app_data['app.user.label'] = $this->user->label;
        $this->app_data['app.breadcrumb'] = $this->render_breadcrumb();
        $this->app_data['app.content']    = $module_tpl;

        $this->render();
    }
    
    /**
     * Load a module, checking existence in database and user access
     * @param String $module The module name to load
     * @return Module An instanciated module object
     */
    public function load_module($module) {
        $mrow = $this->get_module_infos($module);
        
        if($mrow === false) {
            /**
             * Module could not be found in database
             */
            error(t('Le module <strong>%s</strong> n\'est pas activé', array($module)), Error::WARNING);
            return false;
        }
        elseif(!$mrow['public'] && !$this->user->acl($mrow['id'], $module)) {
            /**
             * User can't access to it
             */
            error(t('Accès interdit', array($module)), Error::NOTICE);
            return false;
        }
        
        if(isset($this->loaded_modules[$module])) {
            /**
             * Use a previously saved object
             */
            $module_object = $this->loaded_modules[$module];
        }
        else {
            /**
             * Require module file and instanciate the class
             */
            $module_file = ROOT . 'modules/' . $module . '/' . $module . '.php';
            if(!file_exists($module_file)) {
                error(t('Fichier module non trouvé'), Error::SYSTEM);
                return false;
            }
            require_once($module_file);
            $module_object = new $module();
            /**
             * Save the module object for further use
             */
            $this->loaded_modules[$module] = $module_object;
        }
        
        return $module_object;
    }
    
    /**
     * Load a module and execute a function with parameters
     * @param String $module The module name to load
     * @param String $function The function name to execute
     * @param Array $parameters An array containing all the parameters to pass
     * @return boolean|String False if module could not be loaded, the generated template otherwise 
     */
    private function module_exec($module, $function, $parameters) {
        $module_object = $this->load_module($module);
        
        if($module_object === false) {
            return false;
        }
        
        if(!method_exists($module_object, $function)) {
            error(t('Fonction <strong>%s</strong> inexistante', $function), Error::SYSTEM);
        }
        
        $module_tpl = $module_object->$function($parameters);

        return $module_tpl;
    }
    
    /**
     * Retrieve basic information on a named module
     * @param String $module_name Name of module to get infos from
     * @return boolean|Array False if module could not be found in database, an array containing module ID and public attribute otherwise
     */
    private function get_module_infos($module_name) {
        $this->db->query("SELECT id, public FROM `{modules}` WHERE name = :module_name AND status = 1", array('module_name' => $module_name));
        if($this->db->count() == 0) {
            return false;
        }
        return $this->db->row();
    }

    /**
     * Adds data to global data array
     * @param array $data
     */
    public function add_data(array $data) {
        $this->app_data = array_merge($this->app_data, $data);
    }

    /**
     * Render the breadcrumb in HTML
     * @return String HTML Breadcrumb
     */
    public function render_breadcrumb() {
        $base_uri = SpeedcodeBox::getApp()->setting('public.base_uri');
                
        $output = '<ul class="breadcrumb">' . _RN_;
        if(empty($this->breadcrumb)) {
            $output .= '<li class="active">' . t('Accueil') . '</li>' . _RN_;
        }
        else {
            $output .= '<li><a href="' . $base_uri . '">' . t('Accueil') . '</a></li>' . _RN_;
            $last_item = array_pop($this->breadcrumb);
            foreach($this->breadcrumb as $item) {
                $output .= '<li><a href="' . $base_uri . $item['uri'] . '">' . $item['label'] . '</a></li>' . _RN_;
            }
            $output .= '<li class="active">' . $last_item['label'] . '</li>' . _RN_;
            
            $this->breadcrumb[] = $last_item;
        };
        $output .= '</ul>' . _RN_;
        
        return $output;
    }
    
    /**
     * Loads a module and render a formatted block
     * @param String $module Name of module which contains the block
     * @param String $block Name of block function
     * @return String A block's content
     */
    private function render_block($module, $block) {
        $parsed_uri = $this->request->get_parsed_uri();
        
        $block_tpl = '<div id="block-' . $module . '-' . $block . '">' . $this->module_exec($module, 'block_' . $block, $parsed_uri['parameters']) . '</div>';
        
        return $block_tpl;
    }

    /**
     * Global setting getter
     * @param String $key Name of the setting to retrieve
     * @return String|boolean Returns the setting if it exists, false otherwise
     */
    public function setting($key) {
        $ar_keys = explode('.', $key);

        if(count($ar_keys) > 1) {
            $tmp_setting = $this->settings;
            foreach($ar_keys as $value) {
                if(isset($tmp_setting[$value])) {
                    $tmp_setting = $tmp_setting[$value];
                }
                else {
                    $tmp_setting = false;
                    break;
                }
            }
            return $tmp_setting;
        }
        else {
            if(isset($this->settings[$key])) {
                return $this->settings[$key];
            }
        }
        return false;
    }
    
    /**
     * Raw modules list getter, scan the modules directory
     * @return Array An array containing the modules names 
     */
    public function get_raw_modules_list() {
        $base_path = ROOT . 'modules';
        
        $list = array();
        
        $dh = opendir($base_path);
        
        while($file = readdir($dh)) {
            if($file != '.' && $file != '..' && is_dir($base_path . '/' . $file)) {
                $list[] = $file;
            }
        }
        
        closedir($dh);
        
        return $list;
    }
    
    /**
     * Raw loading of module, without database or ACL check
     * @param String $module_name Name of module to load
     * @return Module The module object 
     */
    public function load_module_raw($module_name) {
        $module_file = ROOT . 'modules/' . $module_name . '/' . $module_name . '.php';
        if(!file_exists($module_file)) {
            error(t('Fichier module non trouvé : <strong>%s</strong>', $module_file), Error::SYSTEM);
        }
        require_once($module_file);
        return new $module_name();
    }

    /**
     * Renders the application in HTML
     * Generates the errors, inject the global data and renders the template
     */
    private function render() {
        $error_stack = ErrorStack::getErrorStack();
        
        if(isset($this->template)) {
            $this->template->data($error_stack->render(), 'app.error');

            $this->template->data($this->app_data);

            print $this->template;
        }
    }

}
?>