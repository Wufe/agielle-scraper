<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;
	use Scraper\Configuration\Config;
	use Scraper\App;

	class Database{

		public static $conn = null;

		public static function connect(){
			$verbose = Config::$env == "dev" ? true : false;
			if( $verbose ){
				Log::log( "<blue>Connecting to MySQL ".Config::$db_user."@".Config::$db_host.".. " );
			}
			$mysqli = @new \mysqli( Config::$db_host, Config::$db_user, Config::$db_pass, Config::$db_name );
			if( $mysqli->connect_errno ){
				Log::log( "<red>[".$mysqli->connect_errno."] ".$mysqli->connect_error );
				exit();
			}else{
				self::$conn = $mysqli;
				if( $verbose ){
					Log::log( "<green>Connected." );
				}
			}
		}

		public static function exec( $query ){
			$exec = self::$conn->query( $query );
			return !$exec ? false : $exec;
		}

		public static function escape( $value ){
			return self::$conn->real_escape_string( $value );
		}

	}

?>