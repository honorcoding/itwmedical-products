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
             * @param array $fields - the array table 
             * @param boolean $headers - whether or not the array table has headers
             *
             * Note: https://www.php.net/manual/en/function.fputcsv.php 
             *       (Examples include techniques for generating CSV files or just returning strings)
             */
            public static function array_to_csv( array $table, $headers = true ) {

                // send response headers to the browser
                /*
                header( 'Content-Type: text/csv' );
                header( 'Content-Disposition: attachment;filename='.$filename);
                */
$csv = 'some text';
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"report.csv\";" );
header("Content-Transfer-Encoding: binary");

echo $csv;
exit;           
/*     
                $fp = fopen('php://output', 'w');

                if($headers) {
                    // output header row (if at least one row exists)
                    if ( isset( $table[0] ) ) {
                        $row = $table[0];
                        fputcsv($fp, array_keys($row));
                    }
                }    

                foreach( $table as $row ) {
                    fputcsv($fp, $row);
                }

                fclose($fp);            
*/
            } // end : export_to_csv_attachment() 
            

    } // end class: ITW_CSV_File

    new ITW_CSV_File();

endif;


