<?php

	namespace Scraper\Models;

	use Scraper\Middleware\Database;
	use Scraper\Middleware\Log;
	use Scraper\Configuration\Config;

	use Scraper\App;

	class Category{

		public $term_id  = null;
		public $tax_id   = null;

		public $name = null;

		public function __construct( $name ){
			$this->name = $name;
		}

		public function check_exists(){
			$verbose = Config::$env == "dev" ? true : false;
			$query = "SELECT `term_id` FROM `".Config::$db_name."`.`".Config::$wp_prefix."terms` WHERE `name` = '".Database::escape( $this->name )."';";
			$exec = Database::exec( $query );
			if( $exec === false || $exec->num_rows == 0 ){
				if( $verbose )
					Log::log( "<darkRed>The category `".$this->name."` does NOT exist." );
				$ids = $this->save_id();
				$this->term_id = $ids[ 0 ];
			}else{
				$id = $exec->fetch_assoc();
				$this->term_id = $id[ 'term_id' ];
				if( $verbose )
					Log::log( "<darkGreen>The category `".$this->name."` exists. Its ID is '".$this->term_id."'." );
			}
			$query = "SELECT `term_taxonomy_id` FROM `".Config::$db_name."`.`".Config::$wp_prefix."term_taxonomy` WHERE `term_id` = ".$this->term_id.";";
			$exec = database::exec( $query );
			if( $exec === false || $exec->num_rows == 0 ){
				Log::log( "<red>Cannot get the taxonomy id:" );
				Log::log( "<red>[".Database::$conn->errno."] ".Database::$conn->error );
				Log::log( "<red>".$query );
				exit();
			}else{
				$id = $exec->fetch_assoc();
				$this->tax_id = $id[ 'term_taxonomy_id' ];
			}
			if( $verbose )
				Log::log( "<green>Term ID: '".$this->term_id."' - Taxonomy ID: '".$this->tax_id."'" );
			return true;
		}

		public function save_id(){
			$verbose = Config::$env == "dev" ? true : false;
			$id = null;
			if( $verbose )
				Log::log( "<blue>Creating the category `".$this->name."`..");
			$slug = Database::escape( strtolower( App::clean( $this->name ) ) );
			$query = "INSERT INTO `".Config::$db_name."`.`".Config::$wp_prefix."terms`( `name`, `slug`, `term_group` ) VALUE( '".Database::escape( $this->name )."', '".$slug."', 0 )";
			$exec = Database::exec( $query );
			if( $exec === false ){
				Log::log( "<red>Cannot save the category:" );
				Log::log( "<red>[".Database::$conn->errno."] ".Database::$conn->error );
				Log::log( "<red>".$query );
				exit();
			}else{
				$term_id = Database::$conn->insert_id;
				if( $verbose ){
					Log::log( "<green>Created the category `".$this->name."` with the slug `".$slug."` and ID '".$term_id."'." );
				}
			}
			$query = "INSERT INTO `".Config::$db_name."`.`".Config::$wp_prefix."term_taxonomy`( `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUE( '".$term_id."', 'category', '', 0, 0 );";
			$exec = Database::exec( $query );
			if( $exec === false ){
				Log::log( "<red>Cannot save the taxonomy:" );
				Log::log( "<red>[".Database::$conn->errno."] ".Database::$conn->error );
				Log::log( "<red>".$query );
				exit();
			}else{
				if( $verbose )
					Log::log( "<green>Taxonomy created." );
				$tax_id = Database::$conn->insert_id;	
			}
			
			return [ $term_id, $tax_id ];
		}

	}

?>