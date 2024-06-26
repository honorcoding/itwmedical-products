<?php

// -----------------------------------------------------------
// ITW_Product : Class
//
// Purpose:
//     ITW_Product object
//
// Usage:
//     Created by calling ITW_Product_Controller 
//     e.g. itw_prod()->get_product( $post_id );
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
            const TABLE_HEADER = array(
                'post_id',
                'product_number',
                'mfg_number',
                'title',
                'short_description',
                'long_description',
                'image',
                'image_file',
                'product_details_materials_of_construction',
                'product_details_connections',
                'product_details_design',
                'product_details_performance_data',
                'product_details_packaging',
                'product_drawings',
                'product_drawings_files',
                'technical_literature',
                'technical_literature_files',
                'categories'
            );

            
            // product data 
            public $post_id;
            public $product_number;
            public $mfg_number;
            public $title;
            public $short_description;
            public $long_description;
            public $image;
            public $image_file;
            public $product_details_materials_of_construction;
            public $product_details_connections;
            public $product_details_design;
            public $product_details_performance_data;
            public $product_details_packaging;
            public $product_drawings;
            public $product_drawings_files;
            public $technical_literature;
            public $technical_literature_files;
            public $categories;


            public function __construct() {}


            // get constants
            public static function get_post_type() {
                return self::CUSTOM_POST_TYPE;
            }

            public static function get_taxonomy() {
                return self::CUSTOM_TAXONOMY;
            }


            // general tools 
            public static function is_itw_product( $post_id ) {

                $post_type = get_post_type( $post_id );
                if ( $post_type === self::get_post_type() ) {
                    return true;
                } else {
                    return false;
                }    

            }

            public static function get_table_header() {
                return self::TABLE_HEADER;
            }

            public static function get_import_export_header() {

                $header = self::TABLE_HEADER;

                // remove fields that are unnecessary for import/export
                foreach( $header as $key => $value ) {
                    switch( $value ) {
                        case 'post_id':
                        case 'image':
                        case 'product_drawings':
                        case 'technical_literature':
                            unset( $header[ $key ] );
                            break;
                        default:
                            break;
                    }
                }

                return $header;

            }


            /**
             * Converts ITW_Product from object to array  
             */
            public function get_data( $with_labels = true ) {

                $header = self::get_table_header();
                $data = array();
                
                foreach( $header as $key ) {
                    if ( $with_labels ) {
                        $data[ $key ] = $this->$key;
                    } else {
                        $data[] = $this->$key;
                    }
                }

                return $data;

            }

            public function get_export_data( $with_labels = true ) {

                $header = self::get_import_export_header();
                $data = array();
                
                foreach( $header as $key ) {
                    if ( $with_labels ) {
                        $data[ $key ] = $this->$key;
                    } else {
                        $data[] = $this->$key;
                    }
                }

                return $data;

            }

            /*
            public function get_data_as_html() {

                $html_data = array();

                $data = $this->get_data();
                foreach ( $data as $key => $value ) {

                    $html_data[ $key ] = nl2br( $value );

                }

            }
            */


            // -----------------------------------------------------
            // CSV TABLE
            // -----------------------------------------------------

            /*
            // get data as csv table (with or without header)
            public function get_csv( $with_header = true ) {

                $results = '';                

                // get the data 
                $data = $this->get_data( false );

                // convert the header to a CSV table (add a PHP_EOL)
                if ( $with_header === true ) {
                    $results = self::get_csv_header() . PHP_EOL;                        
                }

                // convert the row to a csv table
                $results .= self::convert_array_to_csv( $data );

                return $results;

            }

            public static function get_csv_header() {

                return self::convert_array_to_csv( self::get_table_header() );

            }

            public static function convert_array_to_csv( $array ) {

                $results = '';

                if ( is_array( $array ) && ! empty( $array ) ) {

                    foreach( $array as $value ) {
                        $results .= self::csv_escape( $value ) . ',';
                    }
                    $results = rtrim( $results, ',' );

                }

                return $results;

            }

            public static function csv_escape( $string ) {

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
            */

    
    } // end class: ITW_Product

endif;
