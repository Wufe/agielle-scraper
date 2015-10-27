<?php
	
	use Scraper\Middleware\Log;
	use Scraper\Controllers\Main;
	
	require( 'vendor/autoload.php' );
	Log::log( "<red>Starting main controller." );
	Main::main();


?>