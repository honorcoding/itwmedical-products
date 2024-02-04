<?php
// -----------------------------------------------------------
// ITW_WP_Media - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      Handle Wordpress Media files (forms fields, display, etc)
//
//  Use: 
//      1. Enqueue the necessary styles and scripts 
//      \ITW_Medical\Media\ITW_WP_Media::enqueue_admin_styles_and_scripts();
//
//      2. Add fields to forms (meta boxes) where necessary
//            
//
//  Source: 
//      https://stackoverflow.com/questions/46752418/wordpress-select-image-from-media-library 
//
// -----------------------------------------------------------


namespace ITW_Medical\Media;
use ITW_Medical\Wordpress\WPX as WPX;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'ITW_WP_Media' ) ) :
    
    class ITW_WP_Media {

            const STYLES_AND_SCRIPTS_URL  = ITW_MEDICAL_PRODUCTS_URL . 'assets/';
            const STYLES_AND_SCRIPTS_PATH = ITW_MEDICAL_PRODUCTS_PATH . 'assets/';
            const DOCUMENT_IMAGE = ITW_MEDICAL_PRODUCTS_URL . 'assets/document-512x.jpg';
            const VIDEO_IMAGE    = ITW_MEDICAL_PRODUCTS_URL . 'assets/video-512x.jpg';
            const DEFAULT_IMAGE  = ITW_MEDICAL_PRODUCTS_URL . 'assets/placeholder-512x.jpg';


            // ---------------------------------------------------
            //  INSTANTIATION
            // ---------------------------------------------------

            public function __construct() {

                self::add_hooks_and_filters();
                
            }

            public static function add_hooks_and_filters() {

                // Ajax action to refresh the user image
                add_action( 'wp_ajax_itw_wp_media_get_image', [ static::class, 'ajax_get_attachments' ] );
                add_action( 'wp_ajax_nopriv_itw_wp_media_get_image', [ static::class, 'ajax_get_attachments' ] );                

            }

            public static function enqueue_media_styles_and_scripts() {

                // enqueue Wordpress media styles
                $css_slug = "itw-wp-media-styles"; 
                $css_uri = self::STYLES_AND_SCRIPTS_URL . 'css/admin-wp-media.css';
                $css_filetime = filemtime( self::STYLES_AND_SCRIPTS_PATH . 'css/admin-wp-media.css' );
                
                wp_register_style( $css_slug, $css_uri, array(), $css_filetime );
                wp_enqueue_style( $css_slug ); 
                

                // enqueue Wordpress media scripts
                if ( ! did_action( 'wp_enqueue_media' ) ) {
                    wp_enqueue_media();
                }                

                // enqueue class-specific scripts
                $js_slug = "itw-wp-media-scripts"; 
                $js_uri = self::STYLES_AND_SCRIPTS_URL . 'js/admin-wp-media.js';
                $js_filetime = filemtime( self::STYLES_AND_SCRIPTS_PATH . 'js/admin-wp-media.js' );

                wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
                wp_enqueue_script( $js_slug );   

            }



            // ---------------------------------------------------
            //  AJAX CALLS
            // ---------------------------------------------------

            // Ajax action to refresh the user image
            public static function ajax_get_attachments() {

                $html = '';

                if( isset( $_GET['attachment_ids'] ) ){

                    $attachment_ids = filter_input( INPUT_GET, 'attachment_ids', FILTER_SANITIZE_STRING );                         
                    $attachments = WPX::simple_explode( ',', $attachment_ids );                

                    $html = '<div class="itw_wp_media_images">';

                        if ( ! empty ( $attachments ) ) {
                            foreach( $attachments as $key => $attachment_id ) {
                                $html .= self::get_attachment_html( $attachment_id );
                            }
                        } 

                    $html .= '</div>';


                } else {

                    $html = '<div class="itw_wp_media_images"></div>';
                    $success = true;

                } // end : if attachment_ids

                $data = array(
                    'html' => $html,
                );

                wp_send_json_success( $data );

            } // end : ajax_get_image()



            // ---------------------------------------------------
            //  FORMS AND FIELDS
            // ---------------------------------------------------

            public static function get_field_html( $field_id, $attachment_ids, $class = '' ) {

                $html = '';

                // convert comma-separated-string into an array (remove empty strings) 
                $attachments = WPX::simple_explode( ',', $attachment_ids ); 

                // prepare class 
                if ( $class !== '' ) {
                    $class = ' ' . $class;
                }

                $html .= '<div class="itw_wp_media_field'.$class.'">';

                    $html .= '<div class="itw_wp_media_images">';

                        if ( ! empty ( $attachments ) ) {
                            foreach( $attachments as $key => $attachment_id ) {
                                $html .= self::get_attachment_html( $attachment_id );
                            }
                        }

                    $html .= '</div>';
                    
                    $html .= '<input type="hidden" name="'.$field_id.'" id="'.$field_id.'" value="'.$attachment_ids.'" class="regular-text itw_wp_media_text" />';
                    $html .= '<input type="button" value="Select Media" class="button-primary itw_wp_media_button" />';

                $html .= '</div>';                    

                return $html;

            } // end : get_field_html()



            // ---------------------------------------------------
            //  WP ATTACHMENTS 
            // ---------------------------------------------------

            public static function get_attachment_html( $attachment_id ) {

                $html = '';

                if ( is_numeric( $attachment_id ) ) {

                    $type = self::get_attachment_type( intval( $attachment_id ) );

                    switch( $type ) {

                        case 'image':
                            $html = self::get_attachment_image_html( $attachment_id );
                            break;

                        case 'document':
                            $path = get_attached_file( $attachment_id, true );
                            $file = basename( $path );
                            $html = self::get_attachment_document_html( $file );
                            break;

                        case 'video':
                            $path = get_attached_file( $attachment_id, true );
                            $file = basename( $path );
                            $html = self::get_attachment_video_html( $file );
                            break;

                        default:
                            $html = self::get_attachment_placeholder_html();
                            break;

                    }

                }

                return $html;

            }

            public static function get_attachment_type( $attachment_id ) {

                $type = 'none';

                // get attachment type 
                $file = get_attached_file( $attachment_id, true);
                if ( $file ) {

                    $mimetype = mime_content_type( $file );
                    if ( strpos( $mimetype, 'image' ) !== false ) {
                        $type = 'image';
                    } else if ( strpos( $mimetype, 'application' ) !== false ) {
                        $type = 'document';
                    } else if ( strpos( $mimetype, 'video' ) !== false ) {
                        $type = 'video';
                    } else {
                        $type = 'other';
                    }

                } 
                
                return $type;

            }

            public static function get_attachment_image_html( $attachment_id ) {
                $html  = '<div class="attachment_container">';
                $html .= wp_get_attachment_image( $attachment_id, 'thumbnail', false );
                $html .= '</div>';
                return $html;
            }

            public static function get_attachment_document_html( $filename ) {
                $html  = '<div class="attachment_container">';
                $html .= '<img src="'.self::DOCUMENT_IMAGE.'" width="150" height="150" />';
                $html .= '<div class="text">'.$filename.'</div>';
                $html .= '</div>';

                return $html;
            }

            public static function get_attachment_video_html() {
                $html  = '<div class="attachment_container">';
                $html .= '<img src="'.self::VIDEO_IMAGE.'" width="150" height="150" />';
                $html .= '<div class="text">'.$filename.'</div>';
                $html .= '</div>';
                return $html;
            }

            public static function get_attachment_placeholder_html() {
                $html  = '<div class="attachment_container">';
                $html .= '<img src="'.self::DEFAULT_IMAGE.'" width="150" height="150" />';
                $html .= '</div>';
                return $html;
            }


    } // end class: ITW_WP_Media

    // instantiate class to add hooks and filters 
    new ITW_WP_Media();

endif;
