<?php
class DB {
	private static $instance;

	private function __construct() {
		global $dbhost, $dblogin, $dbpassword, $dbdatabase;
		if (!extension_loaded("mysqli"))
			trigger_error("You must have the mysqli extension installed.", E_USER_ERROR);
		else {
			$mysqli = @new MySQLi($dbhost, $dblogin, $dbpassword, $dbdatabase);
			if ($mysqli->connect_errno)
				die(sprintf("Connect Error (%s): %s", $mysqli->connect_errno, $mysqli->connect_error));

			$mysqli->set_charset('utf8');
			self::$instance = $mysqli;
		}
	}
	public static function getInstance() {
		if (is_null(self::$instance))
            $connection = new self();

        return self::$instance;
	}
	public static function exQuery($sql) {
		$connection = self::getInstance();
		if (!($result = $connection->query($sql)))
			exit(sprintf("Error: %s", $connection->error));
		return $result;
	}
	public static function insertID() {
		return self::$instance->insert_id;
	}
	public static function affectedRows() {
		return self::$instance->affected_rows;
	}
	public static function __callStatic($name, $args) {
		$connection = self::getInstance();
		if (method_exists($connection, $name))
			return call_user_func_array(array($connection, $name), $args);
		else {
			trigger_error('Unknown Method ' . $name . '()', E_USER_WARNING);
			return;
		}
    }
}