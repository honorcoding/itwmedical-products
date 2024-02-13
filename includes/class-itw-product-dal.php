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
use ITW_Medical\Wordpress\WP_Expanded as WPX;


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

            // last error 
            private $last_error;

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
                            'key' => self::META_KEY_PREFIX . 'product_number',
                            'value' => $product_number,
                            'compare' => '='
                        ),
                        array(
                            'key' => self::META_KEY_PREFIX . 'mfg_number',
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
            
            // get a list of ids of all available products in database
            public function get_all_product_ids() {

                // prepare the query args 
                $args = array(
                    'post_type' => ITW_Product::CUSTOM_POST_TYPE,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                );

                // get a list of matching posts 
                $post_ids = get_posts( $args );

                // return first post_id with matching product_number 
                if ( ! empty( $post_ids ) ) {
                    return $post_ids;
                } else {
                    return false;
                }

            }

            public function get_products_with_category( $cat_ids = array(), $except = '' ) {

                $post_ids = array();

                if ( ! empty( $cat_ids ) ) {

                    // prepare the query args 
                    $args = array(
                        'post_type' => ITW_Product::CUSTOM_POST_TYPE,
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'fields' => 'ids',
                        'tax_query' => array( 
                            array(
                                'taxonomy' => ITW_Product::CUSTOM_TAXONOMY,
                                'terms' => $cat_ids,
                                'operator' => 'IN',
                            ) 
                        ),
                    );

                    if ( $except !== '' ) {
                        $args['post__not_in'] = array( intval( $except ) );
                    }

                    // get a list of matching posts 
                    $post_ids = get_posts( $args );

                }

                return $post_ids;

            }

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
                    $product->post_id                = $post_id;
                    $product->product_number         = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_number', true );
                    $product->mfg_number             = get_post_meta( $post_id, self::META_KEY_PREFIX . 'mfg_number', true );
                    $product->title                  = get_the_title( $post_id );
                    $product->short_description      = get_post_meta( $post_id, self::META_KEY_PREFIX . 'short_description', true );
                    $product->long_description       = get_post_meta( $post_id, self::META_KEY_PREFIX . 'long_description', true );
                    $product->image                  = get_post_thumbnail_id( $post_id );
                    $product->image_file             = WPX::get_filename_from_attachment_id( $product->image );
                    $product->product_details_materials_of_construction = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_materials_of_construction', true );
                    $product->product_details_connections               = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_connections', true );
                    $product->product_details_design                    = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_design', true );
                    $product->product_details_performance_data           = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_performance_data', true );
                    $product->product_details_packaging                 = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', true );
                    $product->product_drawings       = get_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', true );
                    $product->product_drawings_files = WPX::get_filenames_from_attachment_ids( $product->product_drawings, 'STRING' );
                    $product->technical_literature   = get_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', true );
                    $product->technical_literature_files = WPX::get_filenames_from_attachment_ids( $product->technical_literature, 'STRING' );
                    $product->categories             = $this->get_product_categories( $post_id );
        
                }


                // return Product $product;               
                return $product;

            } // end : get_product()


            // @return (array or string) a list of product categories associated with this post_id 
            public function get_product_categories( $post_id, $return_type = self::ARRAY ) {

                $categories = array();

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
             * Called when product is saved via admin area - single post 
            **/
            public function save_product( ITW_Product $product ) {
                    // TODO: to speed up this function, use a single MySQL query, instead of a dozen different ones

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
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_performance_data', $product->product_details_performance_data );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', $product->product_details_packaging );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', $product->product_drawings );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', $product->technical_literature );

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

                } else { 
                    $this->set_last_error( 'Could not insert post.' );
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

                        // save images, drawings, technical literature
                        if ( $import_external_image === true ) {

                            // get the attachment image (and assign to this post as the featured image)
                            $attachment_id = WPX::get_attachment_id_from_filename( $product['image_file'] );
                            if ( $attachment_id ) {
                                set_post_thumbnail( $post_id, $attachment_id );
                                update_post_meta( $post_id, self::META_KEY_PREFIX . 'image', $attachment_id );
                            }

                            // save drawings and technical literature filenames as attachment ids
                            $drawings_attachment_ids = WPX::get_attachment_ids_from_filenames( $product['product_drawings_files'] );
                            update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_drawings', $drawings_attachment_ids );

                            $technical_literature_attachment_ids = WPX::get_attachment_ids_from_filenames( $product['technical_literature_files'] );
                            update_post_meta( $post_id, self::META_KEY_PREFIX . 'technical_literature', $technical_literature_attachment_ids );

                        }

                        // save post meta 
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_number', $product['product_number'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'mfg_number', $product['mfg_number'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'long_description', $product['long_description'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'short_description', $product['short_description'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_materials_of_construction', $product['product_details_materials_of_construction'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_connections', $product['product_details_connections'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_design', $product['product_details_design'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_performance_data', $product['product_details_performance_data'] );
                        update_post_meta( $post_id, self::META_KEY_PREFIX . 'product_details_packaging', $product['product_details_packaging'] );

                        // save post categories 
                        if ( $product['categories'] !== '' ) {

                            // get cat_ids from category names 
                            $cat_names = WPX::simple_explode( $product['categories'] );
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

                    } else {
                        $this->set_last_error( 'Could not update post.' );
                    }

                } else {
                    $this->set_last_error( 'Not a valid post_id or not a valid ITW Medical Product.' );
                }

                return $success;

            }  // end : update_product() 



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



            // -----------------------------------------------------------
            // HANDLE ERRORS
            // -----------------------------------------------------------

            public function get_last_error() {
                return $this->last_error;
            }

            private function set_last_error( $error ) {
                $this->last_error = $error;
            }

            
    } // end class: ITW_Product_DAL

endif;
