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
 * app/classes/database.class.php
 * 
 * speedcode-box database handler
 *
 * @author          Laurent ASENSIO (laurent@mindescape.eu)
 * @package         speedcode-box
 * @category        Core classes
 * @version         2.0
 * @see             PDO
 * @copyright       Copyright (c) 2010
 * @license         http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL)
 */
class Database {

    /**
     * The current connection to database.
     * @access private
     * @var resource
     */
    private $pdo = null;
    /**
     * A link to the current result.
     * @access private
     * @var integer The query ID
     */
    private $statement = null;
    private static $_instance = null;
    
    private $tb_prefix = '';

    /**
     * Singleton pattern / Private class constructor
     */
    private function __construct() {
        $app = SpeedcodeBox::getApp();
        $this->tb_prefix = $app->setting('tb_prefix');
    }

    /**
     * Connects to server, select database and saves link identifier.
     * @param String $dsn
     * @param String $db_user
     * @param String $db_pwd
     */
    public function connect($dsn, $db_user, $db_pwd) {
        $this->statement = null;
        try {
            $this->pdo = new PDO($dsn, $db_user, $db_pwd);
            $this->query("SET NAMES 'UTF8'");
        }
        catch(PDOException $e) {
            error($e->getMessage(), Error::SYSTEM);
        }
    }

    /**
     * Singleton pattern / Instance getter
     * @return Database The singleton object instance
     */
    public static function getDB() {
        if(is_null(self::$_instance)) {
            self::$_instance = new Database();
        }
        return self::$_instance;
    }

    /**
     * Executes a query
     * @param String $query The SQL query string, tables must be in "{table}" format, without prefix, and values must be in ":var" format
     * @param Array $vars An array of variables to bind to the SQL query. Name usage is ":var"
     * @param boolean $debug Default false, if true print the resulting query and variables
     * @return Database Chaining purposes, allows to call query()->rows() directly
     */
    public function query($query, $vars = array(), $debug = false) {
        $matches = array();
        /**
         * Replaces all table names surrounded by {} with the actual table name, using the prefix
         */
        if(preg_match_all('#\{([^\}]+)\}#U', $query, $matches)) {
            foreach($matches[0] as $index => $match) {
                $query = str_replace($match, $this->tb_prefix . $matches[1][$index], $query);
            }
            unset($matches);
        }
        
        // Prepare the query with PDO
        $this->statement = $this->pdo->prepare($query);
        
        // Bind variables
        $this->bind($vars);

        // Execute query
        $this->statement->execute();

        if($debug) { // Debug trace
            error('SQL Query : ' . $query, Error::NOTICE);
            error('SQL Vars : <br />' . array_to_string($vars), Error::NOTICE);
        }
        
        return $this; // Chaining
    }

    /**
     * Allows to execute the last query with new variables values
     * @param Array $vars An array of variables to bind to the SQL query. Name usage is ":var"
     * @param boolean $debug Default false, if true print variables
     * @return Database Chaining purposes
     */
    public function execute($vars, $debug = false) {
        if($this->bind($vars)) { // Bind variables
            $this->statement->execute();
            if($debug) {
                error('SQL Vars : <br />' . array_to_string($vars), Error::NOTICE);
            }
        }
        return $this; // Chaining
    }

    /**
     * 
     * @param type $vars
     * @return boolean True if binding has been done, false otherwise
     */
    private function bind($vars) {
        if($this->statement == null) {
            return false;
        }

        foreach($vars as $name => $array) {
            $name = ':' . $name; // Adds ':' in front of variable name
            
            if(is_array($array) && isset($array['value']) && isset($array['datatype'])) { // Usage of array is possible
                $this->statement->bindValue($name, $array['value'], $array['datatype']);
            }
            else {
                $this->statement->bindValue($name, $array);
            }
        }

        return true;
    }

    /**
     * Count the number of lines from a previous SELECT or UPDATE query
     * @return integer|boolean The number of lines of the last query if possible, false otherwise.
     */
    public function count() {
        if($this->statement != null) {
            return $this->statement->rowCount();
        }
        return false;
    }

    /**
     * Returns the last insert id if exists, false otherwise
     * @return integer|boolean The last insert id if exists, false otherwise
     */
    public function insert_id() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Returns the current row of the last query if possible, false otherwise
     * @return array|boolean An array with the current line of the last query of possible, false otherwise
     * @todo Optimize the fetching if a field is specified (PDOStatement::fetchColumn)
     */
    public function row($field = '') {
        $out = false;

        if($this->statement != null) {
            $out = $this->statement->fetch(PDO::FETCH_ASSOC);
            if(!empty($field) && isset($out[$field])) {
                $out = $out[$field];
            }
        }

        return $out;
    }

    /**
     * Returns all rows of the last query if possible, false or empty array otherwise
     * @return array|boolean An array with the whole result of the last query if possible, 
     * an empty array if no results, false otherwise
     */
    public function rows() {
        if($this->statement != null) {
            return $this->statement->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return false;
    }

}

?>
