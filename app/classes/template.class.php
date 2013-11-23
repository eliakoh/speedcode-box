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
 * app/classes/template.class.php
 * 
 * speedcode-box template engine and parser
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.1
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Template {

    /**
     * @var SpeedcodeBox A link to the SpeedcodeBox instance
     */
    private $app;
    /**
     * @var String The absolute path to the template file
     */
    private $file;
    /**
     * @var String The HTML content
     */
    private $content;
    /**
     * @var Array An array containing all values to inject 
     * into the template content
     */
    private $data = array();

    /**
     * Class constructor
     * @param String $template Path to the template file or an HTML formatted string
     * @param boolean $is_string True to use $template as a String, false to use as a file
     */
    public function __construct($template, $is_string = false, $path_in_template = false) {
        $this->app = SpeedcodeBox::getApp();
        
        $path = '';
        
        if(!$path_in_template) {
            $path = ROOT . 'templates/' . $this->app->setting('theme') . '/';

            if(!is_dir($path) && $this->app->setting('theme') != 'default') {
                $path = ROOT . 'templates/default/';
            }
        }
        
        if (!$is_string && file_exists($path . $template)) {
            /**
             * Load from file
             */
            $this->file = $template;
            $this->content = file_get_contents($path . $template);
        } elseif ($is_string) {
            /**
             * Load from string
             */
            $this->content = $template;
        } else {
            /**
             * System error
             */
            if($this->app->is_loaded()) {
                error('Template file not found "' . $template . '"', Error::SYSTEM);
            }
            else {
                print 'FATAL Error : Template file not found "' . $template . '"';
            }
        }
    }

    /**
     * Insert all public settings ("public" array of settings)
     */
    private function inject_public_settings() {
        $array = $this->app->setting('public');

        foreach ($array as $key => $value) {
            $this->inject_data($value, $key);
        }
    }

    /**
     * Remove a {tag}{/tag} area, do nothing if area does not exists
     * @param String $areaname The name of the area to remove
     */
    public function remove($areaname) {
        $this->content = preg_replace('|\{' . $areaname . '\}(.*)\{/' . $areaname . '\}|Ums', '', $this->content);
    }

    /**
     * Return the content of a {tag}{/tag} area, false if area does not exists
     * @param string $areaname The name of the area to retrieve
     */
    public function get($areaname) {
        preg_match('|\{' . $areaname . '\}(.*)\{/' . $areaname . '\}|Ums', $this->content, $regs);
        if (isset($regs[1])) {
            $out = $regs[1];
        } else {
            $out = false;
        }
        return $out;
    }

    /**
     * Replaces an area by a string, do nothing if area does not exists
     * @param String $areaname The name of the area to replace
     * @param String $replace The value to insert into the area
     */
    public function replace($areaname, $replace) {
        $this->content = preg_replace('|\{' . $areaname . '\}(.*)\{/' . $areaname . '\}|Ums', $replace, $this->content);
    }

    /**
     * Adds data to the template object
     * This data will be stacked into an array and injected 
     * @param String $value
     * @param String $name 
     */
    public function data($value, $name = '') {
        if (!empty($name)) {
            $this->data[$name] = $value;
        } else {
            //$this->data = array_merge($this->data, $value); // WHY U NO WORK ?
            $this->data[] = $value;
        }
    }

    /**
     * Replaces a string - or an array - by a string - or by the pairs of keys / values
     * @param string|array $value The value to insert, or an array to inject
     * The array may be looped if $name correspond to an area, or if a key of the array correspond to an area
     * @param string $name The name of the tag to replace, or of an area to loop
     */
    private function inject_data($value, $name = '') {
        if (is_array($value)) {
            uasort($value, 'ar_compare'); // Prioritize area replacements
            if (empty($name)) {
                /**
                 * No name, looping the array for replacing
                 */
                foreach ($value as $key => $data) {
                    if ($data === ':empty:') {
                        /**
                         * Remove the area into the loop
                         */
                        $this->remove($key);
                    } else {
                        $this->inject_data($data, $key);
                    }
                }
            } else {
                /**
                 * Looping with buffer for iterating into the $name area
                 */
                $buffer = '';
                $area = $this->get($name);
                if ($area !== FALSE) {
                    $i = 1;
                    ksort($value);
                    foreach ($value as $key => $data) {
                        $item = new Template($area, true);
                        $data['app.row-number'] = $i;
                        $item->inject_data($data);
                        $buffer .= $item->render(false);
                        $i++;
                    }
                    $this->replace($name, $buffer);
                }
            }
        } elseif ($name != '') {
            /**
             * Single replacement
             */
            $this->content = str_replace('{' . $name . '}', $value, $this->content);

            if ($value == '' || $value == '0000-00-00' || $value == '0000-00-00 00:00:00' || $value == '00/00/0000') {
                $this->remove('a_' . $name);
            }
        }
    }

    /**
     * Final processing and rendering of the template
     * @param boolean $rm_tags Remove all resulting tags if set to true, don't process the resulting tags otherwise
     */
    public function render($rm_tags = true) {
        $this->inject_public_settings();
        $this->inject_data($this->data);

        preg_match_all("|\{([^}]+)\}|U", $this->content, $regs, PREG_PATTERN_ORDER); //[/A-Za-z_\-\.]*
        $regs = $regs[1];
        if (!empty($regs)) {
            foreach ($regs as $reg_value) {
                if(substr($reg_value, 0, 2) == 't:') {
                    $source = substr($reg_value, 2);
                    $this->inject_data($this->app->language->get($source), $reg_value);
                }
                else {
                    $ar_value = explode('.', $reg_value, 2);
                    if (count($ar_value) > 1) {
                        $reg_type = $ar_value[0];
                        $value = $ar_value[1];

                        switch ($reg_type) {
                            case 'get':
                                /**
                                 * GET variable
                                 */
                                $this->inject_data($this->app->request->get($value), $reg_value);
                                break;

                            case 'post':
                                /**
                                 * POST variable
                                 */
                                $this->inject_data($this->app->request->post($value), $reg_value);
                                break;

                            case 'tpl':
                                /**
                                 * Template injection
                                 */
                                $tmp_tpl = new Template($value . '.tpl');
                                $tmp_tpl->data($this->data);
                                $this->inject_data($tmp_tpl->render($rm_tags), $reg_value);
                                break;
                        }
                    }
                }
            }
        }
        /**
         * Removing all resulting tags
         */
        if ($rm_tags) {
            $this->content = preg_replace('|\{(/?[A-Za-z0-9_\-\.]+)\}|Ums', '', $this->content);
        }

        return stripslashes($this->content);
    }

    /**
     * Alias of the render() function, shortcut to use with implicit casting
     */
    public function __toString() {
        return $this->render();
    }
}
?>