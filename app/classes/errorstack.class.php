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
 * app/classes/errorstack.class.php
 * 
 * speedcode-box error stack handler
 *
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         2.0
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class ErrorStack {

    /**
     * @var ErrorStack The instance
     */
    private static $_instance = null;
    /**
     *
     * @var Array An array of errors
     */
    private $errors = array();

    private function __construct() {
        
    }

    public static function getErrorStack() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ErrorStack();
        }

        return self::$_instance;
    }

    public function push(Error $e) {
        if ($e->type != Error::FATAL && $e->type != Error::SYSTEM) {
            array_push($this->errors, $e);
        } else {
            $app = SpeedcodeBox::getApp();
            $app->stop('<div id="system-error"><span class="error-title">SYSTEM ERROR</span><br />' . $e->content . '</div>');
        }
    }

    public function render() {
        $output = '';
        if (count($this->errors) > 0) {
            $output =   '<div class="alert alert-danger alert-dismissable">' . _RN_ .
                        '   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . _RN_ .
                        '   <h4>' . t('Erreurs') . '</h4>' . _RN_ .
                        '<ul>';
            foreach ($this->errors as $error) {
                $output .= '<li>' . $error->content . '</li>' . _RN_;
            }

            $output .= '</ul>' . _RN_ . '</div>';
        }

        return $output;
    }

    public function __toString() {
        return $this->render();
    }

}
/*

</div>'
 */
?>
