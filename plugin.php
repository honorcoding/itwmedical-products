<?php
/**
 * Plugin Name: ITW Medical Products
 * Description: Facilitates product listing and display. 
 * Version: 1.0
 * Author: IWP
 * Author URI:   https://innerworkspro.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  itwmedical_products    
 * Domain Path:  /
*/


defined( 'ABSPATH' ) || exit;   // no access for random strangers


// PLUGIN URL AND PATH 
define("ITW_MEDICAL_PRODUCTS_URL", plugin_dir_url(__FILE__));
define("ITW_MEDICAL_PRODUCTS_PATH", plugin_dir_path(__FILE__));


// ----------------------------------------------------
// LOAD PLUGIN TOOLS
// ----------------------------------------------------

add_action( 'init', 'itw_load_plugin_resources' ); 
function itw_load_plugin_resources() {
    

    // ------------------------------------------------------------
    // STYLES AND SCRIPTS
    // ------------------------------------------------------------

    add_action('wp_enqueue_scripts', 'itw_product_styles_and_scripts' ); 
    add_action('admin_enqueue_scripts', 'itw_product_admin_styles_and_scripts');


    // ------------------------------------------------------------
    // INFRASTRUCTURE
    // ------------------------------------------------------------

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

    // load classes that support product filtering 
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-filter.php';
    require_once ITW_MEDICAL_PRODUCTS_PATH . 'includes/class-itw-product-filter.php';

    

    // ------------------------------------------------------------
    // CSV IMPORT/EXPORT 
    // ------------------------------------------------------------


    // ------------------------------------------------------------
    // FRONT-FACING PAGES
    // ------------------------------------------------------------

    if ( ! is_admin() ) {
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'client-view/class-itw-product-single-client-view.php';  
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'client-view/class-itw-product-archive-client-view.php';          
    }             


    // ------------------------------------------------------------
    // ADMIN PAGES
    // ------------------------------------------------------------

    // load admin page classes and tools 
    if ( is_admin() ) {
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-list.php';  
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-single.php'; 
    }             
        
        
} // end : itw_load_plugin_resources()



// ------------------------------------------------------------
// STYLES AND SCRIPTS
// ------------------------------------------------------------

// enqueue scripts and styles affiliated with the client portal 
// (use external files, so they can be properly enqueued and not cause conflicts with jquery, etc)
function itw_product_styles_and_scripts() {
    
    // client portal styles
    $css_slug = "itw-product-styles"; 
    $css_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/style.css';
    $css_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/style.css' );
    
    wp_register_style( $css_slug, $css_uri, array(), $css_filetime );
    wp_enqueue_style( $css_slug ); 
    
    // client portal scripts
    $js_slug = "itw-product-scripts"; 
    $js_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/scripts.js';
    $js_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/scripts.js' );

    wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
    wp_enqueue_script( $js_slug );   
        
}

// enqueue scripts and styles affiliated with the wordpress admin area as it pertains to the client portal 
function itw_product_admin_styles_and_scripts() {

    // client portal admin scripts
    $js_slug = "itw-product-admin-scripts"; 
    $js_uri = ITW_MEDICAL_PRODUCTS_URL . 'assets/admin.js';
    $js_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/admin.js' );

    wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
    wp_enqueue_script( $js_slug );   
    
}

  


// ----------------------------------------------------
// DEBUGGING
// ----------------------------------------------------

global $debug;
add_action( 'wp_footer', 'show_debug' );
function show_debug() {
    
    //if ( get_current_user_id() == 10 ) { // 10 = developer user id
        
        global $debug;
        
        // $debug data goes here

        if ( $debug ) {
            ob_start(); 
                print_r($debug); 
            $results = ob_get_clean(); 
            $results = '<div style="padding:25px;background:#fff;"><pre>' . $results . '</pre></div>';
            echo $results;
        }
        
    //}
    
}

