<?php

	namespace Scraper\Models;
	use Scraper\Configuration\Config;
	use Scraper\Models\Category;
	use Scraper\Middleware\Database;
	use Scraper\Middleware\Log;

	class Post{

		public $post_author 	= null;
		public $post_date 		= null;
		public $post_date_gmt 	= null;
		public $post_content 	= null;
		public $post_title 		= null;
		public $post_status 	= null;
		public $comment_status 	= null;
		public $ping_status 	= null;
		public $post_name		= null;
		public $post_modified 	= null;
		public $post_modified_gmt = null;
		public $post_parent 	= null;
		public $menu_order		= null;
		public $post_type		= null;
		public $comment_count	= null;

		public $post_excerpt	= null;
		public $post_password	= null;
		public $to_ping 		= null;
		public $pinged 			= null;
		public $post_content_filtered = null;
		public $guid			= null;
		public $post_mime_type	= null;

		private $post_id 		= null;

		private $category 		= [];

		public function __construct( $date, $content, $title, $name, $category = false ){
			$this->post_date = $date;
			$this->post_date_gmt = $date;
			$this->post_modified = $date;
			$this->post_modified_gmt = $date;
			$this->post_content = $content;
			$this->post_title = $title;
			$this->post_name = $name;
			$this->set_default();
			if( $category !== false ){
				$this->category( $category );
			}
		}

		public function category( $cat = false ){
			if( $cat === false ){
				return $cat;
			}else if( is_object( $cat ) ){
				$cat[] = $cat;
			}else if( is_string( $cat ) ){
				$cat[] = new Category( $cat );
				return $cat;
			}else if( is_array( $cat ) ){
				foreach( $cat as $val ){
					if( is_string( $val ) ){
						$this->category[] = new Category( $val );
					}else{
						$this->category[] = $val;
					}
				}
			}
		}

		public function set_default(){
			$this->post_author = 1;
			$this->post_status = "publish";
			$this->comment_status = "closed";
			$this->ping_status = "open";
			$this->post_parent = 0;
			$this->menu_order  = 0;
			$this->post_type   = "post";
			$this->comment_count = 0;
		}

		public function get_query(){
			$query = "INSERT INTO `".Config::$db_name."`.`".Config::$wp_prefix."posts` (";
			$attributes = [];
			$values 	= [];
			foreach( $this as $key => $value ){
				if( $key != "category" && $key != "post_id" ){
					$attributes[] = "`".$key."`";
					$values[] 	  = "'".Database::escape( $value )."'";
				}
			}
			$query .= implode( ",", $attributes );
			$query .= ") VALUE (";
			$query .= implode( ",", $values );
			$query .= ");";
			return $query;
		}

		public function save(){
			$verbose = Config::$env == "dev" ? true : false;
			$post_save_query = $this->get_query();
			$post_saved = Database::exec( $post_save_query );
			if( $post_saved === false ){
				Log::log( "<red>Cannot save the article:" );
				Log::log( "<red>[".Database::$conn->errno."] ".Database::$conn->error );
				Log::log( "<red>".$post_save_query );
				exit();
			}
			$this->post_id = Database::$conn->insert_id;
			if( $verbose )
				Log::log( "<green>Article with ID [".$this->post_id."] saved." );
			foreach( $this->category as $cat_key => $cat ){
				$cat->check_exists();
				$relation_query = "INSERT INTO `".Config::$db_name."`.`".Config::$wp_prefix."term_relationships` ( `object_id`, `term_taxonomy_id`, `term_order` ) VALUE ( ".$this->post_id.", ".$cat->tax_id.", 0 )";
				$relation_exec = Database::exec( $relation_query );
				if( $relation_exec === false ){
					Log::log( "<red>\t\tCannot save the link between the article and the taxonomy:" );
					Log::log( "<red>\t\t[".Database::$conn->errno()."] ".Database::$conn->error );
					Log::log( "<red>\t\t".$relation_query );
					exit();
				}
				$count_query = "UPDATE `".Config::$db_name."`.`".Config::$wp_prefix."term_taxonomy` SET count = count +1 WHERE `term_taxonomy_id` = ".$cat->tax_id.";";
				$count_exec = Database::exec( $count_query );
				if( $count_exec === false ){
					Log::log( "<red>\t\tCannot add the article to the counter:" );
					Log::log( "<red>\t\t[".Database::$conn->errno()."] ".Database::$conn->error );
					Log::log( "<red>\t\t".$count_query );
					exit();
				}
			}
			Log::log( "<green>Article #".$this->post_id." saved with its category." );
		}
	}

?>