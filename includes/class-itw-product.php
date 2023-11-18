<?php

// -----------------------------------------------------------
// ITW_Product : Class
//
// Purpose:
//     ITW_Product object
//
// Usage:
//     Created by calling ITW_Product_Controller 
//     e.g. prod()->get_product( $post_id );
//     
//     Dump to csv as follows: 
//     $csv_table = $product->dump( $product::CSV, false ); // includes table header 
//     $csv_row   = $product->dump( $product::CSV, true );  // no header 
// -----------------------------------------------------------

namespace ITW_Medical\Products;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'ITW_Product' ) ) :
    
    class ITW_Product {


            // product CPT constants
            const CUSTOM_POST_TYPE = 'itw-medical-product';
            const CUSTOM_TAXONOMY  = 'itw-medical-product-category';

            // csv constants
            const ARRAY            = 'ARRAY';
            const CSV              = 'CSV';
            

            // product data 
            public $post_id         = null; 
            

            public function __construct() {

                    /*
                    // set request constants
                    if ( is_null( self::$custom_post_type ) ) {
                        self::$custom_post_type = 'itw-medical-product';
                    } 
                    */   

            }


            // get constants
            public static function get_post_type() {
                    return self::CUSTOM_POST_TYPE;
            }

            public static function get_taxonomy() {
                    return self::CUSTOM_TAXONOMY;
            }


            // dump data as table (csv string or array, with or without header)
            public function dump( $type = self::CSV, $no_header = false ) {

                    // create the table header (as array)
                    $table = array();

                    if ( $no_header === false ) {

                        $table['header'] = array(
                            // headers go here
                            'post "id"',
                            'item1',
                            'item 2',
                        );

                    }

                    // create the table row (as array)
                    $table['row'] = array(
                        // data declared here
                        $this->post_id,
                        'this is the time',
                        'this is the other, item',
                    );           

                    if ( $type === self::CSV ) {

                        $results = '';
                        
                        // convert the array to a CSV table 
                        if ( $no_header === false ) {
                            $count = count( $table['header'] );
                            for( $i = 0; $i < $count; $i++ ) { 
                                $results .= $this->csv_escape( $table['header'][$i] );
                                if ( $i < ( $count - 1 ) ) {
                                    $results .= ',';
                                } else {
                                    $results .= PHP_EOL;
                                }
                            }
                        }

                        $count = count( $table['row'] );
                        for( $i = 0; $i < $count; $i++ ) { 
                            $results .= $this->csv_escape( $table['row'][$i] );
                            if ( $i < ( $count - 1 ) ) {
                                $results .= ',';
                            } 
                        }

                    } else {

                        // use the array table
                        $results = $table;

                    }

                    return $results;

            }

            public function csv_escape( $string ) {
                    $string = strval( $string );
                    if( 
                        strpos( $string, ',' ) !== false || 
                        strpos( $string, '"' ) !== false 
                    ) {
                        $string = str_replace( '"', '""', $string );  // escape double quotes
                        $string = '"' . $string . '"';                 // put quotes around the entire string
                    }
                    return $string;
            }

    
    } // end class: ITW_Product

endif;
