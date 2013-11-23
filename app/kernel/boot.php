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
 * app/kernel/boot.php
 * 
 * speedcode-box boot file
 *
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Boot
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
ini_set('date.timezone', 'Europe/Paris');
ini_set('session.gc_maxlifetime', 0);
ini_set('register_globals', 0);
header('Content-Type: text/html; charset=utf-8');
/**
 * The absolute path to the program root
 */
define('ROOT', realpath(dirname(__FILE__) . '/../../') . '/');
/**
 * Base name
 */
define('APP_NAME', 'speedcode-box');
/**
 * Current version
 */
define('APP_VERSION', '1.0-rc1');
/**
 * Debug mode (only for development purposes)
 */
define('_DEBUG_', true);

/**
 * Carriage return definition
 */
define('_RN_', "\n");

/**
 * Setting locale config
 */
setlocale(LC_ALL, 'fr_FR.utf8');

/**
 * Multi-app settings loading
 */
$host = str_replace('www.', '', $_SERVER['SERVER_NAME']);

/**
 * Removes the port number if present
 * 
 * *****Not sure it's necessary with the use of SERVER_NAME instead of HTTP_HOST******
 */
//$host = preg_replace('/:[0-9]+/', '', $host);

$setting_file = ROOT . 'app/settings/' . $host . '.php';

/**
 * Default config file in case of single app
 */
$default_setting_file = ROOT . 'app/settings/default.php';
$_default_mode = false;

if (file_exists($setting_file)) {
    require_once($setting_file);
} elseif (file_exists($default_setting_file)) {
    /**
     * Starting with default setting file
     */
    require_once($default_setting_file);
    $_default_mode = true;
} else {
    if (_DEBUG_) {
        print '<strong>SYSTEM ERROR</strong> : No default settings file';
    }
    /**
     * No setting file, exiting
     */
    exit;
}

if (!is_array($_settings)) {
    print '<strong>SYSTEM ERROR</strong> : Incorrect settings format';
    /**
     * No settings, exiting
     */
    exit;
}

/**
 * Global functions
 */
require_once(ROOT . 'app/kernel/functions.php');
/**
 * System helpers
 */
require_once(ROOT . 'app/kernel/helpers_system.php');
/**
 * Form helpers
 */
require_once(ROOT . 'app/kernel/helpers_form.php');

/**
 * Loads the system
 */
require_once(ROOT . 'app/kernel/system.php');
?>