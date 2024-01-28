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
                            'type'  => 'text',
                        ),
                    'mfg_number' =>
                        array(
                            'id'    => 'itw_mp_mfg_number',
                            'label' => 'MFG Number',
                            'type'  => 'text',
                        ),
                    'short_description' =>
                        array(
                            'id'    => 'itw_mp_short_description',
                            'label' => 'Short Description',
                            'type'  => 'text',
                        ),
                    'long_description' =>
                        array(
                            'id'    => 'itw_mp_long_description',
                            'label' => 'Long Descripion',
                            'type'  => 'text',
                        ),
                    'product_details_materials_of_construction' =>
                        array(
                            'id'    => 'itw_mp_product_details_materials_of_construction',
                            'label' => 'Product Details',
                            'type'  => 'textarea',
                        ),
                    'product_details_connections' =>
                        array(
                            'id'    => 'itw_mp_product_details_connections',
                            'label' => 'Product Details',
                            'type'  => 'textarea',
                        ),
                    'product_details_design' =>
                        array(
                            'id'    => 'itw_mp_product_details_design',
                            'label' => 'Product Details',
                            'type'  => 'textarea',
                        ),
                    'product_details_perfomance_data' =>
                        array(
                            'id'    => 'itw_mp_product_details_perfomance_data',
                            'label' => 'Product Details',
                            'type'  => 'textarea',
                        ),
                    'product_details_packaging' =>
                        array(
                            'id'    => 'itw_mp_product_details_packaging',
                            'label' => 'Product Details',
                            'type'  => 'textarea',
                        ),
                    'product_drawings' =>
                        array(
                            'id'    => 'itw_mp_product_drawings',
                            'label' => 'Product Drawings',
                            'type'  => 'text',
                        ),
                    'technical_literature' =>
                        array(
                            'id'    => 'itw_mp_technical_literature',
                            'label' => 'Technical Literature',
                            'type'  => 'text',
                        ),
                    'related_products' =>
                        array(
                            'id'    => 'itw_mp_related_products',
                            'label' => 'Related Products',
                            'type'  => 'text',
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
                    $fields = $this->populate_fields_with_product_data( $product );
                }

                // set up nonce 
                wp_nonce_field( 'itw_medical_product_update_post_metabox', 'itw_medical_product_update_post_nonce' );

                // display meta box 
                foreach ( $fields as $field ) {
                    ?>
                    <p>
                        <?php
                            if ( $field['type'] === 'text' ) {
                                ?>
                                <label for="<?php echo $field['id']; ?>"><?php esc_html_e( $field['label'], 'itw_medical_products' ); ?></label>
                                <br />
                                <input class="widefat" type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
                                <?php
                            } else if ( $field['type'] === 'textarea') {
                                ?>
                                <label for="<?php echo $field['id']; ?>"><?php esc_html_e( $field['label'], 'itw_medical_products' ); ?></label>
                                <br />
                                <textarea class="widefat" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" rows="4"><?php echo esc_attr( $field['value'] ); ?></textarea>
                                <?php
                            } else if ( $field['type'] === 'checkbox' ) {
                                if (  $field['value'] == 1  ) {
                                    $checked = ' checked="checked"';
                                } else {
                                    $checked = '';
                                }
                                ?>
                                <input class="widefat" type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="1" <?php echo $checked; ?> />
                                <label for="<?php echo $field['id']; ?>"><?php esc_html_e( $field['label'], 'itw_medical_products' ); ?></label>
                                <?php                                
                            }
                        ?>
                    </p>
                    <?php    
                }
            }

            // get the meta box fields and populate with values
            public function populate_fields_with_product_data( ITW_Product $product ) {

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
                    if ( $field['type'] === 'checkbox' ) {
                        // always update checkboxes
                        // (if $_POST is set, then checked. if $_POST is not set, then unchecked.)
                        $checked = ( isset( $_POST[ $field['id'] ] ) && $_POST[ $field['id'] ] ) ? "1" : "0";
                        update_post_meta( $post_id, $field['id'], $checked ); 
                    } else if ( array_key_exists( $field['id'], $_POST ) ) {
                        if ( $field['type'] === 'text' ) {
                            $product->$key = sanitize_text_field( $_POST[ $field['id'] ] );
                        } else if ( $field['type'] === 'textarea' ) {
                            $product->$key = $_POST[ $field['id'] ]; // do not sanitize. allows html tags, carriage returns, etc.
                        } else { 
                            $product->$key = '';
                        }                    
                    }
                }

                // save the meta box field values to post meta 
                $product_controller = itw_prod();
                $product_controller->save_product( $product );                

            } // end : save_post_meta_box()
              
    } // end class: Admin_ITW_Product_Single

    // create a single instantiation of this class 
    new Admin_ITW_Product_Single();

endif;
