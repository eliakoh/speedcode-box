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
 * app/kernel/helpers_system.php
 * 
 * System helpers functions
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Helpers
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */

/**
 * Return a pre-formatted string to print a title
 * @param string $title The title to print
 * @return string HTML pre-formatted title
 */
function title($title) {
    return '<div class="system-title">' . $title . '</div>' . _RN_;
}

/**
 * Return a pre-formatted string to print a confirmation message
 * @param string $title The confirmation message to print
 * @return string HTML pre-formatted confirmation message
 */
function confirm($message) {
    return '<div class="alert alert-success">' . $message . '</div>' . _RN_;
}

/**
 * Return a pre-formatted string to print an error message
 * @param string $title The errpr message to print
 * @return string HTML pre-formatted error message
 */
function error($message, $type = Error::NOTICE) {
    ErrorStack::getErrorStack()->push(new Error($message, $type));
}

/**
 * Translation helper
 * 
 * @param String $lindex A string index of the translations array
 * @param Array $replacements Dynamic values to be injected into the translated text
 * @return String Corresponding translation
 */
function t($lindex, $replacements = array()) {
    $l = SpeedcodeBox::getLanguage();

    if(!is_null($l)) {
        return sanitize($l->get($lindex, $replacements), true);
    }
}

function set_title($new_title) {
    $app = SpeedcodeBox::getApp();
    $app->add_data(array('app.title' => $new_title));
    $app->add_data(array('app.meta_title' => $app->setting('public.base_name') . ' - ' . $new_title));
}

function set_meta_title($new_meta_title) {
    $app = SpeedcodeBox::getApp();
    $app->add_data(array('app.meta_title' => $new_meta_title));
}

function add_breadcrumb_item($uri, $label) {
    $app = Speedcodebox::getApp();
    $app->breadcrumb[] = array('uri' => $uri, 'label' => $label);
}

/*
 * Recursive function to build a menu
 */
function build_menu($menu_id) {
    $db = Database::getDB();
    
    $rows = $db->query('SELECT * FROM `{menus}` WHERE menu_id = :menu_id ORDER BY `weight` ASC', array('menu_id' => $menu_id))->rows();
    if(empty($rows)) {
        return false;
    }
    
    $row = $db->query('SELECT * FROM `{menus}` WHERE id = :menu_id', array('menu_id' => $menu_id))->row();
    $menu = new Menu($row['name'], $row['label'], $row['path']);
    
    foreach($rows as $row) {
        $sub = build_menu($row['id']);
        if($sub !== false) {
            $menu->add_menu($sub);
        }
        else {
            $menu->add($row['label'], $row['path']);
        }
    }
    
    return $menu;
}

function cache_get($hash) {
    $c = new Cache($hash);
    return $c->get();
}

function cache_set($hash, $content) {
    $c = new Cache($hash);
    return $c->set($content);
}
?>