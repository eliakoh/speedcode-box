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
 * app/classes/language.class.php
 * 
 * Multilingual support
 * 
 * Allows text replacements based on translations array
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.1
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Language {

    /**
     * @var String The absolute path to the language file
     */
    private $language_file = '';
    /**
     * @var Array Contains all the translations
     */
    private $translations = array();

    /**
     * Class constructor
     * @param string $lang The file name of the language without extension
     * @param boolean $module True if in-module system, false otherwise
     */
    public function __construct($lid, $module = false) {
        $this->set_language($lid, $module);
    }

    public function set_language($lid) {
        $db = Database::getDB();
        
        $rows = $db->query(new Query('select', array('source', 'translation'), 'translations', "lid = $lid"))->rows();
        foreach($rows as $row) {
            $this->translations[$row['source']] = $row['translation'];
        }
    }

    /**
     * Pick a translation
     * @param String $label The code for the translation, in fact the array key
     * @param Array $replacements Dynamic values to insert into the translation
     * @return boolean|string The translation if exists, false otherwise
     */
    public function get($label, $replacements = array()) {
        $translated = $label;
        if (isset($this->translations[$label])) {
            $translated = $this->translations[$label];
        } 
        if (!empty($replacements)) {
            if(!is_array($replacements)) {
                $replacements = array($replacements);
            }
            return vsprintf($translated, $replacements);
        } else {
            return $translated;
        }
    }

    /**
     * Merge an array with the translations array
     * @param array $array The array to merge with
     */
    public function merge($array) {
        $this->translations = array_merge($this->translations, $array);
    }

    /**
     * Getter for all translations
     * @return array An array containing all translations
     */
    public function get_all() {
        return $this->translations;
    }

}

?>