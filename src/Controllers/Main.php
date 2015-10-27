<?php

	namespace Scraper\Controllers;

	use Scraper\Middleware\Log;

	class Main{

		public static function main(){

			Log::open_file( "log" );
			Log::log( "prova2" );
			Log::close_file();

		}

	}

?>