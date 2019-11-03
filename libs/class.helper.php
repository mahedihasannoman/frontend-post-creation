<?php
if (!defined('ABSPATH')) exit;
/**
* Since 1.0.0
* Helper collections
*/
class Fpchelper
{
	
	/*
	* Since 1.0.0
	* @Author: Mahedi Hasan	
	* callback function for register_activation_hook
	* Setup the necessery data, tables during plugin activation
	*/
	public function plugin_activation(){
		//currently empty
	}
	/*
	* Since 1.0.0
	* @Author: Mahedi Hasan
	* callback function for register_deactivation_hook
	* Do something during plugin deactivation
	*/
	public function plugin_deactivation(){
		//currently empty
	}
	
	/*
	* Since 1.0.0
	* @Author: Mahedi Hasan
	* @Method: Static method for Tags
	* @Description: Search tags by keyword
	* @Return: Array
	*/
	static function get_tags($keyword){
		global $wpdb;
		$termtable = $wpdb->prefix . "terms";
		$termtaxtable = $wpdb->prefix . "term_taxonomy";
		$results = $wpdb->get_results( $wpdb->prepare( "select t.term_id, t.name, t.slug from $termtable as t, $termtaxtable as tt where tt.term_id = t.term_id and tt.taxonomy = '%s' and t.`name` LIKE '%$keyword%'",
		'post_tag'
		) );
		if(!empty($results)){
			$tags = array();
			foreach($results as $result){
				$tags[] = $result->name;
			}
			return $tags;
		}else{
			return array();
		}
	}
	
	
}