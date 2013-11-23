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
 * app/classes/query.class.php
 * 
 * A MySQL Query maker
 * 
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         1.0
 * @copyright       Copyright (c) 2011
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Query {

    /**
     * SELECT, UPDATE or DELETE
     * @var String
     */
    public $type;
    /**
     * Contains tables name and aliases
     * @var Array
     */
    public $tables;
    /**
     * Contains fields and values
     * @var Array
     */
    public $fields;
    /**
     * WHERE and other group functions (GROUP BY, ORDER BY, LIMIT, ...)
     * @var String
     */
    public $condition;
    /**
     * A table prefix to use, the default is the constant TB_PREFIX
     * @var String
     */
    public $prefix;
    /**
     * Inner Joins
     * @var String
     */
    private $joins;

    /**
     * Instantiate the class with all parameters
     * @param string $type SELECT, UPDATE or DELETE
     * @param array $tables Tables names and aliases
     * @param array $fields Fields and values
     * @param string $condition WHERE and other group functions (GROUP BY, ORDER BY, LIMIT, ...)
     * @param string $prefix A table prefix to use, the default is the constant TB_PREFIX
     */
    public function __construct($type, $fields = array(), $tables = '', $condition = '1') {
        $this->type = $type;
        $this->tables = $tables;
        $this->fields = $fields;
        $this->condition = $condition;
        $this->prefix = SpeedcodeBox::getApp()->setting('tb_prefix');
    }

    /**
     * Construct the SQL tables list
     * @return string The SQL tables list
     */
    private function get_tables() {
        if(!is_array($this->tables)) {
            return '`' . $this->prefix . $this->tables . '`';
        }
        
        $out = '';
        $keys = array_keys($this->tables);
        $aliases = !is_numeric($keys[0]);
        foreach($this->tables as $key => $value) {
            $out .= '`' . $this->prefix . $value . '`';
            if($aliases) {
                $out .= ' ' . $key;
            }
            $out .= ', ';
        }
        $out = substr($out, 0, -2);
        return $out;
    }

    /**
     * Construct the SQL fields list
     * @return string The SQL fields list with their values
     */
    private function get_fields() {
        $out = '';
        foreach($this->fields as $key => $value) {
            if(strpos($value, '*') === FALSE && strpos($value, '(') === FALSE) {
                /**
                 * Classic field
                 */
                $value = str_replace('.', '`.`', $value);
                $out .= '`' . $value . '`';
                if(!is_numeric($key)) {
                    $out .= ' AS ' . $key;
                }
                $out .= ', ';
            }
            elseif(strpos($value, '*') === FALSE) {
                /**
                 * Function
                 */
                $out .= $value;
                if(!is_numeric($key)) {
                    $out .= ' AS ' . $key;
                }
                $out .= ', ';
            }
            else {
                /**
                 * The * field
                 */
                $out .= $value . ', ';
            }
        }
        $out = substr($out, 0, -2);
        return $out;
    }

    /**
     * Construct an SQL string with fields for update
     * @return string A SQL string made from fields for an update
     */
    private function get_fields_for_update() {
        $out = '';

        foreach($this->fields as $key => $value) {
            $key = str_replace('.', '`.`', $key);
            $out .= '`' . $key . '` = ';
            if(substr($value, 0, 5) != 'FSQL_') {
                $out .= '"' . $value . '", ';
            }
            else {
                $out .= substr($value, 5) . ', ';
            }
        }
        $out = substr($out, 0, -2);
        return $out;
    }

    /**
     * Add inner join into query
     * @param string $table
     * @param string $condition
     * @param string $alias
     * @return void
     */
    public function inner_join($table, $condition, $alias = '') {
        if(!empty($alias)) {
            $alias = ' ' . $alias;
        }
        $this->joins .= ' INNER JOIN `' . $this->prefix . $table . '`' . $alias . ' ON ' . $condition;
    }

    /**
     * Construct the final complete query string
     * @return string The final SQL query string
     */
    public function __toString() {
        switch(strtolower($this->type)) {
            case 'select':
                $out = strtoupper($this->type) . ' ' . $this->get_fields() . ' FROM ' . $this->get_tables() . $this->joins . ' WHERE ' . $this->condition;
                break;

            case 'update':
                $out = strtoupper($this->type) . ' ' . $this->get_tables() . ' SET ' . $this->get_fields_for_update() . ' WHERE ' . $this->condition;
                break;

            case 'insert':
                $out = strtoupper($this->type) . ' INTO ' . $this->get_tables() . ' SET ' . $this->get_fields_for_update();
                break;

            case 'delete':
                $out = strtoupper($this->type) . ' FROM ' . $this->get_tables() . ' WHERE ' . $this->condition;
                break;

            default:
                break;
        }
        return $out;
    }

}

?>
