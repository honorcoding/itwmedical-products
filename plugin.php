<?php
/**
 * Plugin Name: ITW Medical Products
 * Description: Facilitates product listing and display. 
 * Version: 1.0
 * Author: IWP
 * Author URI:   https://itwmedical.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  itwmedical_products    
 * Domain Path:  /
*/


defined( 'ABSPATH' ) || exit;   // no access for random strangers


// PLUGIN URL AND PATH 
define("ITW_MEDICAL_PRODUCTS_URL", plugin_dir_url(__FILE__));
define("ITW_MEDICAL_PRODUCTS_PATH", plugin_dir_path(__FILE__));

// PLUGIN SETTNIGS 
define("ITW_PRODUCT_LIST_PAGE_ID_OPTION_KEY", 'itw_product_list_page_id' );


// ----------------------------------------------------
// LOAD PLUGIN TOOLS
// ----------------------------------------------------

add_action( 'init', 'itw_load_plugin_resources' ); 
function itw_load_plugin_resources() {
    

    // ------------------------------------------------------------
    // INFRASTRUCTURE
    // ------------------------------------------------------------

    // load class to download remotes images (required by dal for bulk importing)
    // also, expand basic wordpress tools 
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-download-remote-image.php';        
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-wp-expanded.php'; // note: requires class-download-remote-image.php

    // load custom post types (must load CPTs before tools)
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/itw-custom-post-types.php';

    // load classes that support product functionality 
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-product.php';
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-product-dal.php';
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-product-controller.php';

    // allows quick access to Product_Controller class, 
    // but only loads on pages where it is used and only loads once 
    // example: itw_prod()->[method or property] 
    //          for multiple calls, use $itw_prod = itw_prod(); 
    function itw_prod() {
        return \ITW_Medical\Products\ITW_Product_Controller::instance();
    }     

    // --------------------------------------------
    // tells dependent tools whether or not to load 
    // --------------------------------------------
    //
    // EXAMPLE: 
    //    // make sure itw-medical-product tools are activated
    //    if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    //        return false;
    //    }
    //    // can safely use tools now (e.g. itw_prod()->...)
    //
    define('ITW_MEDICAL_PRODUCTS', 'TRUE');
    

    // ------------------------------------------------------------
    // CLIENT-FACING PAGES
    // ------------------------------------------------------------

    if ( ! is_admin() ) {

        // load client-facing styles and scripts
        add_action('wp_enqueue_scripts', 'itw_product_styles_and_scripts' );         
    
        // load classes that support product filtering 
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-filter.php';
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-product-filter.php';

        // handle client-facing pages
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'client-view/class-itw-product-single-client-view.php';  
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'client-view/class-itw-product-archive-client-view.php';          

    }             

    // ------------------------------------------------------------
    // ADMIN-FACING PAGES
    // ------------------------------------------------------------

    if ( is_admin() ) {

        // load admin-facing styles and scripts
        add_action('admin_enqueue_scripts', 'itw_product_admin_styles_and_scripts');

        // load classes that support file uploads, csv processing, and accessing wordpress media files
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-file-upload.php';
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-csv-file.php';
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-wp-media.php';

        // handle admin-facing pages
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-settings.php'; 
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-list.php';  
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-single.php'; 

    }             
        
        
} // end : itw_load_plugin_resources()



// ------------------------------------------------------------
// FOR LOADING STYLES AND SCRIPTS
// ------------------------------------------------------------

// enqueue scripts and styles affiliated with the client portal 
// (use external files, so they can be properly enqueued and not cause conflicts with jquery, etc)
function itw_product_styles_and_scripts() {
    
    // client portal styles
    $css_slug = "itw-product-styles"; 
    $css_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/css/style.css';
    $css_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/css/style.css' );
    
    wp_register_style( $css_slug, $css_uri, array(), $css_filetime );
    wp_enqueue_style( $css_slug ); 
    
    // client portal scripts
    $js_slug = "itw-product-scripts"; 
    $js_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/js/scripts.js';
    $js_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/js/scripts.js' );

    wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
    wp_enqueue_script( $js_slug );   
        
}

// enqueue scripts and styles affiliated with the wordpress admin area as it pertains to the client portal 
function itw_product_admin_styles_and_scripts() {

    global $post_type;

    // client portal styles
    $css_slug = "itw-product-admin-styles"; 
    $css_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/css/admin.css';
    $css_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/css/admin.css' );
    
    wp_register_style( $css_slug, $css_uri, array(), $css_filetime );
    wp_enqueue_style( $css_slug ); 
    
    
    // client portal admin scripts
    $js_slug = "itw-product-admin-scripts"; 
    $js_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/js/admin.js';
    $js_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/js/admin.js' );

    wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
    wp_enqueue_script( $js_slug );   
 
    // enqueue media scripts on admin product pages
    if( $post_type == \ITW_Medical\Products\ITW_Product::get_post_type() ){
        \ITW_Medical\Media\ITW_WP_Media::enqueue_media_styles_and_scripts();
    }

}

  


// ----------------------------------------------------
// DEBUGGING
// ----------------------------------------------------

global $debug;
if ( is_admin() ) {
    add_action( 'admin_footer', 'show_debug' );
} else { 
    add_action( 'wp_footer', 'show_debug' );
}


function show_debug() {
    
    //if ( get_current_user_id() == 10 ) { // 10 = developer user id
        
        global $debug;
        // example: $debug['data'] = $data;

        //$debug['export_all'] = itw_prod()->export_all();

        if ( $debug ) {
            ob_start(); 
                print_r($debug); 
            $results = ob_get_clean(); 
            $results = '<div style="padding:25px;background:#fff;"><pre>' . $results . '</pre></div>';
            if ( is_admin() ) {
                $results = '<div style="padding-left:160px;">' . $results . '</div>';
            }
            echo $results;
        }

    //}
    
}

