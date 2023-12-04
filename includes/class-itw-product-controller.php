<?php

// -----------------------------------------------------------
// ITW_Product_Controller : Class
//
// Purpose:
//     ITW_Product Business Layer.
//
// Usage:
//     // grabbing the instance prevents the need for a global variable to load on every page
//     $prod = \ITW_Medical\Products\ITW_Product_Controller::instance();         
//     $prod->function();
//     
//     // Or, simply...
//     itw_prod()->function();  
//     // note: function itw_prod() is declared outisde this namespace in plugin.php 
// -----------------------------------------------------------

namespace ITW_Medical\Products;
use ITW_Medical\Products\ITW_Product;
use ITW_Medical\Products\ITW_Product_DAL;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'ITW_Product_Controller' ) ) :
    
    class ITW_Product_Controller {

            // this class only needs to be instantiated once 
            private static $_instance = null;

            // data access layer
            private $dal;
            

            // -----------------------------------------------------------
            // INSTANTIATION 
            // -----------------------------------------------------------
            
            // Return an instance of this class 
            // grabbing the instance prevents the need for a global variable to load on every page
            public static function instance() {
                
                    // If the single instance hasn't been set, set it now.
                    if ( is_null( self::$_instance ) ) {
                        self::$_instance = new self();
                    }

                    return self::$_instance;

            }

            public function __construct() {

                    $this->dal = \ITW_Medical\Products\ITW_Product_DAL::instance();

            }

            public function test() {
                    // get information from data access layer
                    return $this->dal->test();
            }

            // * returns @ITW_Product or @boolean (false)
            public function get_product( $post_id ) {
                    return $this->dal->get_product( $post_id );
            }

            public function save_product( ITW_Product $product ) {
                    return $this->dal->save_product( $product );
            }

    
    } // end class: ITW_Product_Controller

endif;


