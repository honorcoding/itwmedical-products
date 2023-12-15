<?php
// -----------------------------------------------------------
// ITW_Filter - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles filtering
//
// -----------------------------------------------------------

namespace ITW_Medical\Filter;
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



if ( ! class_exists( 'ITW_Filter' ) ) :
    
    abstract class ITW_Filter {

            protected $filters = null;
            protected $filter_ids = null;

            public function __construct() {

                /* example:
                $this->filters = array(
                    array(
                        'id'    => 'itw_category',
                        'type'  => 'taxonomy',
                        'slug'  => 'itw-medical-product-category',
                    ),
                );

                // call parent constructor to configure settings 
                parent::__construct();
                */

                $this->filter_ids = array();
                if ( isset( $this->filters ) && is_array( $this->filters ) ) {
                    foreach( $this->filters as $filter ) {
                        $this->filter_ids[] = $filter['id'];
                    }    
                }

                $this->load_hooks_and_Filter();

            }

            public function load_hooks_and_Filter() {

                // declare filter parameters (for url)
                add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

            }

            // declare filter parameters (for url)
            public function add_query_vars( $vars ) {

                foreach( $this->filter_ids as $filter_id ) {
                    $vars[] = $filter_id;
                }                

            }

            public function get_query_var( $key ) {

                $value = get_query_var( $key ); 

                if ( 
                    $value === '' && 
                    isset( $_GET[ $key ] )
                ) {
                    $value = $_GET[ $key ];
                }

                return $value;

            }

            // get a filtered url 
            public function add_filter_params_to_url( $filters, $old_url = '' ) {

                $query = array();

                foreach( $filters as $key => $value ) {
                    if ( in_array( $key, $this->filter_ids ) ) {
                        $query[ $key ] = urlencode( $value );
                    }                    
                }

                if ( $old_url !== '' ) {
                    $new_url = esc_url( add_query_arg( $query, $old_url ) );
                } else {
                    $new_url = esc_url( add_query_arg( $query ) ); // if no old_url specified, then assumes current url 
                }                

                return $new_url;

            }

            // get query args (to use in query)
            abstract protected function get_query_args();

    } // end class: ITW_Filter

endif;
