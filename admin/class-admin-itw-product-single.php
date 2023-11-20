<?php
// -----------------------------------------------------------
// Admin_ITW_Product_Single - Class
// -----------------------------------------------------------
//  TABLE OF CONTENTS
//      - ADMIN LIST PAGE
//      - ADMIN SINGLE EDIT PAGE
//      - HELPER TOOLS 
// -----------------------------------------------------------

namespace ITW_Medical\Products\Admin;
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



if ( ! class_exists( 'Admin_ITW_Product_Single' ) ) :
    
    class Admin_ITW_Product_Single {

            // itw medical product controller
            private $itw_prod = null;


            public function __construct() {

                $this->itw_prod = itw_prod();
                //$this->load_hooks_and_filters();

            }


            public function load_hooks_and_filters() {

                // admin single edit page 
                //add_action( 'acf/save_post', array( $this, 'on_save_post' ) );     
                //add_filter( 'acf/load_field/key=field_64dab926dfc10', array( $this, 'load_request_status_field' ) );                          
                //add_filter( 'acf/load_field/key=field_64dab992dfc11', array( $this, 'load_approval_status_field' ) );                          

            }


            // ----------------------------------------------------
            // ADMIN SINGLE EDIT PAGE
            // ----------------------------------------------------

            // HANDLE Client Request FIELDS AFTER SAVE 
            public function on_save_post( $post_id ) {

                // make sure it is a valid post of the type ITW_Product 
                $post = get_post( $post_id ); 
                if ( 
                    $post == null || 
                    $post->post_type != ITW_Product::get_post_type() 
                ) {
                    return;
                } 

                // if the post content was changed
                if ( $post->post_content != '' ) { 
                    
                    // get existing client request data 
                    $request = $this->itw_prod->get_request( $post_id );
                    if ( $request ) {

                        // unhook this function so it does not loop infinitely
                        remove_action( 'acf/save_post', 'on_save_post' );

                        // update the conversation with the new post content 
                        $request = $itw_prod->update_conversation( $request, $post->post_content, 'staff' );

                        // delete the post content
                        wp_update_post( array( 'ID' => $post_id, 'post_content' => '' ) );              
                    
                        // re-hook this function
                        add_action( 'acf/save_post', 'on_save_post' );

                    }
                    
                    
                } // end : if post content was changed 
                
            } // end : on_save_post()

            // POPULATE REQUEST STATUS FIELD 
            public function load_request_status_field( $field ) {
                
                // Reset choices
                $field['choices'] = array( '' => '- Select -' );  // start with a blank option

                // Get field from options page
                $statuses = ITW_Product::get_request_statuses(); 

                // Get only names, emails and ids in array
                foreach( $statuses as $key => $value ) {
                    $field['choices'][ $key ] = $value;
                }

                // Return choices
                return $field;

            }

            // POPULATE APPROVAL STATUS FIELD 
            public function load_approval_status_field( $field ) {
                
                // Reset choices
                $field['choices'] = array( '' => '- Select -' );  // start with a blank option

                // Get field from options page
                $statuses = ITW_Product::get_approval_statuses(); 

                // Get only names, emails and ids in array
                foreach( $statuses as $key => $value ) {
                    $field['choices'][ $key ] = $value;
                }

                // Return choices
                return $field;

            }



            // ----------------------------------------------------
            // HELPER TOOLS 
            // ----------------------------------------------------

    
    } // end class: Admin_ITW_Product_Single

    // create a single instantiation of this class 
    new Admin_ITW_Product_Single();

endif;
