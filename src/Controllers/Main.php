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

				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh2", "Categoria Nuova" );
				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh2", "New Cat" );
				$post->save();
				//Log::log( "<blue>".$post->get_query() );

				

			Log::close_file();

		}

	}

?>