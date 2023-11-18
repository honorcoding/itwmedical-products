<?php

// -----------------------------------------------------------
// ITW_Product_DAL : Class
//
// Purpose:
//     Product Data Access Layer.
//
// Usage:
//     // note: Class is instantiated by the Client Request Controller
//     //       No need to access directly.
//
//     $prod_dal = \ITW_Medical\Products\ITW_Product_DAL::instance();         
//     $prod_dal->function();
// -----------------------------------------------------------

namespace ITW_Medical\Products;
use ITW_Medical\Products\ITW_Product;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'ITW_Product_DAL' ) ) :
    
    class ITW_Product_DAL {

            // this class only needs to be instantiated once 
            private static $_instance = null;
            
            // constants
            const META_KEY_CONVERSATION     = 'itw_request_conversation';
            const META_KEY_REQUEST_STATUS   = 'itw_request_status';
            const META_KEY_APPROVAL_STATUS  = 'itw_request_approval_status';


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

            }

            public function test() {

                    return 'DAL is active';

            }


            // -----------------------------------------------------------
            // PROCESS DATA 
            // -----------------------------------------------------------
            
            public function get_request( $post_id ) {

                    $request = false; // if $post_id does not point to a valid Product, or other errors
                                    // return false 


                    if ( 
                        get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                        get_post_type( $post_id ) == Product::get_post_type()    // and $post_id is a Product
                    ) {

                        // create the Product object
                        $request = new Product();
                        $request->post_id           = $post_id;
                        $request->conversation      = get_post_meta( $post_id, self::META_KEY_CONVERSATION, true );
                        $request->request_status    = get_post_meta( $post_id, self::META_KEY_REQUEST_STATUS, true );
                        $request->approval_status   = get_post_meta( $post_id, self::META_KEY_APPROVAL_STATUS, true );

                    }


                    // return Product $request;               
                    return $request;

            }

            public function save_request( Product $request ) {

                    $success = false;


                    $post_id = $request->post_id;

                    if ( 
                        get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                        get_post_type( $post_id ) == Product::get_post_type()    // and $post_id is a Product
                    ) {

                        update_post_meta( $post_id, self::META_KEY_CONVERSATION, $request->conversation );
                        update_post_meta( $post_id, self::META_KEY_REQUEST_STATUS, $request->request_status );
                        update_post_meta( $post_id, self::META_KEY_APPROVAL_STATUS, $request->approval_status );

                        $success = true;

                    }


                    return $success;

            }
    
    } // end class: ITW_Product_DAL

endif;
