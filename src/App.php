<?php

	namespace Scraper;
	use Scraper\Configuration\Config;

	class App{

		public static function clean( $string ){
			$string = str_replace( ' ', '-', $string );
			return preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
		}

	}

?>