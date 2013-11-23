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
 * app/classes/user.class.php
 * 
 * speedcode-box current user management class
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class User {
    
    public $language = null;
    private static $_instance = null;
    public $label;
    public $profile_name;
    public $uid = 0;
    private $acl = array();
    
    /**
     * Class constructor (singleton)
     * Initialize current user
     */
    private function __construct() {
        if(!isset($_SESSION['user'])) {
            $this->label = t('system.anonymous-name');
            $this->profile_name = t('system.no-profile');
        }
        else {
            $this->uid = $_SESSION['user']['uid'];
            $this->label = $_SESSION['user']['label'];
            $this->profile_name = $_SESSION['user']['profile_name'];
            $this->language = $_SESSION['user']['language'];
            $this->acl = $_SESSION['user']['acl'];
        }
    }
    
    /**
     * Singleton
     * @return type User
     */
    public static function getUser() {
        if (is_null(self::$_instance)) {
            self::$_instance = new User();
        }
        return self::$_instance;
    }
    
    /**
     * Set default User language
     * @param integer $lid Language ID
     */
    public function default_language($lid) {
        if(is_null($this->language)) {
            $this->language = $lid;
        }
    }
    
    /**
     * Authenticate a user and retrieve profile, language and ACL
     * @param String $login
     * @param String $password
     * @return boolean True if user is authenticated, false otherwise
     */
    public function authenticate($login, $password) {
        $password = md5($password);
        $db = Database::getDB();
        $db->query('SELECT * FROM `{users}` WHERE login = :login AND password = :password AND status = 1', array('login' => $login, 'password' => $password));
        
        if($db->count() == 0) {
            return false;
        }
        
        $userdata = $db->row();
        
        $db->query('SELECT label FROM `{profiles}` WHERE id = :pid', array('pid' => $userdata['profile_id']));
        if($db->count() == 0) {
            error(t('system.profile-not-found'), Error::FATAL);
        }
        $profiledata = $db->row();
        
        $db->query('SELECT code FROM `{languages}` WHERE id = :lid', array('lid' => $userdata['language_id']));
        if($db->count() == 0) {
            error(t('system.language-not-found'), Error::FATAL);
        }
        $languagedata = $db->row();
        
        $db->query('SELECT name, module_id FROM `{acl}` A, `{pacl}` P WHERE A.id = P.acl_id AND P.profile_id = :pid', array('pid' => $userdata['profile_id']));
        $rows = $db->rows();
        $acldata = array();
        foreach($rows as $row) {
            $acldata[$row['module_id']][$row['name']] = true;
        }
        
        $_SESSION['user'] = array();
        
        $this->uid = $_SESSION['user']['uid'] = $userdata['id'];
        $this->label = $_SESSION['user']['label'] = $userdata['firstname'] . ' ' . $userdata['lastname'];
        $this->profile_name = $_SESSION['user']['profile_name'] = $profiledata['label'];
        $this->language = $_SESSION['user']['language'] = $languagedata['code'];
        $this->acl = $_SESSION['user']['acl'] = $acldata;
        
        return true;
    }
    
    /**
     * User's ACL getter
     * @param String $name AC to test
     * @return boolean True if user has $name AC, false otherwise
     */
    public function acl($mid, $name) {
        return isset($this->acl[$mid][$name]) ? $this->acl[$mid][$name] : false;
    }
}
?>