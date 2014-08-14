<?php
/*
Plugin Name: Tony's Portfolio Plugin
Plugin URI: http://github.com/tonyedwardspz/WP-Portfolio-Plugin
Description: A simple plugin that creates and display a projects portfolio with WordPress using custom post types!
Author: Tony Edwards
Version: 1.0
Author URI: http://www.purelywebdesign.co.uk
*/
/* forked from https://github.com/jcasabona/wp-portfolio */


/*Some Set-up*/
define('TEP_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' ); 
define('TEP_NAME', "Tony's Portfolio Plugin");
 
/*Files to Include*/
require_once('te-project-cpt.php');

if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails' );
    add_image_size('te_project', 1100, 640, true); 
}


function te_projects_scripts() {
	wp_enqueue_script( 'picturefill', TEP_PATH.'js/picturefill.js', array());
}

add_action( 'wp_enqueue_scripts', 'te_projects_scripts' );

/**Actions, Filters, and Hooks**/


add_filter( 'post_thumbnail_html', 'te_projects_get_featured_image');

//Replace image with picturefill markup
function te_projects_get_featured_image($html, $aid=false){
    $sizes= array('thumbnail', 'medium', 'large', 'te_project', 'full'); 
    
	$img= '<span data-picture data-alt="'.get_the_title().'">';
	$ct= 0;
	$aid= (!$aid) ? get_post_thumbnail_id() : $aid;

	foreach($sizes as $size){
		$url= wp_get_attachment_image_src($aid, $size);
		$width= ($ct < sizeof($sizes)-1) ? ($url[1]*0.66) : ($width/0.66)+25;
		$img.= '
			<span data-src="'. $url[0] .'"';
		$img.= ($ct > 0) ? ' data-media="(min-width: '. $width .'px)"></span>' :'></span>';
		$ct++;
	}

	$url= wp_get_attachment_image_src( $aid, $sizes[1]);
    $img.=  '<noscript>
            	<img src="'.$url[0] .'" alt="'.get_the_title().'">
			</noscript>
		</span>';
	return $img;
}

/**Display Functions and Shortcodes**/

// get the custom project link
function te_projects_get_link($id){
	$url= get_post_custom_values('te_projects_link', $pid); 
	return ($url[0] != '') ? $url[0] : false;
}

// get the skills taxonomies
// used to display the skills and tools in the single portfolio view
function custom_taxonomies_skills() {
    global $post, $post_id;
    // get post by post id
    $post = &get_post($post->ID);
    // get post type by post
    $post_type = $post->post_type;
    // get post type taxonomies
    $taxonomies = get_object_taxonomies($post_type);
    $out = "<ul>";
    foreach ($taxonomies as $taxonomy) { 
    	if ($taxonomy == 'te-skill-type' ){
	        // get the terms related to post
	        $terms = get_the_terms( $post->ID, $taxonomy );
	        if ( !empty( $terms ) ) {
	            foreach ( $terms as $term )
	            	//build the list
	                $out .= "<li>".$term->name."</li>";
	        }
	    }
	}
    $out .= "</ul>";
    return $out;
} 


// get taxonomies for project type
// used to filter posts of the portfolio category
function custom_taxonomies_terms_links() {
    global $post, $post_id;
    // get post by post id
    $post = &get_post($post->ID);
    // get post type by post
    $post_type = $post->post_type;
    // get post type taxonomies
    $taxonomies = get_object_taxonomies($post_type);
    
    foreach ($taxonomies as $taxonomy) {
        // get the terms related to post
        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( !empty( $terms ) ) {
            foreach ( $terms as $term )
                $out .= $term->name.' ';
        }
    }
    return $out;
}

?>
