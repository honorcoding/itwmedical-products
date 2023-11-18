<?php

// -----------------------------------------------------------
// Product_Controller : Class
//
// Purpose:
//     Product Business Layer.
//
// Usage:
//     // grabbing the instance prevents the need for a global variable to load on every page
//     $prod = \ITW_Medical\Products\Product_Controller::instance();         
//     $prod->function();
//     
//     // Or, simply...
//     prod()->function();  
//     // note: function prod() is declared outisde this namespace in plugin.php 
// -----------------------------------------------------------

namespace ITW_Medical\Products;
use ITW_Medical\Products\Product;
use ITW_Medical\Products\Product_DAL;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'Product_Controller' ) ) :
    
    class Product_Controller {

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

                    $this->dal = \ITW_Medical\Products\Product_DAL::instance();

            }

            public function test() {

                    // get information from data access layer
                    return $this->dal->test();

            }

            public function get_product( $post_id ) {
                    return $this->dal->get_product( $post_id );
            }

            public function save_product( Product $product ) {
                    return $this->dal->save_product( $product );
            }



            
            // ----------------------------------------------------
            // HELPER TOOLS 
            // ----------------------------------------------------


    
    } // end class: Product_Controller

endif;


