<?php

	namespace Scraper\Controllers;

	use Scraper\Middleware\Log;
	use Scraper\Middleware\Downloader;
	use Scraper\Middleware\Database;
	use Scraper\Models\Post;
	use Carbon\Carbon;

	class Main{

		public static function main(){

			Log::open_file( Carbon::now()->format( "F-Y-g-i-A" ).".log" );
			
				//Log::log( "Downloading archive.." );
				//$source = Downloader::get( "http://www.agiellenews.it/archivio/" );

				Database::connect();

				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh1", [ "New Cat 1", "New Cat 2" ] );
				$post->save();
				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh2", [ "New Cat 2", "New Cat 3" ] );
				$post->save();

				

			Log::close_file();

		}

	}

?>