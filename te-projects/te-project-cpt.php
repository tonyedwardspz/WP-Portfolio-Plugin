<?php

add_action('init', 'te_projects_register');  
  
function te_projects_register() {  
    $args = array(  
        'label' => __('Portfolio'),  
        'singular_label' => __('Project'),  
        'public' => true,  
        'show_ui' => true,  
        'capability_type' => 'post',  
        'hierarchical' => true,    
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail') ,
        'rewrite' => array('slug' => 'portfolio', 'with_front' => false)
       );  
  
    register_post_type( 'portfolio' , $args );  
    register_taxonomy("te-project-type", array("portfolio"), array("hierarchical" => true, "label" => "Project Type", "singular_label" => "Project Type", "rewrite" => true));

    // skills
    register_taxonomy("te-skill-type", array("portfolio"), array("hierarchical" => true, "label" => "Skills & Tools", "singular_label" => "Skill of Tool", "rewrite" => true));
}  	

add_action("admin_init", "te_projects_admin_init");  


function te_projects_admin_init(){  
    add_meta_box("te-projects-meta", __("Project Link"), "te_projects_options", "portfolio", "side", "low");  
}  
  

function te_projects_options(){  
        global $post;  
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
        $custom = get_post_custom($post->ID);  
        $link = $custom["te_projects_link"][0];  
?>  
    <input name="te_projects_link" placeholder="http://" value="<?php echo $link; ?>" />  
<?php  
}  


 add_action('save_post', 'te_projects_save');  
  
function te_projects_save(){  
    global $post;  
    
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){ 
		return $post_id;
	}else{
    	update_post_meta($post->ID, "te_projects_link", $_POST["te_projects_link"]); 
    } 
}  
  

add_action('init', 'te_projects_rewrite');

function te_projects_rewrite() {
    global $wp_rewrite;
    $wp_rewrite->add_permastruct('typename', 'typename/%year%/%postname%/', true, 1);
    add_rewrite_rule('typename/([0-9]{4})/(.+)/?$', 'index.php?typename=$matches[2]', 'top');
    $wp_rewrite->flush_rules();
}
?>
