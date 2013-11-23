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
 * app/classes/menuitem.class.php
 * 
 * speedcode-box menu item class
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class MenuItem {

    /**
     * @var String The name of the item to print 
     */
    public $name;
    /**
     * @var String The target of the item 
     */
    public $uri;
    /**
     * @var Array Attributes to add to the link
     */
    private $options;

    /**
     * Class constructor
     * @param String $name Item's printed label
     * @param String $uri Item's target URI
     * @param Array $options Item's options
     */
    public function __construct($name, $uri, array $options = array()) {
        $app = SpeedcodeBox::getApp();
        
        $this->name = $name;
        $this->uri = $app->setting('public.base_uri') . $uri;
        $this->options = $options;
    }

    /**
     * Render the menu item
     * @param String $liclass A class to put on the LI tag
     * @param type $aclass A class to put on the A tag
     * @return String An HTML representation of the menu item
     */
    public function render($liclass, $aclass, $depth) {
        $attributes = '';
        foreach ($this->options as $name => $value) {
            $attributes .= ' ' . $name . '="' . $value . '"';
        }
        return '<li class="' . $liclass . '"><a href="' . $this->uri . '" class="' . $aclass . '"' . $attributes . '>' . $this->name . '</a></li>' . _RN_;
    }

}