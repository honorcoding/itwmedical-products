<?php
// -----------------------------------------------------------
// WP_Expanded - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      Expands basic Wordpress and PHP functionality 
// 
// -----------------------------------------------------------


namespace ITW_Medical\Wordpress;
use KM_Download_Remote_Image;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'WP_Expanded' ) ) :
    
    class WP_Expanded {


            /** 
             * Get a list of filenames from a list of wordpress attachment ids
             * 
             * @param array/string $ids - an array or comma-delineated string of attachment ids 
             * @param string $output - [ 'ARRAY' or 'STRING' ] 
             * 
             * @return array/string - returns an array or comma-delineated string of filenames 
             *                        (depending on $output requested)
             */
            public static function get_filenames_from_attachment_ids ( $ids, $output = 'ARRAY' ) {

                $filenames = array();

                // convert a comma-deliminated string into an array
                if ( ! is_array( $ids ) ) {
                    $ids = self::simple_explode( ',', $ids );
                }

                // get the filename for each id (use empty string if no attachment found) 
                foreach( $ids as $id ) {
                    $filename = self::get_filename_from_attachment_id( $id );
                    $filename = ( $filename ) ? $filename : '';
                    $filenames[ $id ] = $filename;
                }   

                if ( $output === 'STRING' ) {
                    $filenames = self::simple_implode( ',', $filenames );
                }

                return $filenames;

            }
             

            /** 
             * Get a filename from a wordpress attachment id
             * 
             * @param string $id - an attachment id
             * 
             * @return string - returns a filename (or false if unsuccessful)
             */
            public static function get_filename_from_attachment_id ( $id ) {

                $filename = false;

                if ( is_numeric( $id ) ) {
                    $id = intval( $id );
                    if ( $id > 0 ) {
                        $filename = get_attached_file( $id, true ); // return false if unsuccessful
                    }
                }

                return $filename;

            }


            /**
             * Get a comma-delineated list of attachment ids 
             * from a list of filenames (array or comma-delineated string) 
             *
             * @param  array/string $filenames - a list of filenames to convert to attachment_ids
             *
             * @return string $ids - a list of attachment_ids (blank if unsuccessful)
             */
            public function get_attachment_ids_from_filenames( $filenames ) {
                
                $ids = '';

                // convert a comma-deliminated string into an array
                if ( ! is_array( $filenames ) ) {
                    $filenames = self::simple_explode( ',', $filenames );
                }

                if ( is_array( $filenames ) ) {
                    foreach( $filenames as $filename ) { 
                        $id = $this->get_attachment_id_from_filename( $filename );
                        if ( $id ) {
                            $ids = ( $ids !== '' ) ? $ids . ',' . $id : $id;
                        }
                    }
                }

                return $ids;

            }


            /** 
             * Get an attachment id from a filename 
             * If no such attachment exists, then download from external source 
             * 
             * @param string $filename
             *
             * @return int/boolean $id or false (if unsuccessful)
             */
            public function get_attachment_id_from_filename( $filename ) {

                // if the image url is from this site
                $attachment_id = 0;
                if ( self::is_url_from_this_site( $filename ) ) {
                    // attempt to find that attachment_id
                    $attachment_id = attachment_url_to_postid( $filename );  // returns 0 on failure
                }

                // if an attachment_id was not found, or this image is from another site
                if ( $attachment_id === 0 ) {

                    // download image from external URL, insert into Media files, and save to post_id
                    $download_remote_image = new KM_Download_Remote_Image( $filename );
                    $attachment_id         = $download_remote_image->download();
                    
                }

                // if unsuccessful, then return false 
                if ( ! $attachment_id || $attachment_id === 0 ) {
                    $attachment_id = false;
                }

                return $attachment_id;

            }


            /**  
             * Check if url if from this site
             * 
             * @param string $url 
             * 
             * @return boolean true/false
             */
            public static function is_url_from_this_site( $url ) {

                $site_url_parsed = wp_parse_url( get_site_url() );
                $site_host = $site_url_parsed['host'];
                
                $url_parsed = wp_parse_url( $url );
                $url_host = $url_parsed['host'];

                $is_site_url = ( $site_host === $url_host ) ? true : false;
                return $is_site_url;

            }
            

            /**
             * Simplifies PHP explode 
             * (internally handles errors and removes empty strings)
             * 
             * @param string $delimiter - token separator 
             * @param string $string - string to tokenize
             * 
             * @return array (returns an empty array if no valid tokens found) 
             */
            public static function simple_explode( $delimiter, $string ) {

                if ( $delimiter !== '' ) { 
                    $array = explode( $delimiter, $string ); 
                } else {
                    $array = array();
                }

                // get rid of empty strings
                foreach( $array as $key => $value ) {
                    $value = trim( $value );
                    if ( $value === '' ) {
                        unset( $array[ $key ] );
                    }
                }

                return $array;

            }


            /**
             * Simplifies PHP implode
             * (internally handles errors and removes empty strings)
             * 
             * @param string $glue - token separator 
             * @param array $array - join $array elements into a string
             * 
             * @return string (returns an empty string if no elements found) 
             */
            public static function simple_implode( $glue, $array ) {

                $string = '';

                if ( is_array( $array ) ) {
                    foreach( $array as $token ) {
                        $token = trim( $token );
                        if ( $token && $token !== '' ) {
                            $string = ( $string !== '' ) ? $string . $glue . $token : $token; 
                        }
                    }
                }

                return $string;

            }      


    } // end class: WP_Expanded

endif;
