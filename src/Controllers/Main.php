<?php

	namespace Scraper\Controllers;

	use Carbon\Carbon;
	use Scraper\Middleware\Crawler;
	use Scraper\Middleware\Database;
	use Scraper\Middleware\Downloader;
	use Scraper\Middleware\Log;
	use Scraper\Models\Post;

	class Main{

		public static function main(){

			Log::open_file( Carbon::now()->format( "F-Y-g-i-A" ).".log" );

				Database::connect();
			
				Crawler::crawl();


				

				/*$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh1", [ "New Cat 1", "New Cat 2" ] );
				$post->save();
				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh2", [ "New Cat 2", "New Cat 3" ] );
				$post->save();*/

				

			Log::close_file();

		}

	}

?>