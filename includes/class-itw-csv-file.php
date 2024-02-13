<?php
// -----------------------------------------------------------
// ITW_CSV_File - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles csv files
//
// -----------------------------------------------------------

namespace ITW_Medical\CSV;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)



if ( ! class_exists( 'ITW_CSV_File' ) ) :
    
    class ITW_CSV_File {

            // ---------------------------------------------------
            //  INSTANTIATION
            // ---------------------------------------------------

            public function __construct() {}

            /** 
             * Import a CSV file to an array variable
             */
            public static function import_csv_file_to_array( $file_path, $has_header = true ) {

                $data = array();
                $header = array();

                // attempt to open the file
                $handle = fopen( $file_path, "r" );
                if ( $handle !== FALSE ) {

                    // loop through each line (row) of the file 
                    $row_count = 0;
                    
                    // TODO: catch errors (i.e. if $handle is not valid csv, etc...)
                    while ( ( $row = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) {

                        // loop through each column in that row
                        $total_cols = count($row);
                        $row_data = array();
                        for ( $col = 0; $col < $total_cols; $col++ ) {

                            if ( $has_header ) {

                                if ( $row_count === 0 ) {
                                    $header[ $col ] = $row[ $col ];
                                } else {
                                    $row_data[ $header[ $col ] ] = $row[ $col ];
                                }

                            } else {

                                $row_data[ $col ] = $row[ $col ];

                            }                            

                        }
                        
                        if ( ! empty( $row_data ) ) {
                            $data[] = $row_data;
                        }

                        // prepare for next loop
                        $row_count++;

                    } // end : while

                    // close the file 
                    fclose($handle);

                // else (file could not be opened)
                } else {

                    // state error message 
                    $data['error'] = 'File could not be opened or does not exist.';

                } // end : if file could be opened
                
                return $data;

            } // end: import_csv_file_to_array()


            /**
             * Exports an array table as a CSV browser attachment (immediate download)
             *
             * @param array $table - an array of rows. each row is an array of columns. 
             *                       example: $table = array(
             *                                   'header_row' => array( 'h1', 'h2', 'h3' ),
             *                                   'row_1'      => array( 'c1', 'c2', 'c3' ),
             *                                   'row_2'      => array( 'c4', 'c5', 'c6' ),
             *                                );
             *
             * @return string/boolean - returns a string of CSV data or false on failure
             * 
             * Note: see https://www.php.net/manual/en/function.fputcsv.php 
             */
            public static function array_to_csv( array $table ) {

                // open the file output (piping to memory instead of file) 
                $f = fopen('php://memory', 'r+');

                // go through each table row and out put the csv 
                $csv = '';
                foreach( $table as $row ) {
                    if (fputcsv($f, $row) === false) {
                        return false;   // on error, return false 
                    }
                }                

                // rewind the output pipe and dump the contents to the $csv variable
                rewind( $f );
                $csv = stream_get_contents($f);

                // close the output pipe
                fclose( $f );

                // return the results
                return $csv; 

            } // end : array_to_csv() 


    } // end class: ITW_CSV_File

    new ITW_CSV_File();

endif;


