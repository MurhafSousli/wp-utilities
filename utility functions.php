<?php	

function Murhaf_setup()
{
    add_theme_support('menus');
}

add_action('after_setup_theme', 'Murhaf_setup');


/** this fucntion extends WP Query arguments which is disabled by default */
function extendQueryVars( $vars ){  
   $vars[] = 'post__not_in';     
   $vars[] = 'post__in';       
   $vars[] = 'post_parent';     
   return $vars;
}  
add_filter( 'query_vars', 'extendQueryVars' );

/** Register and return the menu as an object, it can be passed to js using localizescript function */
function register_mainmenu()
{
    $menu_items = wp_get_nav_menu_items('primary');
    $menu = array();
    foreach ($menu_items as $menu_item) {
        $menutype = $menu_item->type_label;

        if ($menutype === "Post" || $menutype === "Page") {
            $item_slug = get_post($menu_item->object_id)->post_name;
        } else if ($menutype == "Category") {
            $item_slug = get_category($menu_item->object_id)->slug;
        }

        /** create instance of menu-item class and push it to $menu array  */
        $item = array(
            'title' => $menu_item->title,
            'slug' => $item_slug,
            'type' => $menutype,
            'url' => $menu_item->url
        );
        array_push($menu, $item);
    }
    return $menu;
}

/** This function return all categories as an object */
 function getCategories(){
     $categories = array();
     $cats = get_categories();
     foreach ($cats as $cat){
         array_push($categories, $cat);
     }
     return $categories;
 }

function register_Config()
{
    /*
    * add our configuration to the main script.
    */
    $config = array(
        'template_directory' => get_template_directory(),
        'site_url' => get_option('siteurl'),
        'site_title' => get_option('blogname'),
        'site_description' => get_option('blogdescription'),
        'home_id' => get_option('page_on_front'),
        'blog_id' => get_option('page_for_posts'),
        'admin_email' => get_option('admin_email'),
        'menu' => register_mainmenu(),
        'categories' => getCategories()
    );
    wp_localize_script('main', 'app_config', $config);
}

/** Adding Scripts */
function wpb_adding_scripts()
{

    wp_deregister_script('jquery');
    wp_deregister_script('wp-api');

    wp_register_script('polyfills', get_template_directory_uri() . '/angular/dist/polyfills.bundle.js', array(), false, true);
    wp_enqueue_script('polyfills');
    wp_register_script('vendor', get_template_directory_uri() . '/angular/dist/vendor.bundle.js', array(), false, true);
    wp_enqueue_script('vendor');
    wp_register_script('main', get_template_directory_uri() . '/angular/dist/main.bundle.js', array('vendor'), false, true);
    //register_Config();
    wp_enqueue_script('main');
}
add_action('wp_enqueue_scripts', 'wpb_adding_scripts');

/** Adding Styles */
function wpb_adding_styles()
{
    wp_enqueue_style('style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'wpb_adding_styles');



/*
echo wp_script_is('wp-api', 'registered');

$handle = 'wp-api.js';
$list = 'enqueued';
 if (wp_script_is( $handle, $list )) {
   return;

if () ) {
Use minified scripts if SCRIPT_DEBUG is not on.
$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

wp_register_script( 'wp-api', plugins_url( 'wp-api' . $suffix . '.js', __FILE__ ), array(), '1.1', true );

$settings = array(
    'root'          => esc_url_raw( get_rest_url() ),
    'nonce'         => wp_create_nonce( 'wp_rest' ),
    'versionString' => 'wp/v2/',
);
wp_localize_script( 'main', 'wpApiSettings', $settings );
}
*/
?>