<?php
// -----------------------------------------------------------
// ITW_Product_Archive_Client_View - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles display and interaction of ITW_Product in client view
//      (i.e. visitor end of website)
//
// -----------------------------------------------------------

namespace ITW_Medical\Products\Client;
use ITW_Medical\Products\ITW_Product;
use ITW_Medical\Filter\ITW_Product_Filter;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)


if ( ! class_exists( 'ITW_Product_Archive_Client_View' ) ) :
    
    class ITW_Product_Archive_Client_View {

            private $product_controller = null;
            private $product_filter = null;

            const TEMPLATE_PATH       = ITW_MEDICAL_PRODUCTS_PATH . 'templates/';
            const TEMPLATE_PARTS_PATH = ITW_MEDICAL_PRODUCTS_PATH . 'templates/multiple-product/';

            public function __construct() {

                $this->load_hooks_and_filters();

            }

            public function load_hooks_and_filters() {

                // show product filters
                add_shortcode( 'itw_product_filters', array( $this, 'itw_product_filters_shortcode' ) );

                // show list of products 
                add_shortcode( 'itw_products', array( $this, 'itw_products_shortcode' ) );

            }


            // loads the filter into the object var (if not already loaded)
            // used to prevent shortcodes from having to load a filter multiple times on the same page 
            public function get_product_filter() {

                if ( is_null( $this->product_controller ) ) {
                    $this->product_controller = new ITW_Product_Filter();
                }

                if ( ! is_null( $this->product_controller ) ) {
                    return $this->product_controller;
                } else {
                    return false;
                }

            }

            public function get_product_controller() {

                if ( is_null( $this->product_filter ) ) {
                    $this->product_filter = itw_prod();
                }

                if ( ! is_null( $this->product_filter ) ) {
                    return $this->product_filter;
                } else {
                    return false;
                }

            }


            // displays the product filters
            public function itw_product_filters_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    //'url' => '',
                ), $atts));

                // get terms as options
                $terms = get_terms( array(
                    'taxonomy'   => ITW_Product::CUSTOM_TAXONOMY,
                ) );

                $options = array();
                if (  
                    ! is_wp_error( $terms )  &&  
                    ! empty( $terms ) 
                ) {
                    $options['all'] = 'All Categories';
                    foreach( $terms as $term ) {
                        $options[ $term->slug ] = $term->name;
                    }
                } 

                // get filter
                $filter = $this->get_product_filter();

                if ( $filter ) {

                    // generate shortcode output from template file
                    ob_start(); 
                        include self::TEMPLATE_PARTS_PATH . 'product-filter.php';
                    $output = ob_get_clean();
                                    
                }

                // return shortcode output
                return $output;

            }

            // displays a list of filtered products 
            public function itw_products_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    //'post_id' => '',
                ), $atts));

                // get filter
                $filter = $this->get_product_filter();

                // get list of products
                $products = array();
                if ( $filter ) {

                    // gets a list of all product_ids filtered by itw_category
                    $query_args             = $filter->get_query_args();
                    $query_args['fields']   = 'ids';
                    $product_ids            = get_posts( $query_args );

                    $prod_ctl = $this->get_product_controller();

                    // get all products
                    foreach( $product_ids as $product_id ) {
                        $products[] = $prod_ctl->get_product( $product_id );
                    }

                }

                // generate shortcode output from template file
                ob_start(); 
                    include self::TEMPLATE_PARTS_PATH . 'product-list.php';
                $output = ob_get_clean();

                // return shortcode output
                return $output;

            }


    } // end class: ITW_Product_Archive_Client_View

    // create a single instantiation of this class 
    new ITW_Product_Archive_Client_View();

endif;
