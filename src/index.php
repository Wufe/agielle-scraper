<?php
	
	use Scraper\Middleware\Log;
	use Scraper\Controllers\Main;

	ini_set( "max_execution_time", "900000" );
	
	require( 'vendor/autoload.php' );
	Log::log( "<red>Starting main controller." );
	Main::main();


?>