<?php

	namespace Scraper\Middleware;
	use GuzzleHttp\Client  as Guzzle;
	use Scraper\Middleware\Log;
	use Scraper\Configuration\Config;

	class Downloader{

		public static $tries = 10;
		
		public static function get( $url ){
			$client = new Guzzle( [ 'base_uri' => Config::$base_uri ]);
			$res = null;
			$tries = 0;
			$error = null;
			do{
				try{
					$res = $client->request( 'GET', $url, [ 'timeout' => 600, 'http_errors' => false ]);	
				}catch( \GuzzleHttp\Exception\ConnectException $e ){
					$error = "<red>ConnectException: ".$e->getMessage();
					Log::log( "<red>Retrying" );
				}catch( \GuzzleHttp\Exception\RequestException $e ){
					$error = "<red>RequestException: ".$e->getMessage();
					Log::log( "<red>Retrying" );
				}
				
				$tries++;
			}while( ( !$res || $res->getStatusCode() == 200 ) && $tries < self::$tries );
			if( $res == null ){
				Log::log( $error );
			}
			return $res != null ? $res->getBody() : false;
		}

	}

?>