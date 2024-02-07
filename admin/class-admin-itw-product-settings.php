<?php
// -----------------------------------------------------------
// Admin_ITW_Product_Settings - Class
// -----------------------------------------------------------
//  TABLE OF CONTENTS
// -----------------------------------------------------------

namespace ITW_Medical\Products\Admin;
use ITW_Medical\File\ITW_File_Upload;
use ITW_Medical\CSV\ITW_CSV_File;
use ITW_Medical\Products\ITW_Product;

// TODO : think this through... what is best way to handle a file download? with an external class? 
//        or straight here in the settings page? ...
//        can we add javascript and ajax calls (with a callback? a static callback?) 
use ITW_Medical\File\ITW_File_Download;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)



if ( ! class_exists( 'Admin_ITW_Product_Settings' ) ) :
    
    class Admin_ITW_Product_Settings {

            // import file upload object
            protected $import_file_upload = null;
            const IMPORT_FILE_FIELD_ID = 'itw_import_file';
            const IMPORT_FILE_FORM_ID  = 'itw_import_file_upload_form';
            const IMPORT_FILE_UPLOAD_FOLDER = 'itw_import';


            // path for display templates
            const TEMPLATE_PATH       = ITW_MEDICAL_PRODUCTS_PATH . 'templates/admin/';


            public function __construct() {

                $this->load_hooks_and_filters();

            }


            public function load_hooks_and_filters() {

                // additional values for javascript to know 
                add_action( 'admin_head', array( $this, 'javascript_data_for_this_page' ) );

                // add admin menu
                add_action( 'admin_menu', array( $this, 'add_settings_page_to_admin_menu') );

                // Ajax action to export product csv data
                add_action( 'wp_ajax_itw_get_product_csv_data', array( $this, 'get_product_csv_data' ) );
                add_action( 'wp_ajax_nopriv_itw_get_product_csv_data', array( $this, 'get_product_csv_data' ) );                

            }

            public function javascript_data_for_this_page() {

                // if on the medical-product settings page
                $current_screen = get_current_screen();
                if ( 
                    isset( $current_screen->id ) && 
                    $current_screen->id === 'itw-medical-product_page_itwmp-settings' 
                ) {
                    ?>
                    <script type="text/javascript">
                        var itw = {
                            ajax_url:'<?php echo admin_url('admin-ajax.php'); ?>',
                            import_form_id: '<?php echo self::IMPORT_FILE_FORM_ID; ?>',
                        };
                    </script>
                    <?php
                } 

            }

            public function get_import_file_upload() {

                // create the file upload object (if not already created)
                if ( is_null( $this->import_file_upload ) ) {

                    $this->import_file_upload = new ITW_File_Upload(
                        self::IMPORT_FILE_FIELD_ID,
                        self::IMPORT_FILE_FORM_ID,
                        ITW_File_Upload::get_wordpress_upload_path( self::IMPORT_FILE_UPLOAD_FOLDER ) 
                    );
        
                }

                // validate $file_upload object
                if ( 
                    $this->import_file_upload && 
                    $this->import_file_upload instanceof ITW_File_Upload 
                ) {
                    return $this->import_file_upload;
                } else {
                    return false;
                }

            }


            // ------------------------------------------------------
            // ADD ADMIN PAGE 
            // ------------------------------------------------------

            public function add_settings_page_to_admin_menu() {
                if (is_admin()) {
                    $parent_page = 'edit.php?post_type=itw-medical-product';
                    $page_title  = 'ITW Medical Products - Settings';
                    $menu_title  = 'Settings';
                    $page_slug   = 'itwmp-settings';
                    add_submenu_page( $parent_page, $page_title, $menu_title, 'manage_options', $page_slug, array( $this, 'render_settings_page' ) );                    
                }
            }


            // ------------------------------------------------------
            // DISPLAY ADMIN PAGE 
            // ------------------------------------------------------

            // render the settings page
            public function render_settings_page() {

                $messages = $this->process_form_submissions();

                $import_args = array(
                    'file_upload' => $this->get_import_file_upload(),
                );

                $export_args = array(
                    'export_link' => $this->get_csv_export_download_link( 'Export All Products' ),
                );

                $warranty_args = array(
                    'warranty' => itw_prod()->get_warranty(),
                );            

                // get a list of all pages on the site 
                $args = array(
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
                );
                $all_pages = get_posts( $args );

                $product_list_page_args = array(
                    'product_list_page_id' => get_option( ITW_PRODUCT_LIST_PAGE_ID_OPTION_KEY ),
                    'all_pages' => $all_pages,
                );    

                $args = array(
                    'messages'          => $messages,
                    'import_section'    => $this->get_template( 'parts/import', $import_args ),
                    'export_section'    => $this->get_template( 'parts/export', $export_args ), 
                    'warranty_section'  => $this->get_template( 'parts/warranty', $warranty_args ), 
                    'product_list_page' => $this->get_template( 'parts/product_list_page', $product_list_page_args ),
                );

                $page_html = $this->get_template( 'settings-page-content', $args );

                echo $page_html;

            }

            // get page templates
            public function get_template( $template_name, $args = array() ) {

                // generate content from template file 
                ob_start(); 
                    include self::TEMPLATE_PATH . $template_name . '.php';
                $content = ob_get_clean();

                return $content;
                
            }
    

            // ------------------------------------------------------
            // PROCESS FORM SUBMISSIONS 
            // ------------------------------------------------------

            // process form submissions
            protected function process_form_submissions() {

                $messages = array();

                // process import form 
                $messages = $this->process_import_form( $messages ); 

                // process warranty form 
                $messages = $this->process_warranty_form( $messages );

                // process product_list_page form 
                $messages = $this->process_product_list_page_form( $messages );

                return $messages;

            }


            protected function process_import_form( $messages ) {

                // upload the import file 
                $file_upload = $this->get_import_file_upload();
                if ( $file_upload ) {

                    $file_upload_results = $file_upload->handle_form_submission();
                    if ( $file_upload_results ) {

                        // if no error on file upload
                        if ( $file_upload_results['has_error'] === 'false' ) {

                            // deprecated: alert that the file upload worked
                            // $this->add_message( $messages, $file_upload_results['message'], 'ok' );

                            // -----------------------------------------
                            // import the file data 
                            // -----------------------------------------

                            // get the path of the uploaded file 
                            $file_data = $file_upload->get_files();
                            if ( isset( $file_data['uploadPath'] ) ) {

                                $file_path = $file_data['uploadPath'];      

                                // import the csv file data into an array
                                $csv_data = ITW_CSV_File::import_csv_file_to_array( $file_path );

                                // delete the temporary import file (no longer required)
                                if ( file_exists( $file_path ) ) {
                                    unlink( $file_path );
                                }                                 

                                // if no errors while importing the csv file
                                if ( ! isset( $csv_data['error'] ) ) {

                                    // import the csv data as ITW_Products 
                                    $product_controller = itw_prod();

                                    $import_messages = $product_controller->import( $csv_data );

                                    // convert the $import_messages into a string 
                                    $import_messages_string = '';
                                    foreach( $import_messages as $key => $msg ) {
                                        if ( $key > 0 ) {
                                            $import_messages_string .= '<br/>';
                                        }
                                        $import_messages_string .= $msg;
                                    }

                                    // if error, then flag
                                    if ( 
                                        strpos( $import_messages_string, 'could not' ) !== false ||
                                        strpos( $import_messages_string, 'Invalid' ) !== false 
                                    ) {
                                        $type = 'error';
                                    } else {
                                        $type = 'ok';
                                    }

                                    // declare results
                                    $this->add_message( $messages, $import_messages_string, $type );    

                                // else (if error while importing the csv file)
                                } else {
                                    // state error 
                                    $this->add_message( $messages, $csv_data['error'], 'error' );  
                                }
        
                            } else {
                                // report the file upload error message 
                                $this->add_message( $messages, 'Invalid upload path.', 'error' );  
                            }
                                                        
                        // else (if error on file upload)
                        } else {
                            // report the file upload error message 
                            $this->add_message( $messages, $file_upload_results['message'], 'error' );
                        }
                        
                    } // end : if file upload results

                } else {
                    $this->add_message( $messages, 'ITW_File_Upload object not found.', 'error' );
                }

                return $messages;

            } // end : process_import_form()

            // process warranty form 
            protected function process_warranty_form( $messages ) {

                if ( isset( $_POST['itwmp-warranty-submit'] ) && $_POST['itwmp-warranty-submit'] === 'Update' ) {
                    if ( isset( $_POST['itwmp-warranty-field'] ) ) {
                        $warranty = $_POST['itwmp-warranty-field'];
                        itw_prod()->set_warranty( $warranty );
                        $this->add_message( $messages, 'Product warranty updated.' );
                    }                    
                }

                return $messages;

            }

            // process product_list_page form
            protected function process_product_list_page_form( $messages ) {

                if ( isset( $_POST['itwmp-plp-submit'] ) && $_POST['itwmp-plp-submit'] === 'Update' ) {
                    if ( isset( $_POST['itwmp-plp-field'] ) ) {
                        $product_list_page_id = $_POST['itwmp-plp-field'];
                        update_option( ITW_PRODUCT_LIST_PAGE_ID_OPTION_KEY, $product_list_page_id );
                        $this->add_message( $messages, 'Product list page updated.' );
                    }                    
                }

                return $messages;

            }

            // handle messages
            protected function add_message( &$messages, $text, $type = 'ok' ) {

                if ( ! is_array( $messages ) ) {
                    $messages = array();
                }

                $messages[] = array(
                    'type' => $type,
                    'text' => $text,
                );

            }


            // ------------------------------------------------------
            // IMPORT TOOLS
            // ------------------------------------------------------

            /**
             * Get CSV Export Download Link
             */
            public function get_import_product_button( $title = 'Export' ) {
                return '<input type="button" onclick="itw_download_export_file()" value="'.$title.'" />';
            }


            // ------------------------------------------------------
            // EXPORT TOOLS
            // ------------------------------------------------------

            /**
             * Handles Ajax call to get product csv data 
             */
            public function get_product_csv_data() {

                // get array of data for all products
                $product_controller = itw_prod();
                $product_data = $product_controller->export_all();


                // convert product data to csv
                $products_as_csv = ITW_CSV_File::array_to_csv( $product_data );
                if ( $products_as_csv === false ) {
                    $products_as_csv = '';
                }

                $data = array(
                    'products' => $products_as_csv,
                );

                wp_send_json_success( $data );

            }

            /**
             * Get CSV Export Download Link
             */
            public function get_csv_export_download_link( $title = 'Export' ) {
                return '<input type="button" onclick="itw_download_export_file()" value="'.$title.'" />';
            }

 
    } // end class: Admin_ITW_Product_Settings

    // create a single instantiation of this class 
    new Admin_ITW_Product_Settings();

endif;

