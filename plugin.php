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



    // ------------------------------------------------------------
    // CSV IMPORT/EXPORT 
    // ------------------------------------------------------------


    // ------------------------------------------------------------
    // FRONT-FACING PAGES
    // ------------------------------------------------------------


    // ------------------------------------------------------------
    // ADMIN PAGES
    // ------------------------------------------------------------

    // load admin page classes and tools 
    if ( is_admin() ) {
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-list.php';  
        require_once ITW_MEDICAL_PRODUCTS_PATH . 'admin/class-admin-itw-product-single.php'; 
    }             
        
        
} // end : itw_load_plugin_resources()




  

// ----------------------------------------------------
// DEBUGGING
// ----------------------------------------------------

global $debug;
add_action( 'wp_footer', 'show_debug' );
function show_debug() {
    
    //if ( get_current_user_id() == 10 ) { // 10 = developer user id
        
        global $debug;
        
        //$debug['testing'] = itw_prod()->test();

        /*
        $product = new \ITW_Medical\Products\ITW_Product();
        $product->post_id = 589;
        $product->title = 'great';
        $product->long_description = 'something goes here';
        $product->short_description = 'something goes there';
        itw_prod()->save_product( $product );
        */

        //delete_option( 'debug_option' );
        $debug['option'] = get_option( 'debug_option' ); 

        //$debug['dump1'] = $product->dump( $product::CSV, false );

        if ( $debug ) {
            ob_start(); 
                print_r($debug); 
            $results = ob_get_clean(); 
            $results = '<div style="padding:25px;background:#fff;"><pre>' . $results . '</pre></div>';
            echo $results;
        }
        
    //}
    
}

