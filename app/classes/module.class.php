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
 * app/classes/module.class.php
 * 
 * speedcode-box module superclass
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Module {

    public $label;
    public $version;
    protected $name;
    protected $path;
    protected $app;
    protected $db;
    protected $user;
    protected $lang;
    protected $template;
    protected $conf;
    protected $request;

    /**
     * Class constructor
     * Initialize the module, parse the INI file, get a link to DB and User
     * @param type $name Name of module to load
     */
    public function __construct($name = '') {
        if (empty($name)) {
            /**
             * Heriting class
             */
            $name = strtolower(get_class($this));
        }

        $this->name = $name;
        $this->app = SpeedcodeBox::getApp();
        $this->request = $this->app->request;

        $this->path = ROOT . 'modules/' . $this->name;
        if (!is_dir($this->path)) {
            error(t('Module <strong>%s</strong> inexistant', $this->name), Error::SYSTEM);
        }

        $ini_file = $this->path . '/' . $this->name . '.ini';

        if (!file_exists($ini_file)) {
            error(t('Fichier de configuration du module <strong>%s</strong> non trouvé', $this->name), Error::SYSTEM);
        }

        $this->conf = parse_ini_file($ini_file, true);

        if ($this->conf == false) {
            error(t('Fichier de configuration du module <strong>%s</strong> incorrect', $this->name), Error::SYSTEM);
        }
        
        $this->label = $this->conf['main']['label'];
        $this->version = $this->conf['main']['version'];

        $this->db = Database::getDB();
        $this->user = User::getUser();
    }
    
    public function load_tpl($tpl_name) {
        $path_to_tpl = $this->path . '/' . basename($tpl_name) . '.tpl';
        
        if(!file_exists($path_to_tpl)) {
            error(t('Fichier template non trouvé', Error::FATAL));
        }
        
        $this->template = new Template($path_to_tpl, false, true);
    }
    
    public function render($data = array(), $name = '', $rm_tags = true) {
        if(count($data) > 0) {
            $this->template->data($data, $name);
        }
        if(count($this->form_errors) > 0) {
            foreach($this->form_errors as $value) {
                $this->data('system-input-error', 'input_error_' . $value);
            }
        }
        return $this->template->render($rm_tags);
    }
    
    public function get_acl() {
        return $this->conf['acl'];
    }
    
    public function get_menu() {
        return $this->conf['menu'];
    }
    
    public function get_ini_var($setting) {
        return $this->conf['custom'][$setting];
    }
    
    public function install() {
        return true;
    }
    
    public function db_create($data, $table = '') {
        if(empty($table) && isset($this->conf['main']['table'])) {
            $table = $this->conf['main']['table'];
        }
        
        if(!empty($table)) {
            $this->db->query(new Query('insert', $data, $table));
            return $this->db->insert_id();
        }
        else {
            error(t('system.empty-table'), Error::WARNING);
        }
        
        return false;
    }
    
    public function db_read($id, $table = '') {
        if(empty($table) && isset($this->conf['main']['table'])) {
            $table = $this->conf['main']['table'];
        }
        
        if(!empty($table)) {
            $this->db->query(new Query('select', array('*'), $table, "id = $id"));
            return $this->db->row();
        }
        else {
            error(t('Table vide'), Error::WARNING);
        }
        
        return false;
    }
    
    public function db_update($id, $data, $table = '') {
        if(empty($table) && isset($this->conf['main']['table'])) {
            $table = $this->conf['main']['table'];
        }
        
        if(!empty($table)) {
            $this->db->query(new Query('update', $data, $table, "id = $id"));
            return ($this->db->count() > 0);
        }
        else {
            error(t('system.empty-table'), Error::WARNING);
        }
        
        return false;
    }
    
    public function db_delete($id, $table = '') {
        if(empty($table) && isset($this->conf['main']['table'])) {
            $table = $this->conf['main']['table'];
        }
        
        if(!empty($table)) {
            $this->db->query(new Query('delete', array(), $table, "id = $id"));
            return ($this->db->count() > 0);
        }
        else {
            error(t('system.empty-table'), Error::WARNING);
        }
        
        return false;
    }
    
    public function db_list($condition = '1', $table = '') {
        if(empty($table) && isset($this->conf['main']['table'])) {
            $table = $this->conf['main']['table'];
        }
        
        if(!empty($table)) {
            $this->db->query(new Query('select', array('*'), $table, $condition));
            return $this->db->rows();
        }
        else {
            error(t('system.empty-table'), Error::WARNING);
        }
        
        return false;
    }
}

?>
