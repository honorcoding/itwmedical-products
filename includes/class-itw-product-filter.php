<?php
// -----------------------------------------------------------
// ITW_Product_Filter - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles filtering for ITW_Products
//
// -----------------------------------------------------------

namespace ITW_Medical\Filter;
use ITW_Medical\Filter\ITW_Filter;
use ITW_Medical\Products\ITW_Product;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)


if ( ! class_exists( 'ITW_Product_Filter' ) ) :

    class ITW_Product_Filter extends ITW_Filter {
          
            public function __construct() {

                $this->filters = array(
                    array(
                        'id'    => 'itw_category',
                        'type'  => 'taxonomy',
                        'slug'  => ITW_Product::CUSTOM_TAXONOMY,
                    ),
                );

                // call parent constructor to configure settings 
                parent::__construct();
                
            }

            // get query args
            public function get_query_args() {

                $query_args = array(
                    'post_type' => ITW_Product::CUSTOM_POST_TYPE,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                );

                foreach( $this->filters as $filter ) {

                    if ( $filter['type'] === 'taxonomy' ) {

                        $value = $this->get_query_var( $filter['id'] );  

                        // do not add tax query if not required 
                        if ( $value && $value !== '' && $value !== 'all' ) {

                            $tax_query = array(
                                'tax_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'taxonomy' => $filter['slug'],
                                        'field' => 'slug',
                                        'terms' => $value,
                                        'operator' => 'IN'
                                    ),
                                ),
                            );

                            $query_args = array_merge( $query_args, $tax_query );

                        }                        

                    }

                }
                
                return $query_args;

            }

            // get filter values 
            public function get_filter_values() {

                $filter_values = array();

                foreach( $this->filters as $filter ) {

                    if ( $filter['type'] === 'taxonomy' ) {

                        $value = $this->get_query_var( $filter['id'] );  

                        // do not add tax query if not required 
                        if ( $value && $value !== '' && $value !== 'all' ) {

                            $term = get_term_by( 'slug', $value, ITW_Product::CUSTOM_TAXONOMY );

                            if ( $term ) {
                                $term_id = $term->term_id;
                            } else {
                                $term_id = false;
                            }
                            
                            if ( $term_id ) {
                                $term_description = term_description( $term );
                            } else {
                                $term_description = false;
                            }

                            $filter_values[] = array(
                                'id'    => 'itw_category',
                                'type'  => 'taxonomy',
                                'slug'  => ITW_Product::CUSTOM_TAXONOMY,
                                'value' => $value,
                                'term_id' => $term_id,
                                'term_description' => $term_description, 
                            ); 

                        } 

                    }

                }

                return $filter_values;

            }


    } // end class: ITW_Product_Filter

endif;
