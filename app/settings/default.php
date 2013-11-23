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
 * app/settings/default.php
 * 
 * speedcode-box default settings file
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Settings
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
/**
 * Do NOT use dots in the settings keys
 */
$_settings = array(
    'db_dsn'            => 'mysql:dbname=speedcodebox;host=127.0.0.1',
    'db_user'           => 'speedcodebox',
    'db_pwd'            => 'speedcodebox',
    'tb_prefix'         => 'scb_',
    'salt'              => '1234567890',
    'default_language'  => 'fr_FR',
    'default_module'    => 'home',
    'use_cache'         => true,
    'cache_duration'    => 3600,
    'cache_path'        => ROOT . 'app/tmp',
    'public'            => array(
                            'base_uri'          => 'http://speedcode-box.mindescape.eu/',
                            'base_name'         => 'The Speedcode Box',
                            'base_copyright'    => 'Copyright 2011 laurent@mindescape.eu',
                         ),
    'libraries'         => array(
                        //'krumo' => 'class.krumo.php',
    ),
);
?>