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


            // -----------------------------------------------------------
            // GET, SEARCH, AND SAVE PRODUCTS 
            // -----------------------------------------------------------
            
            // @returns (ITW_Product) or (boolean) false
            public function get_product( $post_id ) {
                return $this->dal->get_product( $post_id );
            }

            // Searches for the first ITW_Product with a matching product_number and mfg_number
            // @returns (int) post_id or (boolean) false
            public function search( $product_number, $mfg_number ) {
                return $this->dal->search( $product_number, $mfg_number );
            }

            // @returns (boolean) true or false
            public function save_product( ITW_Product $product ) {
                return $this->dal->save_product( $product );
            }


            // -----------------------------------------------------------
            // ADD / IMPORT PRODUCTS 
            // -----------------------------------------------------------
            
            /* 
             * Imports an array of products into the wordpress database as ITW_Products
             * 
             * @param (array) $products : array of products. 
             *                            each of the individual product's keys should match 
             *                            the fields in ITW_Product::TABLE_HEADER (except "post_id")
             * 
             * @return (array) results of import of each product 
            **/
            public function import( $products, $update = true, $import_external_image = true ) {

                $messages = array();
                
                foreach( $products as $product ) {

                    // check if product already exists 
                    if ( 
                        isset( $product['product_number'] ) &&
                        isset( $product['mfg_number'] )
                    ) {

                        $post_id = $this->search( $product['product_number'], $product['mfg_number'] );

                        // if product exists 
                        if ( $post_id !== false && $update === true ) {

                            // update the existing product 
                            $success = $this->update_product( $post_id, $product, $import_external_image );
                            $product_title = ( isset( $product['title'] ) ) ? $product['title'] : '';
                            if ( $success === true ) {
                                $messages[] = 'Product: ' . $product_title . ' successfully added.';
                            } else {
                                $title = ( $product_title !== '' ) ? ': ' . $product_title : '';
                                $messages[] = 'Product' . $title . ' could not be added.';
                            }

                        // else (product does not exist)
                        } else {

                            // create the product 
                            $success = $this->create_product( $product, $import_external_image );
                            $product_title = ( isset( $product['title'] ) ) ? $product['title'] : '';
                            if ( $success === true ) {
                                $messages[] = 'Product: ' . $product_title . ' successfully added.';
                            } else {
                                $title = ( $product_title !== '' ) ? ': ' . $product_title : '';
                                $messages[] = 'Product' . $title . ' could not be added.';
                            }

                        }

                    } else {

                        $messages[] = 'Invalid product_number and/or mfg_number.';

                    }

                } // end : foreach $products

                return $messages;

            } // end : import()

            // Called to add product programmatically (e.g. on csv import)
            public function create_product( $product_data, $import_external_image = true ) {

                $success = false;

                $product_title = ( isset( $product_data['title'] ) ) ? $product_data['title'] : '';

                if ( $this->is_valid_product_data( $product_data ) ) {

                    $success = $this->dal->create_product( $product_data, $import_external_image );

                } 

                return $success;
                
            }

            // Called to update product programmatically (e.g. on csv import)
            public function update_product( $post_id, $product_data, $import_external_image = true ) {

                $success = false;

                $product_title = ( isset( $product_data['title'] ) ) ? $product_data['title'] : '';

                if ( $this->is_valid_product_data( $product_data ) ) {

                    $success = $this->dal->update_product( $post_id, $product_data, $import_external_image );

                } 

                return $success;
                
            }

            // checks if product data is valid 
            public function is_valid_product_data( $product_data ) {

                $is_valid = true;

                $product_keys = array_keys( $product_data );

                foreach( ITW_Product::TABLE_HEADER as $header_col ) {

                    if ( 
                        $header_col !== 'post_id' && 
                        ! in_array( $header_col, $product_keys ) 
                    ) {

                        $is_valid = false;

                    }

                }

                return $is_valid;

            }
 
    
    } // end class: ITW_Product_Controller

endif;


