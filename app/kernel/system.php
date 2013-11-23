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
 * app/kernel/system.php
 * 
 * Loads the libraries and creates the SpeedcodeBox application
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Boot
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
session_start();

/**
 * Adding libraries
 */
if (isset($_settings['libraries']) && is_array($_settings['libraries'])) {
    $libraries = $_settings['libraries'];
    unset($_settings['libraries']);

    foreach ($libraries as $folder_name => $file_name) {
        $path_to_library_file = ROOT . 'app/libraries/' . $folder_name . '/' . $file_name;
        if (file_exists($path_to_library_file)) {
            require_once($path_to_library_file);
        }
    }
}
/**
 * Getting an instance of the SpeedcodeBox class
 * In fact, this call *MUST* be the first for creating the object
 */
$_app = SpeedcodeBox::getApp();
/**
 * Initialize app with settings
 */
$_app->initialize($_settings);
/**
 * Executing main process
 */
$_app->execute();
?>