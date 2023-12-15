<?php
// -----------------------------------------------------------
// ITW_Product_Single_Client_View - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles display and interaction of ITW_Product in client view
//      (i.e. visitor end of website)
//
// -----------------------------------------------------------

namespace ITW_Medical\Products\Client;
use ITW_Medical\Products\ITW_Product;
use ITW_Medical\Products\ITW_Product_Controller;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)



if ( ! class_exists( 'ITW_Product_Single_Client_View' ) ) :
    
    class ITW_Product_Single_Client_View {

            // allows multiple shortcodes to display the same product (see: maybe_load_product())
            private $post_id = null;
            private $product = null;

            const TEMPLATE_PATH       = ITW_MEDICAL_PRODUCTS_PATH . 'templates/';
            const TEMPLATE_PARTS_PATH = ITW_MEDICAL_PRODUCTS_PATH . 'templates/single-product/';

            public function __construct() {

                // TODO: check if itw_product single (or print) - if so, then proceed, otherwise do nothing ? ... 
                //       thoughts: this would prevent unnecessary loading of shortcodes, but will it be overly restrictive? 
                //       see below: is_itw_product_single()

                $this->load_hooks_and_filters();

            }

            public function load_hooks_and_filters() {

                // TODO: add shortcodes 
                //       include capability for filters 

                // ARCHIVE PAGE 


                // SINGLE PAGE TEMPLATE 

                add_shortcode( 'itw_medical_product_single_content', array( $this, 'itw_medical_product_single_content_shortcode' ) );


                // SINGLE PAGE SHORTCODES
                // [itw_product view="header"]
                // [itw_product view="order"]
                //    which uses:
                //      [itw_product view="ordering_info"]
                //      [itw_product view="ordering_info_extended"]
                // [itw_product_tabs] 
                //    which uses: 
                //      [itw_product view="details"]
                //      [itw_product view="drawings"]
                //      [itw_product view="warranty"]
                //      [itw_product view="technical"]
                // [itw_product view="related"]

                add_shortcode( 'itw_product', array( $this, 'itw_product_single_shortcode' ) );
                add_shortcode( 'itw_product_tabs', array( $this, 'itw_product_single_tabs_shortcode' ) );


                // SINGLE PAGE PRINT 
                // [itw_product_print]
                //    which uses:
                //      [itw_product view="header"]
                //      [itw_product view="ordering_info"]
                //      [itw_product view="details"]
                //      [itw_product view="drawings"]
                //      [itw_product view="warranty"]
                //      [itw_product view="technical"]

                add_shortcode( 'itw_product_print', array( $this, 'itw_product_print_shortcode' ) );


            }


            // loads the product into the object variables (if not already loaded)
            // used to prevent shortcodes from having to load a product multiple times on the same page 
            // note: this does not allow shortcodes to load multiple products on the same page
            public function maybe_load_product( $post_id ) {

                if ( 
                    $post_id !== $this->post_id || 
                    is_null( $this->post_id ) ||     
                    is_null( $this->product ) 
                ) {

                    $this->post_id = null;
                    $this->product = null;

                    if ( ITW_Product::is_itw_product( $post_id ) ) {

                        $product = itw_prod()->get_product( $post_id );
                        if ( $product ) { 
                            $this->post_id = $post_id;
                            $this->product = $product;
                        }                  

                    }

                }

                if ( ! is_null( $this->product ) ) {
                    return $this->product;
                } else {
                    return false;
                }

            }



            // ------------------------------------------------------
            // ARCHIVE PAGES - SHORTCODES 
            // ------------------------------------------------------



            // ------------------------------------------------------
            // SINGLE PAGE - SHORTCODES 
            // ------------------------------------------------------

            // show product views 
            public function itw_product_single_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    'post_id' => '',
                    'view' => '',
                ), $atts));
                
                // by default, use the current post 
                if( $post_id === '' ) {
                    $post_id = get_the_ID();    
                }

                // prepare output
                $output = '';

                // load product
                $product = $this->maybe_load_product( $post_id );

                if ( $product && $view !== '' ) {

                    // generate shortcode output from template file
                    ob_start(); 
                        include self::TEMPLATE_PARTS_PATH . $view . '.php';
                    $output = ob_get_clean();
                                    
                }

                // return shortcode output
                return $output;
                
            }


            // show product tabs
            public function itw_product_single_tabs_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    'post_id' => '',
                ), $atts));
                
                // by default, use the current post 
                if( $post_id === '' ) {
                    $post_id = get_the_ID();    
                }

                // prepare output
                $output = '';

                // load product
                $product = $this->maybe_load_product( $post_id );

                if ( $product ) {

                    // generate shortcode output from template file
                    ob_start(); 
                        include self::TEMPLATE_PARTS_PATH . 'tabs.php';
                    $output = ob_get_clean();
                                    
                }

                // return shortcode output
                return $output;
                
            }



            // ------------------------------------------------------
            // SINGLE PAGE PRINT - SHORTCODES 
            // ------------------------------------------------------

            // show print page 
            public function itw_product_print_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    'post_id' => '',
                ), $atts));
                
                // by default, use the current post 
                if( $post_id === '' ) {
                    $post_id = get_the_ID();    
                }

                // prepare output
                $output = '';

                // load product
                $product = $this->maybe_load_product( $post_id );

                if ( $product ) {

                    // generate shortcode output from template file
                    ob_start(); 
                        include self::TEMPLATE_PARTS_PATH . 'print.php';
                    $output = ob_get_clean();
                                    
                }

                // return shortcode output
                return $output;
                
            }              



            // ------------------------------------------------------
            // SINGLE PAGE TEMPLATE 
            // ------------------------------------------------------

            public function itw_medical_product_single_content_shortcode( $atts = array(), $content='' ) {

                // set up default parameters
                extract(shortcode_atts(array(
                    //'post_id' => '',
                ), $atts));
                

                // only process if itw_product cpt and single page 
                if ( 
                    ITW_Product::is_itw_product( get_the_ID() ) && 
                    is_single()
                ) {

                    // generate single page content from template file 
                    ob_start(); 
                        include self::TEMPLATE_PATH . 'single-itw-product-content.php';
                    $new_content = ob_get_clean();

                    return $new_content;
                    
                }

            }



    } // end class: ITW_Product_Single_Client_View

    // create a single instantiation of this class 
    new ITW_Product_Single_Client_View();

endif;