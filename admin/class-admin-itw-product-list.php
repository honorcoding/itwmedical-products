<?php
// -----------------------------------------------------------
// Admin_ITW_Product_List - Class
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



if ( ! class_exists( 'Admin_ITW_Product_List' ) ) :
    
    class Admin_ITW_Product_List {

            // client request controller
            private $itw_prod = null;


            public function __construct() {

                $this->itw_prod = itw_prod();
                //$this->load_hooks_and_filters();

            }


            public function load_hooks_and_filters() {

                // admin list page
                //add_action('pre_get_posts', array( $this, 'handle_filters_on_list_page' ) );
                //add_action('restrict_manage_posts', array( $this, 'add_filters_to_list_page' ) );
                //add_action( 'manage_'.ITW_Product::get_post_type().'_posts_custom_column' ,  array( $this, 'add_data_to_custom_columns_for_list_page') , 10, 2 );
                //add_filter( 'manage_'.ITW_Product::get_post_type().'_posts_columns',  array( $this, 'add_custom_columns_to_list_page' ) ); 

            }



            // ----------------------------------------------------
            // ADMIN LIST PAGE
            // ----------------------------------------------------

            // CLIENT REQUESTS - ADD CUSTOM COLUMNS AND FILTERS TO CLIENT REQUESTS LIST
            // Add the custom columns to the post type:
            public function add_custom_columns_to_list_page($columns) {
                $columns['request_status']    = __( 'Request Status', 'your_text_domain' );
                $columns['approval_status']   = __( 'Approval Status', 'your_text_domain' );
                $columns['assigned_to_user']  = __( 'Assigned To', 'your_text_domain' );
                return $columns;
            }

            // Add the data to the custom columns for the post type:
            public function add_data_to_custom_columns_for_list_page( $column, $post_id ) {
                switch ( $column ) {
                    
                    case 'request_status' :
                        $output = '';
                        // check if field exists: only_visible_to_user 
                        $statuses = ITW_Product::get_request_statuses();
                        $field_value = get_field('iwp_request_status');
                        if ( isset( $statuses[ $field_value ] ) ) {
                            $output =  $statuses[ $field_value ];                
                        }
                        echo $output;
                        break;
                        
                    case 'approval_status' :
                        $output = '';
                        // check if field exists: only_visible_to_user 
                        $statuses = ITW_Product::get_approval_statuses();
                        $field_value = get_field('iwp_request_approval_status');
                        if ( isset( $statuses[ $field_value ] ) ) {
                            $output =  $statuses[ $field_value ];                
                        }
                        echo $output;
                        break;
                        
                    case 'assigned_to_user' :
                        $output = '';
                        // check if field exists: only_visible_to_user 
                        $only_visible_to_user =  get_field('iwp_only_visible_to_user');

                        if ( $only_visible_to_user !== '' ) {
                            
                            // get username
                            $user = get_user_by( 'id', $only_visible_to_user );
                            if ( $user !== false ) {
                                // get username 
                                $output = $user->display_name . '<br />' .$user->user_email;
                            } 
                            
                        }                
                        echo $output;
                        break;
                        
                }
            }

            // add the filters for admin client requests list
            public function add_filters_to_list_page(){

                // only execute on the ITW_Product post type
                global $post_type;
                if( $post_type == ITW_Product::get_post_type() ){

                    
                    // ----------------------------------------------
                    // request category dropdown
                    // ----------------------------------------------
                    
                    $args_request_categories = array(
                        'show_option_all'   => 'All Request Categories',
                        'orderby'           => 'NAME',
                        'order'             => 'ASC',
                        'name'              => 'iwp_cta_request_category_filter',
                        'taxonomy'          => 'iwp_request_category'
                    );

                    // if already selected, ensure that its value is set to be selected
                    if(isset($_GET['iwp_cta_request_category_filter'])){
                        $args_request_categories['selected'] = sanitize_text_field($_GET['iwp_cta_request_category_filter']);
                    }

                    wp_dropdown_categories($args_request_categories);
                    
                    
                    // ----------------------------------------------
                    // request_status dropdown 
                    // ----------------------------------------------        
                    
                    $field_id = 'iwp_cta_request_status_filter';
                    $all_label = 'All request statuses';        
                    $options = ITW_Product::get_request_statuses();

                    // if already selected, ensure that its value is set to be selected
                    if( isset( $_GET['iwp_cta_request_status_filter'] ) ){
                        $selected = sanitize_text_field($_GET['iwp_cta_request_status_filter']);
                    } else {
                        $selected = '';
                    }
                    
                    echo $this->iwp_create_admin_dropdown( $field_id, $all_label, $options, $selected );
                    

                    // ----------------------------------------------
                    // approval_status dropdown 
                    // ----------------------------------------------        
                    
                    $field_id = 'iwp_cta_approval_status_filter';
                    $all_label = 'All approval statuses';        
                    $options = ITW_Product::get_approval_statuses();

                    // if already selected, ensure that its value is set to be selected
                    if( isset( $_GET['iwp_cta_approval_status_filter'] ) ){
                        $selected = sanitize_text_field($_GET['iwp_cta_approval_status_filter']);
                    } else {
                        $selected = '';
                    }
                    
                    echo $this->iwp_create_admin_dropdown( $field_id, $all_label, $options, $selected );
                    

                    // ----------------------------------------------
                    // assigned_to dropdown
                    // ----------------------------------------------
                    
                    $field_id = 'iwp_cta_assigned_to_filter';
                    $all_label = 'All assigned users';
                    
                    // get a list of all users who have been assigned to a Client Request
                    $options = $this->iwp_get_all_users_assigned_to_ITW_Products(); 

                    // if already selected, ensure that its value is set to be selected
                    if( isset( $_GET['iwp_cta_assigned_to_filter'] ) ){
                        $selected = sanitize_text_field($_GET['iwp_cta_assigned_to_filter']);
                    } else {
                        $selected = '';
                    }
                    
                    echo $this->iwp_create_admin_dropdown( $field_id, $all_label, $options, $selected );
                    
                    
                } // end : if Client Request post type
            } // end : add_filters_to_list_page()


            // filter the posts by the applicable Client Request filter
            public function handle_filters_on_list_page($query){

                global $post_type, $pagenow;

                //if we are currently on the edit screen of the post type listings
                if( 
                    $pagenow == 'edit.php' && 
                    $post_type == ITW_Product::get_post_type()
                ){
                    
                    // ----------------------------------------------
                    // request category dropdown
                    // ----------------------------------------------
                    
                    if( isset( $_GET['iwp_cta_request_category_filter'] ) ){

                        $request_category = sanitize_text_field($_GET['iwp_cta_request_category_filter']);
                        
                        //if the request_category is not 0 (which means all)
                        if( $request_category != 0 ){

                            $query->query_vars['tax_query'] = array(
                                array(
                                    'taxonomy'  => 'iwp_request_category',
                                    'field'     => 'ID',
                                    'terms'     => array( $request_category )
                                )
                            );

                        }
                        
                    } // end : if request category dropdown
                    
                    
                    // ----------------------------------------------
                    // assigned_to dropdown
                    // ----------------------------------------------
                    
                    if( isset( $_GET['iwp_cta_assigned_to_filter'] ) ){
                        
                        $assigned_to = sanitize_text_field( $_GET['iwp_cta_assigned_to_filter'] );
                        
                        //if the assigned_to is not 0 (which means all)
                        if( $assigned_to != 0 ){

                            $meta_query = array(
                                'key'       => 'iwp_only_visible_to_user',
                                'value'     => $assigned_to,
                                'compare'   => '='
                            );

                            if ( isset( $query->query_vars['meta_query'] ) ) {
                                $query->query_vars['meta_query'][] = $meta_query;
                            } else {
                                $query->query_vars['meta_query'] = array( $meta_query );
                            }

                        } // if $assigned_to
                        
                    } // end : if assigned_to dropdown
                    
                    
                    // ----------------------------------------------
                    // request_status dropdown 
                    // ----------------------------------------------
                    
                    if( isset( $_GET['iwp_cta_request_status_filter'] ) ){
                        
                        $request_status = sanitize_text_field( $_GET['iwp_cta_request_status_filter'] );
                        
                        //if the assigned_to is not 0 (which means all)
                        if( $request_status != 0 ){

                            $meta_query = array(
                                'key'       => 'iwp_request_status',
                                'value'     => $request_status,
                                'compare'   => '='
                            );

                            if ( isset( $query->query_vars['meta_query'] ) ) {
                                $query->query_vars['meta_query'][] = $meta_query;
                            } else {
                                $query->query_vars['meta_query'] = array( $meta_query );
                            }

                        } // if $request_status
                        
                    } // end : if request_status dropdown        
                    
                    
                    // ----------------------------------------------
                    // approval_status dropdown 
                    // ----------------------------------------------
                    
                    if( isset( $_GET['iwp_cta_approval_status_filter'] ) ){
                        
                        $approval_status = sanitize_text_field( $_GET['iwp_cta_approval_status_filter'] );
                        
                        //if the assigned_to is not 0 (which means all)
                        if( $approval_status != 0 ){

                            $meta_query = array(
                                'key'       => 'iwp_request_approval_status',
                                'value'     => $approval_status,
                                'compare'   => '='
                            );

                            if ( isset( $query->query_vars['meta_query'] ) ) {
                                $query->query_vars['meta_query'][] = $meta_query;
                            } else {
                                $query->query_vars['meta_query'] = array( $meta_query );
                            }

                        } // if $approval_status
                        
                    } // end : if approval_status dropdown        
                    
                    
                } // end : if editing client requests
            }


            // ----------------------------------------------------
            // HELPER TOOLS 
            // ----------------------------------------------------

            // get a list of all users who have been assigned to client requests
            public function iwp_get_all_users_assigned_to_ITW_Products() {
                
                $assigned_users = array();
                
                
                // get the users who have been assigned to client requests
                global $wpdb;
                $sql = '
                    SELECT pm.meta_value AS user_id
                    FROM '.$wpdb->prefix.'posts AS p 
                    INNER JOIN '.$wpdb->prefix.'postmeta AS pm ON p.ID = pm.post_id 
                    WHERE 
                        p.post_type = "'.ITW_Product::get_post_type().'" AND
                        pm.meta_key = "iwp_only_visible_to_user" AND 
                        pm.meta_value != "" 
                    ';
                
                $results = $wpdb->get_results($sql, ARRAY_A);
                
                
                // reformat the results
                $user_ids = array();    
                foreach( $results as $user_meta ) {
                    $user_ids[] = $user_meta['user_id'];
                }

                
                // get rid of any duplicates 
                array_unique( $user_ids );
                
                
                // create a simple list of 'display_name' => 'user_id'   
                $user_list = array(); 
                foreach( $user_ids as $user_id ) {
                    $user = get_user_by( 'id', $user_id );
                    if ( $user !== false ) {
                        $user_list[]= array( 
                            'display_name' => $user->display_name, 
                            'user_id'    => $user->ID
                        );
                    }         
                }      
                
                
                // alphabetize the list 
                $length = count( $user_list );
                $tmp = array();
                if ( $length > 0 ) {
                    for ($i = 0; $i < $length; $i++) {
                        for ($j = 0; $j < $length; $j++) {
                            if ( $user_list[$i]['display_name'] < $user_list[$j]['display_name'] ) {
                                
                                // swap
                                $tmp = $user_list[$i];
                                $user_list[$i] = $user_list[$j];
                                $user_list[$j] = $tmp;
                                
                            }
                        }
                    }    
                }
            
                
                // prepare the list for consumption
                foreach( $user_list as $user ) {
                    $assigned_users[ $user['user_id'] ] = $user['display_name'];
                }
                
                
                // return the list of assigned users 
                return $assigned_users;    
                    
            } // end : iwp_get_all_users_assigned_to_ITW_Products()


            // create a select field 
            public function iwp_create_admin_dropdown( $field_id, $all_label, $options, $selected = '' ) {
                
                $selected_text = ' selected="selected"';

                $o = '';
                
                    $o .= '<select id="'.$field_id.'" name="'.$field_id.'" class="postform">';
                    
                        // first item in select field
                        $all_selected = ( $selected == '' ) ? $selected_text : '';
                        $o .= '<option value="0"'.$all_selected.'>' . $all_label . '</option>';
                        
                        // subsequent items in select field 
                        if ( isset( $options ) ) {
                            foreach( $options as $value => $label ) {

                                $option_selected = ( $selected == $value ) ? $selected_text : '';
                                $o .= '<option value="'.$value.'"'.$option_selected.'>'.$label.'</option>';

                            }
                        }
                
                    $o .= '</select>';
                
                return $o;
                
            }

    
    } // end class: Admin_ITW_Product_List

    // create a single instantiation of this class 
    new Admin_ITW_Product_List();

endif;
