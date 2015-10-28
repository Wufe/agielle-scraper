<?php

	namespace Scraper\Configuration;

	class Config{

		public static $db_host   = "127.0.0.1";
		public static $db_user   = "wufe";
		public static $db_pass   = "123";
		public static $db_name 	 = "wp";

		public static $wp_prefix = "wp_";

		public static $base_uri  = "http://www.agiellenews.it";

		public static $env 	     = "dev"; // dev or prod

		public static $status_archive = 0;
		public static $status_post 	  = 0;

	}

?>