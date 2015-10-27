<?php
	
	namespace Scraper\Middleware;
	use Carbon\Carbon;
	use Scraper\Ansi\Ansi;

	class Log{

		private static $file_handle = null;

		public static function log( $message ){
			echo "[ ".Carbon::now()." ] ".Ansi::tagsToColors( $message ).PHP_EOL;
			if( !is_null( self::$file_handle ) ){
				fwrite( self::$file_handle, "[ ".Carbon::now()." ] ".Ansi::remove_tags( $message ).PHP_EOL );
			}
		}

		public static function open_file( $filename ){
			if( !file_exists( "log" ) || !is_dir( "log" ) ){
				mkdir( "log" );
			}
			self::$file_handle = fopen( "log/".$filename, "w+" );
		}

		public static function close_file(){
			if( !is_null( self::$file_handle ) ){
				fclose( self::$file_handle );
			}
		}

		public static function dump( $obj, $depth = 0, $exp = "" ){
			if( $depth === true ){
				$exp = $depth;
				$depth = 0;
			}
			if( is_bool( $exp ) && $exp === true )$exp = "";
			/*if( !$exp )
				usleep( 10000 );*/
			foreach( $obj as $key => $value ){
				$string = str_repeat( " ", $depth*3 );
				if( is_object( $obj ) ){
					$string .= "[".$key.":".get_class( $obj )."] => ";
				}else{
					$string .= "[".$key."] => ";
				}
				if( is_object( $value ) ){
					$string .= get_class( $value );
					if( !is_string( $exp ) )
						$string = "<darkYellow>".$string;
					if( !is_string( $exp ) ){
						Log::log( $string );	
					}else{
						$exp.= PHP_EOL.$string;
					}
					
					$exp = self::dump( $value, $depth+1, $exp );
				}else if( is_array( $value ) ){
					$string .= "Array";
					if( @!$value ){
						$string .= "()";
						if( !$exp )
							$string = "<red>".$string;
					}else{
						if( !$exp )
							$string = "<blue>".$string;
					}
					if( !is_string( $exp ) ){
						Log::log( $string );	
					}else{
						$exp.= PHP_EOL.$string;
					}
					$exp = self::dump( $value, $depth+1, $exp );
				}else{
					if( is_bool( $value ) ){
						if( $value ){
							if( !is_string( $exp ) ){
								$string .= "<darkGreen>bool(true)";
							}else{
								$string .= "bool(true)";
							}
						}else{
							if( !is_string( $exp ) ){
								$string .= "<darkGreen>bool(false)";
							}else{
								$string .= "bool(false)";
							}
						}
					}else if( is_string( $value ) ){
						$string .= "string(".$value.")";
					}
					if( !is_string( $exp ) ){
						Log::log( $string );	
					}else{
						$exp.= PHP_EOL.$string;
					}
				}
			}
			return $exp;
		}

	}


?>