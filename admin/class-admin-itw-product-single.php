<?php
// -----------------------------------------------------------
// Admin_ITW_Product_Single - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles editing single posts of custom post type: itw-medical-product
//
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

            public $meta_box_fields;

            public function __construct() {

                $this->meta_box_fields = array(
                    'product_number' => 
                        array(
                            'id'    => 'itw_mp_product_number',
                            'label' => 'Product Number',
                        ),
                    'mfg_number' =>
                        array(
                            'id'    => 'itw_mp_mfg_number',
                            'label' => 'MFG Number',
                        ),
                    'short_description' =>
                        array(
                            'id'    => 'itw_mp_short_description',
                            'label' => 'Short Description',
                        ),
                    'long_description' =>
                        array(
                            'id'    => 'itw_mp_long_description',
                            'label' => 'Long Descripion',
                        ),
                    'product_drawings' =>
                        array(
                            'id'    => 'itw_mp_product_drawings',
                            'label' => 'Product Drawings',
                        ),
                    'warranty' =>
                        array(
                            'id'    => 'itw_mp_warranty',
                            'label' => 'Warranty',
                        ),
                    'technical_literature' =>
                        array(
                            'id'    => 'itw_mp_technical_literature',
                            'label' => 'Technical Literature',
                        ),
                    'related_products' =>
                        array(
                            'id'    => 'itw_mp_related_products',
                            'label' => 'Related Products',
                        ),
                );

                $this->load_hooks_and_filters();

            }


            public function load_hooks_and_filters() {

                // display meta box
                add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 1 );

                // save meta box
                add_action( 'save_post', array( $this, 'save_post_meta_box' ), 10, 2 );            

            }


            // ----------------------------------------------------
            // ADMIN SINGLE EDIT PAGE
            // ----------------------------------------------------

            // add meta box 
            public function add_meta_box() {           
                add_meta_box( 
                    'itw_medical_product_metabox', 
                    'Product Information', 
                    array( $this, 'meta_box_html' ), 
                    'itw-medical-product', 
                    'normal', 
                    'high'
                );
            }
            
            // display meta box 
            public function meta_box_html( $post ) {

                // get product information
                $product_controller = itw_prod();
                $product = $product_controller->get_product( $post->ID );

                // get fields and populate with product information 
                if ( $product ) {
                    $fields = $this->get_meta_box_fields_and_populate_with_values( $product );
                }

                // set up nonce 
                wp_nonce_field( 'itw_medical_product_update_post_metabox', 'itw_medical_product_update_post_nonce' );

                // display meta box 
                foreach ( $fields as $field ) {
                    ?>
                    <p>
                        <label for="<?php echo $field['id']; ?>"><?php esc_html_e( $field['label'], 'itw_medical_products' ); ?></label>
                        <br />
                        <input class="widefat" type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
                    </p>
                    <?php    
                }
            }

            // get the meta box fields and populate with values
            public function get_meta_box_fields_and_populate_with_values( ITW_Product $product ) {

                $fields = $this->meta_box_fields;

                if ( $product ) {
                    foreach ( $fields as $key => $field ) {

                        // get values from $product and add to meta box fields 
                        $fields[ $key ]['value'] = $product->$key;

                    }
                }

                return $fields;

            } 

            // save meta box 
// debug: left off here...   
// it did not save... why?          
            function save_post_meta_box( $post_id, $post ) {

                $edit_cap = get_post_type_object( $post->post_type )->cap->edit_post;
                if( ! current_user_can( $edit_cap, $post_id ) ) {
                    return;
                }

                if( 
                    ! isset( $_POST['itw_medical_product_update_post_nonce']) || 
                    ! wp_verify_nonce( $_POST['itw_medical_product_update_post_nonce'], 'itw_medical_product_update_post_metabox' ) 
                ) {
                    return;
                }
                
                // prepare the new ITW_Product object
                $product = new ITW_Product();
                $product->post_id               = $post_id;
                $product->title                 = get_the_title( $post_id );
                $product->product_details       = get_the_content( $post_id );
                $product->image                 = get_the_post_thumbnail( $post_id );
                // note: the product dal only saves the post meta 
                //       (title, product details, and image are already saved via normal Wordpress process)
              

                // populate the product with field values
                $fields = $this->meta_box_fields;
                foreach( $fields as $key => $field ) {
                    if ( array_key_exists( $field['id'], $_POST ) ) {
                        $product->$key = sanitize_text_field( $_POST[ $field['id'] ] );
                    } else { 
                        $product->$key = '';
                    }                    
                }

                // save the meta box field values to post meta 
                $product_controller = itw_prod();
                $product_controller->save_product( $product );                

            }
              
/*
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
*/


    } // end class: Admin_ITW_Product_Single

    // create a single instantiation of this class 
    new Admin_ITW_Product_Single();

endif;
