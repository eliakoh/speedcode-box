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
 * app/classes/cache.class.php
 * 
 * cache management
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2012
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Cache {
    
    private $path;
    private $hash;
    private $filepath;
    private $ctime = null;
    
    public function __construct($hash, $path = 'default') {
        $app = SpeedcodeBox::getApp();
        
        $this->hash = $hash;
        if($path == 'default') {
            $path = $app->setting('cache.path');
        }
        $this->path = $path;
        $this->filepath = $this->path . '/' . $this->hash;
        
        if(file_exists($this->filepath)) {
            $fh = fopen($this->filepath, 'r');
            $stats = fstat($fh);
            fclose($fh);
            $this->ctime = $stats['ctime'];
        }
    }
    
    public function get() {
        $app = SpeedcodeBox::getApp();
        
        if(is_null($this->ctime)) {
            return false;
        }
        if((time() - $this->ctime) > $app->setting('cache.duration')) {
            return false;
        }
        return file_get_contents($this->filepath);
    }
    
    public function set($content) {
        file_put_contents($this->filepath, $content);
        return true;
    }
}
?>