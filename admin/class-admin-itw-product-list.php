<?php
// -----------------------------------------------------------
// Admin_ITW_Product_List - Class
// -----------------------------------------------------------
//  TABLE OF CONTENTS
//      - PRODUCT CATEGORY DROPDOWN 
//      - PRODUCT CATEGORIES IN LIST
// -----------------------------------------------------------

namespace ITW_Medical\Products\Admin;
use ITW_Medical\Products\ITW_Product;
use ITW_Medical\Wordpress\WP_Expanded as WPX;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)



if ( ! class_exists( 'Admin_ITW_Product_List' ) ) :
    
    class Admin_ITW_Product_List {

            public function __construct() {

                $this->load_hooks_and_filters();

            }


            public function load_hooks_and_filters() {

                // product category dropdown 
                add_action('restrict_manage_posts', array( $this, 'add_filters_to_list_page' ) );
                add_action('pre_get_posts', array( $this, 'handle_filters_on_list_page' ) );

                // product categories in list 
                add_filter( 'manage_'.ITW_Product::get_post_type().'_posts_columns',  array( $this, 'add_custom_columns_to_list_page' ) ); 
                add_action( 'manage_'.ITW_Product::get_post_type().'_posts_custom_column' ,  array( $this, 'add_data_to_custom_columns_for_list_page') , 10, 2 );

                // customize the search query to look in custom fields as well 
                add_filter( "pre_get_posts", array( $this, 'custom_search_query' ) );            

            }



            // ----------------------------------------------------
            // PRODUCT CATEGORY DROPDOWN
            // ----------------------------------------------------

            // add the filters for admin list page
            public function add_filters_to_list_page(){

                // only execute on the ITW_Product post type
                global $post_type;
                if( $post_type == ITW_Product::get_post_type() ){

                    
                    // ----------------------------------------------
                    // category dropdown
                    // ----------------------------------------------
                    
                    $args_categories = array(
                        'show_option_all'   => 'All Categories',
                        'orderby'           => 'NAME',
                        'order'             => 'ASC',
                        'name'              => 'iwp_cta_category_filter',
                        'taxonomy'          => ITW_Product::get_taxonomy(),
                    );

                    // if already selected, ensure that its value is set to be selected
                    if(isset($_GET['iwp_cta_category_filter'])){
                        $args_categories['selected'] = sanitize_text_field($_GET['iwp_cta_category_filter']);
                    }

                    wp_dropdown_categories($args_categories);
                    
                    
                } // end : if Client post type
            } // end : add_filters_to_list_page()


            // filter the posts by the applicable Client Request filter
            public function handle_filters_on_list_page($query){

                global $post_type, $pagenow;

                //if we are currently on the edit screen of the post type listings
                if( 
                    $pagenow == 'edit.php' && 
                    $post_type == ITW_Product::get_post_type()
                ){
                    
                    // ----------------------------------------------
                    // category dropdown
                    // ----------------------------------------------
                    
                    if( isset( $_GET['iwp_cta_category_filter'] ) ){

                        $category = sanitize_text_field($_GET['iwp_cta_category_filter']);
                        
                        //if the category is not 0 (which means all)
                        if( $category != 0 ){

                            $query->query_vars['tax_query'] = array(
                                array(
                                    'taxonomy'  => ITW_Product::get_taxonomy(),
                                    'field'     => 'ID',
                                    'terms'     => array( $category )
                                )
                            );

                        }
                        
                    } // end : if request category dropdown
                                        
                } // end : if editing client requests
            }



            // ----------------------------------------------------
            // PRODUCT CATEGORIES IN LIST
            // ----------------------------------------------------

            // CLIENT REQUESTS - ADD CUSTOM COLUMNS AND FILTERS TO CLIENT REQUESTS LIST
            // Add the custom columns to the post type:
            public function add_custom_columns_to_list_page($columns) {

                $new_columns = [
                    'product_id' => __( 'Product ID', 'ITW_MEDICAL_PRODUCTS' ), 
                    'mfg_id' => __( 'MFG ID', 'ITW_MEDICAL_PRODUCTS' ), 
                    'category' => __( 'Categories', 'ITW_MEDICAL_PRODUCTS' ), 
                ];
                $insert_before = 'date';                
                $columns = WPX::array_insert( $columns, $insert_before, $new_columns );

                return $columns;

            }


            // Add the data to the custom columns for the post type:
            public function add_data_to_custom_columns_for_list_page( $column, $post_id ) {

                switch ( $column ) {

                    case 'product_id' :

                        echo itw_prod()->get_product_number( $post_id );
                        break;

                    case 'mfg_id' :

                        echo itw_prod()->get_mfg_number( $post_id );
                        break;
                    
                    case 'category' :

                        $output = '';

                        // get all categories attached to this post 
                        $categories = wp_get_post_terms( $post_id, ITW_Product::get_taxonomy() );
                        if ( 
                            ! is_wp_error( $categories ) && 
                            is_array( $categories ) && 
                            ! empty( $categories ) 
                        ) {
                            foreach ( $categories as $cat ) {
                                $output .= $cat->name . ', ';
                            }
                            if ( substr( $output, -2 ) === ', ' ) {
                                $output = substr( $output, 0, -2 );
                            }
                        }

                        echo $output;
                        break;                        
                        
                    default : 
                        break;

                } // end : switch

            } // end : add_data_to_custom_columns_for_list_page()



            // ----------------------------------------------------
            // CUSTOM SEARCH QUERY 
            // ----------------------------------------------------

            function custom_search_query( $query ) {

                $itw_prod = itw_prod();

                $custom_fields = array(
                    // put all the meta fields you want to search for here
                    $itw_prod->get_post_meta_id( 'product_number' ),
                    $itw_prod->get_post_meta_id( 'mfg_number' ),
                );
                $searchterm = $query->query_vars['s'];

                // we have to remove the "s" parameter from the query, because it will prevent the posts from being found
                $query->query_vars['s'] = "";

                if ($searchterm != "") {
                    $meta_query = array('relation' => 'OR');
                    foreach($custom_fields as $cf) {
                        array_push($meta_query, array(
                            'key' => $cf,
                            'value' => $searchterm,
                            'compare' => 'LIKE'
                        ));
                    }
                    $query->set("meta_query", $meta_query);
                };
            }


    
    } // end class: Admin_ITW_Product_List

    // create a single instantiation of this class 
    new Admin_ITW_Product_List();

endif;
