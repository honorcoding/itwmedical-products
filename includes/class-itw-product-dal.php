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
            
            // note: title, product details (content) and image (featured image) are post elements
            // meta keys
            const META_KEY_LONG_DESCRIPTION =       'itw_medical_product_long_description';
            const META_KEY_PRODUCT_NUMBER =         'itw_medical_product_product_number';
            const META_KEY_MFG_NUMBER =             'itw_medical_product_manufacturer_number';
            const META_KEY_SHORT_DESCRIPTION =      'itw_medical_product_short_description';
            const META_KEY_PRODUCT_DETAILS =        'itw_medical_product_product_details';
            const META_KEY_PRODUCT_DRAWINGS =       'itw_medical_product_product_drawings';
            const META_KEY_WARRANTY =               'itw_medical_product_warranty';
            const META_KEY_TECHNICAL_LITERATURE =   'itw_medical_product_technical_literature';
            const META_KEY_RELATED_PRODUCTS =       'itw_medical_product_related_products';


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
            
            // note: to speed up this function, use a single MySQL query, instead of a dozen different ones
            // ** returns ITW_Product or false 
            public function get_product( $post_id ) {

                    $product = false; // if $post_id does not point to a valid Product, or other errors
                                    // return false 


                    if ( 
                        get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                        get_post_type( $post_id ) == ITW_Product::get_post_type()    // and $post_id is a Product
                    ) {

                        // create the Product object
                        $product = new ITW_Product();
                        $product->post_id               = $post_id;
                        $product->title                 = get_the_title( $post_id );
                        $product->long_description      = get_post_meta( $post_id, self::META_KEY_LONG_DESCRIPTION, true );;
                        $product->image                 = get_the_post_thumbnail( $post_id );
                        $product->product_number        = get_post_meta( $post_id, self::META_KEY_PRODUCT_NUMBER, true );;
                        $product->mfg_number            = get_post_meta( $post_id, self::META_KEY_MFG_NUMBER, true );;
                        $product->short_description     = get_post_meta( $post_id, self::META_KEY_SHORT_DESCRIPTION, true );;
                        //(deprecated) $product->product_details       = get_the_content( $post_id );
                        $product->product_details       = get_post_meta( $post_id, self::META_KEY_PRODUCT_DETAILS, true );;
                        $product->product_drawings      = get_post_meta( $post_id, self::META_KEY_PRODUCT_DRAWINGS, true );;
                        $product->warranty              = get_post_meta( $post_id, self::META_KEY_WARRANTY, true );;
                        $product->technical_literature  = get_post_meta( $post_id, self::META_KEY_TECHNICAL_LITERATURE, true );;
                        $product->related_products      = get_post_meta( $post_id, self::META_KEY_RELATED_PRODUCTS, true );;
            
                    }


                    // return Product $product;               
                    return $product;

            }

            // note: to speed up this function, use a single MySQL query, instead of a dozen different ones
            public function save_product( ITW_Product $product ) {

                    $success = false;


                    $post_id = $product->post_id;

                    if ( 
                        get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                        get_post_type( $post_id ) == ITW_Product::get_post_type()       // and $post_id is a Product
                    ) {

                        // note: title, image and description are saved by normal wordpress post update feature 

                        // save post meta 
                        update_post_meta( $post_id, self::META_KEY_LONG_DESCRIPTION, $product->long_description );
                        update_post_meta( $post_id, self::META_KEY_PRODUCT_NUMBER, $product->product_number );
                        update_post_meta( $post_id, self::META_KEY_MFG_NUMBER, $product->mfg_number );
                        update_post_meta( $post_id, self::META_KEY_SHORT_DESCRIPTION, $product->short_description );
                        update_post_meta( $post_id, self::META_KEY_PRODUCT_DETAILS, $product->product_details );
                        update_post_meta( $post_id, self::META_KEY_PRODUCT_DRAWINGS, $product->product_drawings );
                        update_post_meta( $post_id, self::META_KEY_WARRANTY, $product->warranty );
                        update_post_meta( $post_id, self::META_KEY_TECHNICAL_LITERATURE, $product->technical_literature );
                        update_post_meta( $post_id, self::META_KEY_RELATED_PRODUCTS, $product->related_products );

                        $success = true;

                    }


                    return $success;

            }
    
    } // end class: ITW_Product_DAL

endif;
