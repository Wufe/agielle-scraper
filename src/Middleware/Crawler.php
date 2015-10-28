<?php

	namespace Scraper\Middleware;
	use Carbon\Carbon;
	use Scraper\App;
	use Scraper\Configuration\Config;
	use Scraper\Middleware\Database;
	use Scraper\Middleware\Downloader;
	use Scraper\Middleware\Log;
	use Scraper\Models\Category;
	use Scraper\Models\Post;
	use Symfony\Component\DomCrawler\Crawler as DomCrawler;

	class Crawler{

		public static $archives = [];
		public static $posts 	= [];
		public static $count 	= 0;

		public static function crawl(){
			self::get_archives();
			self::parse_archives();
		}

		public static function get_archive( $arc_key ){
			$verbose = Config::$env == "dev" ? true : false;
			Log::log( "<blue>Downloading archive #".$arc_key." - ".self::$archives[ $arc_key ][ 'text' ]." - ".self::$archives[ $arc_key ][ 'href' ] );
			$crawler;
			$valid = false;
			do{
				$source = Downloader::get( self::$archives[ $arc_key ][ 'href' ] );
				//$source = Downloader::get( "http://localhost:8000" );
				$crawler = new DomCrawler( (string)$source );
				$crawler = $crawler->filter( '.base_pagina_articoli > div > a' );
				$count   = count( $crawler );
				Log::log( "<blue>Download ended." );
				if( $count == 0 ){
					Log::log( "<red>Found 0 articles. Why? Retrying to download." );
				}else{
					$valid = true;
				}
				sleep( 1 );
			}while( !$valid );
			
			if( $verbose ){
				Log::log( "<green>The download was successfull." );
				Log::log( "<cyan>Found ".$count." posts in the archive." );
			}
			foreach( $crawler as $art_key => $domElement ){
				if( $art_key >= Config::$status_post ){
					if( $verbose )
						Log::log( "<cyan>Article ID #".$art_key." of archive #".$arc_key."." );
					Config::$status_post = 0;
					$href = $domElement->getAttribute( 'href' );
					$text = trim( $domElement->nodeValue );
					$text = explode( "\n", $text );
					$title = trim( $text[ 0 ] );
					$date = [ 0, 0, 0, 0, 0, 0 ];
					preg_match( "#(\d+)\/(\d+)\/(\d+)\s\-\s(\d+):(\d+)#is", $text[ 1 ], $date );
					$date = Carbon::create( $date[ 3 ], $date[ 2 ], $date[ 1 ], $date[ 4 ], $date[ 5 ] );
					if( $verbose )
						Log::log( "<blue>[".$date."] - ".$title." - ".$href );
					$valid = false;
					$article = null;
					$categories = [];
					do{
						$source  = Downloader::get( $href );
						$crawl = new DomCrawler( (string)$source );
						preg_match( "#articolo\/(.+?)\/\d+#is", $href, $match );
						$name = trim( $match[ 1 ] );
						if( $verbose )
							Log::log( "<darkGreen>".$name );
						$article = $crawl->filter( '.dettagli_articoli_riassunto > p' );
						if( count( $article ) == 0 ){
							Log::log( "<red>Cannot get the article #".$art_key.". Retrying." );
						}else{
							foreach( $article as $art ){
								$article =  \utf8_decode( htmlentities( trim( $art->nodeValue ) ) );
								//var_dump( mb_detect_encoding( $art->nodeValue ) );
								if( $verbose )
									Log::log( "<darkGreen>".$article );
								break;
							}
						}
						$cats = $crawl->filter( '.dettagli_articoli_famiglie > a' );
						$categories = [];
						foreach( $cats as $cat ){
							$cat = $cat->nodeValue;
							$categories[] = $cat;
						}
						Log::log( "<darkGreen>Categories: ".implode( ", ", $categories ) );
						if( @!!$article )$valid = true;
					}while( !$valid );
					$post = new Post( $date, $article, $title, $name, $categories );
					$post->save();
					self::$count++;
					if( $verbose )
						Log::log( "<red>Count: ".self::$count );
					if( self::$count >= 15000 ){
						Log::log( "<red>Ended." );
						exit();
					}
				}
				
			}

		}

		public static function parse_archives(){
			$verbose = Config::$env == "dev" ? true : false;
			foreach( self::$archives as $arc_key => $archive ){
				if( $arc_key >= Config::$status_archive ){ // This function will resume old work stopped for some reason
					Config::$status_archive = 0;
					self::get_archive( $arc_key );
				}
			}
		}

		public static function get_archives(){
			for( $i = 1; $i <= 25; $i++ ){
				self::$archives[] = [ 'text' => $i, 'href' => "http://localhost:8000/".$i.".html" ];
			}

			// We are commenting this lines of code because the website returns 504-gateway timeout.
			// So I decided to save the index ( the heaviest ) pages into a local webserver, 
			// to be able to download them faster
			
			/*$verbose = Config::$env == "dev" ? true : false;
			Log::log( "Downloading archive.." );
			$source = Downloader::get( "/archivio/" );

			$crawler = new DomCrawler( (string)$source );
			$crawler = $crawler->filter( "ul > li > a" );

			if( $verbose )
				Log::log( "<cyan>Found ".count( $crawler )." archives." );

			foreach( $crawler as $domElement ){
				$href = $domElement->getAttribute( 'href' );
				$text = trim( $domElement->nodeValue );
				if( $verbose )
					Log::log( "<blue>".$text." - ".$href );
				self::$archives[] = [ 'text' => $text, 'href' => $href ];
			}
			if( count( self::$archives ) == 0 ){
				Log::log( "<red>Cannot get the list of archives. Why?" );
				exit();
			}*/

		}


	}

?>