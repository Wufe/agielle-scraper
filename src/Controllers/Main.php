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

				//echo htmlentities( \utf8_decode(  "(AGIELLE) - Galvagni (Cisl): Milano Ã¨ una pentola a pressione, reagire subito - RPT" ) );
			/*	$query = "SELECT ID as 'identification', post_title FROM wp_posts";
				$exec = Database::exec( $query );
				while( $res = $exec->fetch_assoc() ){
					//Log::log( $res[ 'post_title' ] );
					$old = $res[ 'post_title' ];
					$new = htmlentities( $old );
					$query = "UPDATE wp_posts SET post_title = '".Database::escape( @!!$new ? $new : $old )."' WHERE id = ".$res[ 'identification' ];
					Log::log( "<red>".$query );
					
					Database::exec( $query );
					//exit();
					Log::log( "Updated post #".$res[ 'identification' ] );
				}*/
				

				/*$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh1", [ "New Cat 1", "New Cat 2" ] );
				$post->save();
				$post = new Post( Carbon::now(), "Wassup bruh, dis is the contnt", "Taitol", "wassup-bruh2", [ "New Cat 2", "New Cat 3" ] );
				$post->save();*/

				

			Log::close_file();

		}

	}

?>