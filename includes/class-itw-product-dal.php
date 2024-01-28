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
use KM_Download_Remote_Image;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'ITW_Product_DAL' ) ) :
    
    class ITW_Product_DAL {

            // this class only needs to be instantiated once 
            private static $_instance = null;
            
            // note: title, product details (content) and image (featured image) are post elements
            // meta keys prefix
            const META_KEY_PREFIX = 'itw_medical_product_';

            // wp option keys
            const WARRANTY_OPTION_KEY = 'itw_warranty';

            // return type for categories
            const STRING = 'STRING';
            const ARRAY  = 'ARRAY';


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

            /* 
             * Searches for the first ITW_Product with a matching product_number and mfg_number
             * 
             * @return (int) post_id OR 
             *         (boolean) false (if not exists)
            **/
            public function search( $product_number, $mfg_number ) { 

                    // prepare the query args 
                    $args = array(
                        'post_type' => ITW_Product::CUSTOM_POST_TYPE,
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key' => self::META_KEY_PRODUCT_NUMBER,
                                'value' => $product_number,
                                'compare' => '='
                            ),
                            array(
                                'key' => self::META_KEY_MFG_NUMBER,
                                'value' => $mfg_number,
                                'compare' => '='
                            ),
                        ),
                        'fields' => 'ids',
                    );

                    // get a list of matching posts 
                    $post_ids = get_posts( $args );

                    // return first post_id with matching product_number 
                    if ( ! empty( $post_ids ) ) {
                        return $post_ids[0];
                    } else {
                        return false;
                    }

            } // end : search()
            

            // note: to speed up this function, use a single MySQL query, instead of a dozen different ones
            // @return ITW_Product or false 
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
                        $product->long_description      = get_post_meta( $post_id, self::META_KEY_PREFIX . 'long_description', true );
                        $product->image                 = get_the_post_thumbnail( $post_id );
                        $product->product_number        = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_number', true );
                        $product->mfg_number            = get_post_meta( $post_id, self::META_KEY_PREFIX . 'mfg_number', true );
                        $product->short_description     = get_post_meta( $post_id, self::META_KEY_PREFIX . 'short_description', true );
                        //(deprecated) $product->product_details       = get_the_content( $post_id );
                        $product->product_details_materials_of_construction = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_materials_of_construction', true );
                        $product->product_details_connections               = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_connections', true );
                        $product->product_details_design                    = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_design', true );
                        $product->product_details_perfomance_data           = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_performance_data', true );
                        $product->product_details_packaging                 = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', true );
                        $product->product_drawings      = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', true );
                        $product->technical_literature  = get_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', true );
                        $product->related_products      = get_post_meta( $post_id, self::META_KEY_PREFIX . 'related_products', true );
                        $product->categories            = $this->get_product_categories( $post_id );
            
                    }


                    // return Product $product;               
                    return $product;

            } // end : get_product()


            // @return (array or string) a list of product categories associated with this post_id 
            public function get_product_categories( $post_id, $return_type = self::ARRAY ) {

                $categories = false;

                $terms = get_the_terms( $post_id, ITW_Product::get_taxonomy() );

                if ( ! empty( $terms ) ) {

                    $categories = array();
                    foreach( $terms as $term ) {
                        
                        $categories[] = array(
                            'term_id' => $term->term_id,
                            'slug'    => $term->slug,
                            'name'    => $term->name,
                        );

                    }

                    if ( $return_type === self::STRING ) {
                        $categories = implode( ',', $categories );
                    }

                }

                return $categories;
    
            }

            
            /*
             * Called when product is saved via admin area 
            **/
            public function save_product( ITW_Product $product ) {
                    // TODO: to speed up this function, use a single MySQL query, instead of a dozen different ones
                    // TODO: DOES THIS HAVE A UNIQUE IDENTIFIER? (E.G. PRODUCT_NUMBER OR MFG_NUMBER OR BOTH TOGETHER?)

                    $success = false;

                    $post_id = $product->post_id;

                    if ( 
                        get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                        get_post_type( $post_id ) == ITW_Product::get_post_type()       // and $post_id is a Product
                    ) {

                        // note: title, image and categories are saved by normal wordpress post update feature 

                        // save post meta 
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'long_description', $product->long_description );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_number', $product->product_number );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'mfg_number', $product->mfg_number );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'short_description', $product->short_description );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_materials_of_construction', $product->product_details_materials_of_construction );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_connections', $product->product_details_connections );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_design', $product->product_details_design );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_perfomance_data', $product->product_details_perfomance_data );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', $product->product_details_packaging );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', $product->product_drawings );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', $product->technical_literature );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'related_products', $product->related_products );

                        $success = true;

                    }

                    return $success;

            } // end : save_product()


            /* 
             * Called to create product programmatically (e.g. on csv import)
            **/
            public function create_product( $product, $import_external_image = true ) {

                $success = false;

                $update_args = array(
                    'post_type' => ITW_Product::get_post_type(),
                    'post_status' => 'publish',
                );
                $post_id = wp_insert_post( $update_args, false, false );

                if ( $post_id !== 0 ) {

                    $success = $this->update_product( $post_id, $product, $import_external_image );

                }

                return $success;

            }
    

            /*
             * Called to update product programmatically (e.g. on csv import)
             * 
             * @param (int) $post_id
             * @param (array) $product          - an array of product information 
             * @param (bool) $also_import_image - a flag to determine if images should be imported from external sources 
             * 
             * @return (boolean) true or false
            **/
            public function update_product( $post_id, $product, $import_external_image = true ) {

                // TODO: DOES THIS HAVE A UNIQUE IDENTIFIER? (E.G. PRODUCT_NUMBER OR MFG_NUMBER OR BOTH TOGETHER?)

                $success = false;

                if ( 
                    get_post_status( $post_id ) &&                                  // if $post_id is a valid post
                    get_post_type( $post_id ) == ITW_Product::get_post_type()       // and $post_id is a Product
                ) {

                    // save post title
                    $update_args = array(
                        'ID' => $post_id,
                        'post_title' => $product['title'],
                    );
                    $post_id = wp_update_post( $update_args, false, false );

                    if ( $post_id !== 0 ) {

                        // get the attachment image (and assign to this post as the featured image)
                        if ( $import_external_image === true ) {

                            $attachment_id = 0;

                            // if the image belongs to this site
                            if ( $this->is_url_from_this_site( $product['image'] ) ) {
                                // attempt to find that attachment_id
                                $attachment_id = attachment_url_to_postid( $product['image'] );     // returns 0 on failure
                            }

                            // if an attachment_id was not found, or this image is from another site
                            if ( $attachment_id === 0 ) {

                                // download image from external URL and save to post_id
                                $download_remote_image = new KM_Download_Remote_Image( $product['image'] );
                                $attachment_id         = $download_remote_image->download();
                                
                            }

                            if ( $attachment_id && $attachment_id !== 0 ) {
                                set_post_thumbnail( $post_id, $attachment_id );
                            }

                        }

                        // save post meta 
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_number', $product['product_number'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'mfg_number', $product['mfg_number'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'long_description', $product['long_description'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'short_description', $product['short_description'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_materials_of_construction', $product['product_details_materials_of_construction'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_connections', $product['product_details_connections'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_design', $product['product_details_design'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_perfomance_data', $product['product_details_performance_data'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', $product['product_details_packaging'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', $product['product_drawings'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', $product['technical_literature'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'related_products', $product['related_products'] );

                        // save post categories 
                        if ( $product['categories'] !== '' ) {

                            // get cat_ids from category names 
                            $cat_names = explode( ',', $product['categories'] );
                            $cat_ids = array();
                            foreach( $cat_names as $cat_name ) {
                                $term = get_term_by( 'name', trim($cat_name), ITW_Product::get_taxonomy() );
                                if ( $term !== false ) {
                                    $cat_ids[] = $term->term_id;
                                }                                
                            }

                            // assign the terms to this post
                            if ( ! empty( $cat_ids ) ) {
                                wp_set_post_terms( $post_id, $cat_ids, ITW_Product::get_taxonomy() );
                            }                            

                        }

                        $success = true;

                    } 

                }

                return $success;

            }  // end : update_product() 

            // check if url if from this site
            public function is_url_from_this_site( $image_url ) {

                $site_url_parsed = wp_parse_url( get_site_url() );
                $site_host = $site_url_parsed['host'];
                
                $image_url_parsed = wp_parse_url( $image_url );
                $image_host = $image_url_parsed['host'];

                $is_site_image = ( $site_host === $image_host ) ? true : false;
                return $is_site_image;

            }
            

            // -----------------------------------------------------------
            // GLOBAL DATA - WORDPRESS OPTIONS
            // -----------------------------------------------------------

            // get the warranty text
            public function get_warranty() {
                return stripslashes( get_option( self::WARRANTY_OPTION_KEY ) );
            }

            // set the warranty text 
            public function set_warranty( $text ) {
                update_option( self::WARRANTY_OPTION_KEY, $text );
            }

            
    } // end class: ITW_Product_DAL

endif;
