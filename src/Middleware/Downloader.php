<?php

	namespace Scraper\Middleware;
	use \GuzzleHttp\Client  as Guzzle;
	use \Scraper\Middleware\Log;

	class Downloader{
		
		public static function get( $url ){
			$client = new Guzzle();
			$res = null;
			$tries = 0;
			$error = null;
			do{
				try{
					$res = $client->request( 'GET', $url, [ 'timeout' => 10, 'http_errors' => false ]);	
				}catch( \GuzzleHttp\Exception\ConnectException $e ){
					$error = "ConnectException: ".$e->getMessage();
				}catch( \GuzzleHttp\Exception\RequestException $e ){
					$error = "RequestException: ".$e->getMessage();
				}
				
				$tries++;
			}while( ( !$res || $res->getStatusCode() == 200 ) && $tries < 10 );
			if( $res == null ){
				Log::log( $error );
			}
			return $res != null ? $res->getBody() : false;
		}

	}

?>