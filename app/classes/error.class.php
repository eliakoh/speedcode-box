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
 * app/classes/error.class.php
 * 
 * speedcode-box error handler
 *
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         2.0
 * @see             errorstack.class.php
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Error {

    /**
     * @var String Content of error
     */
    public $content;
    /**
     *
     * @var integer Type of error (SYSTEM, FATAL, WARNING or NOTICE)
     */
    public $type;

    const SYSTEM = 0;
    const FATAL = 1;
    const WARNING = 2;
    const NOTICE = 3;

    /**
     * Class constructor
     * 
     * @param String $content Content of error
     * @param integer $type Type of error (use constant)
     */
    public function __construct($content, $type) {
        $this->content = $content;
        $this->type = $type;
    }

}

?>
