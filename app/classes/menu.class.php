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
class Menu {

    /**
     * @var Array An array of MenuItem 
     */
    private $items;
    /**
     * @var String The system name of the menu
     */
    public $name;
    /**
     * @var String The printable name of the menu
     */
    public $label;
    /**
     * @var boolean|String The target link of this menu if exists, false otherwise 
     */
    public $uri = false;

    /**
     * Class constructor
     * @param String $name Menu name
     * @param String $label Menu label
     * @param String $uri Menu URI
     */
    public function __construct($name, $label, $uri = '') {
        $this->name = $name;
        $this->label = $label;
        if(!empty($uri)) {
            $this->uri = $uri;
        }
    }

    /**
     * Adds a menu item
     * @param String $name Menu item name
     * @param String $uri Menu item URI
     * @param Array $options Menu item options
     */
    public function add($name, $uri, array $options = array()) {
        $this->items[] = new MenuItem($name, $uri, $options);
    }

    /**
     * Adds a sub-menu
     * @param Menu $menu Menu to add
     */
    public function add_menu(Menu $menu) {
        $this->items[] = $menu;
    }

    /**
     * Renders the menu
     * @param Array $breadcrumb
     * @return string Rendered HTML menu
     */
    public function render($breadcrumb = array(), $depth = 0) {
        $app = SpeedcodeBox::getApp();
        $base_uri = $app->setting('public.base_uri');
        
        $output = '';

        if(count($this->items) > 0) {
            $ul_class = 'nav nav-pills nav-stacked';
            if($depth > 0) {
                $ul_class = 'dropdown-menu';
            }
            $output = '<ul id="menu-' . $this->name . '" class="' . $ul_class . '">' . _RN_;
            foreach($this->items as $item) {
                if($item instanceof Menu) {
                    $output .= '<li class="dropdown">';
                    $href = '#';
                    if($item->uri !== false) {
                        $href = $base_uri . $item->uri;
                    }
                    $output .= '<a href="' . $href . '" data-target="#" class="dropdown-toggle" data-toggle="dropdown">' . $item->label . ' <span class="caret"></span></a>';
                    $output .= $item->render($breadcrumb, ++$depth);
                    $output .= '</li>' . _RN_;
                }
                elseif($item instanceof MenuItem) {
                    $li_class = 'li-' . $this->name;
                    $a_class = 'a-' . $this->name;
                    if(isset($breadcrumb[$item->uri])) {
                        $li_class = $a_class = 'active';
                    }
                    $output .= $item->render($li_class, $a_class, $depth);
                }
            }
            $output .= '</ul>';
        }

        return $output;
    }

}

?>