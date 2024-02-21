<?php
// -----------------------------------------------------------
// ITW_File_Upload - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      handles manual file uploads
//
//  What it does not do: 
//      does not upload to Wordpress Media files
//      for that, use: media_handle_upload()
//
//  How to Use: 
//      1. ON ADMIN PAGE 1, CREATE FILE UPLOAD OBJECT, THEN HANDLE FORM DISPLAY AND SUBMISSION 
//      2. ON ADMIN PAGE 2, CREATE FILE UPLOAD OBJECT, THEN GET FILE DATA 
//
//      // use directive
//      use ITW_Medical\File\ITW_File_Upload;
//
//      // CREATE FILE UPLOAD OBJECT 
//      $file_upload_field_id = 'itw_import_file';
//      $file_upload_form_id = 'itw_import_file_upload_form';
//      $file_upload_directory = ITW_File_Upload::get_wordpress_upload_path( 'itw_import' );
//      $this->file_upload = new ITW_File_Upload(
//          $file_upload_field_id,
//          $file_upload_form_id,
//          $file_upload_directory 
//      );
//      
//      ...
//      // DIPLAY FILE UPLOAD FORM
//      echo $file_upload->get_form_html();
//
//      ...
//      // HANDLE FILE UPLOAD FORM SUBMISSION
//      $file_upload->handle_form_submission();     // NOTE: saves file data in wordpress options 
//
//      ...
//      // GET FILE DATA
//      $file_upload->get_files();                  // NOTE: retrieves file data from wordpress options 
//
//
//  How to display the file upload field in existing form: 
//      1. get the name of the existing form
//      2. create the file upload object using that form name 
//      3. instead of DISPLAY FILE UPLOAD FORM (above), use...
//          a. get the javascript and display it 
//              echo $file_upload->get_existing_form_javascript();
//          b. display the field within the existing form 
//              echo $file_upload->get_field_html();
//
// -----------------------------------------------------------

namespace ITW_Medical\File;


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


// make sure itw-medical-product tools are activated
if ( ! defined( 'ITW_MEDICAL_PRODUCTS' ) ) {
    return false;
}
// can safely use tools now (e.g. itw_prod()->...)



if ( ! class_exists( 'ITW_File_Upload' ) ) :
    
    class ITW_File_Upload {

            // field params
            protected $field_id;    
            protected $form_id;
            protected $upload_directory; 
            protected $upload_directory_permissions;
            protected $file_extensions;
            protected $multiple;

            // uploaded data
            protected $files = null;

            // constants
            const WP_OPTION_PREFIX = 'itw_file_upload_';



            // ---------------------------------------------------
            //  INSTANTIATION
            // ---------------------------------------------------

            public function __construct(                                             
                                            $field_id, 
                                            $form_id, 
                                            $upload_directory,                  
                                            $permissions = 0755, 
                                            $file_extensions = array( 'csv' ),  // NOTE: must be an array
                                            $multiple = false
                                        ) {

                $this->field_id = $field_id;
                $this->form_id = $form_id;
                $this->upload_directory = $upload_directory;
                $this->upload_directory_permissions = $permissions;
                $this->file_extensions = $file_extensions;
                $this->multiple = ( $multiple === true ) ? true : false;

            }



            // ---------------------------------------------------
            //  DISPLAY FORM (OR FIELD)
            // ---------------------------------------------------

            public function get_form_html() {

                $html = '
                    <form id="'.$this->form_id.'" method="post" enctype="multipart/form-data">
                        ' . $this->get_field_html() . '
                        <input id-"'.$this->form_id.'_submitted" name="'.$this->form_id.'_submitted" type="submit" value="Import" />
                    </form>
                ';

                return $html;

            }

            public function get_field_html() {

                if ( $this->multiple === true ) {
                    $multiple = ' multiple="multiple"';
                    $id = $this->field_id;
                    $name = $this->field_id . '[]';
                } else {
                    $multiple = '';
                    $id = $this->field_id;
                    $name = $this->field_id;
                }

                $html = '<input id="'.$id.'" name="'.$name.'" type="file"' . $multiple . ' />';

                return $html;

            }

            public function get_existing_form_javascript() {

                $script = '
                    <script type="text/javascript">
                        // prepare the ' . $this->form_id . ' form to upload files
                        var form = document.getElementById( "' . $this->form_id . '" );
                        form.encoding = "multipart/form-data";
                        form.setAttribute( "enctype", "multipart/form-data" );
                    </script>                
                ';

                return $script;

            }



            // ---------------------------------------------------
            //  HANDLE FORM SUBMISSION
            // ---------------------------------------------------

            public function handle_form_submission() { 

                $report = false;
                
                // check if the form was submitted 
                if (
                    $_SERVER['REQUEST_METHOD'] == 'POST' && 
                    (
                        ( 
                            $this->multiple === true &&                             // if field multiple uploads allowed
                            isset( $_FILES[ $this->field_id ]['name'][0] ) &&
                            ! empty( $_FILES[ $this->field_id ]['name'][0] )
                        ) ||
                        ( 
                            $this->multiple !== true &&                             // if single upload only
                            isset( $_FILES[ $this->field_id ]['name'] ) &&
                            ! empty( $_FILES[ $this->field_id ]['name'] )
                        )
                    )
                ){

                    // attempt to upload the files 
                    $this->upload_files();

                    // check for errors and get messages
                    $report = $this->get_upload_report();

                } 

                return $report;

            } // end : handle_form_submission()

            /* 
             * uploads files into the specified folder 
            **/
            protected function upload_files() { 
                
                if ( $this->multiple === true ) {

                    return $this->upload_multiple_files();

                } else {

                    return $this->upload_single_file();

                }

            }


            // upload multiple files into the specified folder (the field is set to upload multiple files)
            protected function upload_multiple_files() { 
                
                $files = array(); 

                // if file upload field is populated
                if ( 
                        isset( $_FILES[ $this->field_id ]['name'] ) &&
                        is_array( $_FILES[ $this->field_id ]['name'] ) &&
                        $_FILES[ $this->field_id ]['name'][0] != ''
                    ) {

                    // ------------------------------------------
                    // process the file upload
                    // ------------------------------------------     
                    $total_files = count( $_FILES[ $this->field_id ]['name'] );

                    // check if the upload directory exists. if not, attempt to create. check for issues.
                    // (no point in uploading files if directory does not exist and cannot be created.)
                    $upload_directory_exists = $this->maybe_create_upload_directory( $this->upload_directory, $this->upload_directory_permissions );
                    if ( $upload_directory_exists ) {

                        $php_upload_max_filesize = intval( ini_get('upload_max_filesize') );

                        for( $i = 0; $i < $total_files; $i++ ) {

                            // prepare
                            $files[$i]['errors'] = array(); // Store errors here

                            // get file details
                            $files[$i]['fileName'] = $_FILES[$this->field_id]['name'][$i];
                            $files[$i]['fileSize'] = $_FILES[$this->field_id]['size'][$i];
                            $files[$i]['fileTmpName']  = $_FILES[$this->field_id]['tmp_name'][$i];
                            $files[$i]['fileType'] = $_FILES[$this->field_id]['type'][$i];
                            $tmp = explode( '.', $files[$i]['fileName'] );
                            $files[$i]['fileExtension'] = strtolower( end( $tmp ) );    // fixes "only varibles should be passed in reference" error
                            
                            // get upload path
                            $files[$i]['uploadPath'] = $this->upload_directory . '/' . basename( $files[$i]['fileName'] ); 

                            // file extension test
                            if ( ! in_array( $files[$i]['fileExtension'], $this->file_extensions ) ) {
                                $files[$i]['errors'][] = 'This file extension is not allowed. Please upload one of these types of files: ' . implode( ',', $this->file_extensions );
                            }

                            // file size test 
                            $upload_max_size = $php_upload_max_filesize * ( 1024 * 1024 ); 
                            if ( $files[$i]['fileSize'] > $upload_max_size ) {
                                $files[$i]['errors'][] = 'File exceeds maximum size ('.upload_max_size.'M)';
                            }     

                            // if no errors, then move from the temp folder to the destination folder 
                            if ( empty( $files[$i]['errors'] ) ) {

                                $didUpload = move_uploaded_file( $files[$i]['fileTmpName'], $files[$i]['uploadPath'] );

                                if ( $didUpload ) {                        
                                    $files[$i]['results'] = basename( $files[$i]['fileName'] ) . ' successfully uploaded';
                                } else {
                                    $files[$i]['results'] = 'An error occurred.';
                                }

                            // otherwise, state that error occurred.
                            } else {

                                $files[$i]['results'] = 'An error occurred.';

                            } // end : if no errors

                        } // end : for each file 

                    } else {

                        $files['error'] = 'Upload directory does not exist and could not be created.';

                    } // end : if upload directory exists

                } else {

                    $files['error'] = 'File upload field is empty.';

                } // end : if file upload field is populated


                // save the file data to the object variable and wordpress options for future reference
                if ( $files ) {
                    $this->save_files( $files );                    
                } 
                    
            } // end : upload_multiple_files()            


            // upload single file into the specified folder (the field is not set for multiple files)
            protected function upload_single_file() { 
                
                $file = array(); 

                // if file upload field is populated
                if ( 
                        isset( $_FILES[ $this->field_id ]['name'] ) &&
                        $_FILES[ $this->field_id ]['name'] != ''
                    ) {

                    // ------------------------------------------
                    // process the file upload
                    // ------------------------------------------  

                    // check if the upload directory exists. if not, attempt to create. check for issues.
                    // (no point in uploading files if directory does not exist and cannot be created.)
                    $upload_directory_exists = $this->maybe_create_upload_directory( $this->upload_directory, $this->upload_directory_permissions );
                    if ( $upload_directory_exists ) {

                        // prepare
                        $file['errors'] = array(); // Store errors here

                        // get file details
                        $file['fileName'] = $_FILES[$this->field_id]['name'];
                        $file['fileSize'] = $_FILES[$this->field_id]['size'];
                        $file['fileTmpName']  = $_FILES[$this->field_id]['tmp_name'];
                        $file['fileType'] = $_FILES[$this->field_id]['type'];
                        $tmp = explode( '.', $file['fileName'] );
                        $file['fileExtension'] = strtolower( end( $tmp ) );    // fixes "only varibles should be passed in reference" error
                        
                        // get upload path
                        $file['uploadPath'] = $this->upload_directory . '/' . basename( $file['fileName'] ); 

                        // file extension test
                        if ( ! in_array( $file['fileExtension'], $this->file_extensions ) ) {
                            $file['errors'][] = 'This file extension is not allowed. Please upload one of these types of files: ' . implode( ',', $this->file_extensions );
                        }

                        // file size test 
                        $php_upload_max_filesize = intval( ini_get('upload_max_filesize') );
                        $upload_max_size = $php_upload_max_filesize * ( 1024 * 1024 ); 
                        if ( $file['fileSize'] > $upload_max_size ) {
                            $file['errors'][] = 'File exceeds maximum size ('.upload_max_size.'M)';
                        }                

                        // if no errors, then move from temp folder to destination folder
                        if ( empty( $file['errors'] ) ) {

                            $didUpload = move_uploaded_file( $file['fileTmpName'], $file['uploadPath'] );

                            if ( $didUpload ) {                        
                                $file['results'] = basename( $file['fileName'] ) . ' successfully uploaded';
                            } else {
                                $file['results'] = 'An error occurred.';
                            }

                        // otherwise, state the error occurred.
                        } else {

                            $file['results'] = 'An error occurred.';

                        } // end : if no errors

                    } else { 
                        
                        $files['error'] = 'Upload directory does not exist and could not be created.';
                        
                    } // end : if upload directory exists

                } else {
                        
                    $file['error'] = 'File upload field is empty.';
                        
                } // end : if file upload field is populated


                // save the file data to the object variable and wordpress options for future reference
                if ( $file ) {
                    $this->save_files( $file );
                }
                
            } // end : upload_single_file()   
            

            // returns a report of how the file upload went
            public function get_upload_report() {

                $message = '';
                $has_errors = false;

                // if multiple file uploads are allowed 
                if ( $this->multiple === true ) {

                    if ( isset( $this->files['error'] ) ) {

                        $message = $this->files['error'];
                        $has_errors = true;

                    } else {

                        if ( empty( $this->files ) ) {

                            $message = 'No files uploaded.';
                            $has_errors = true;

                        } else {

                            foreach( $this->files as $key => $file ) {

                                // report errors 
                                if ( ! empty( $file['errors'] ) ) {
                                    $errors = '';
                                    foreach( $file['errors'] as $ekey => $error ) {
                                        if ( $ekey !== 0 ) {
                                            $errors .= '<br/>';
                                        }
                                        $errors .= '&nbsp;&nbsp;-&nbsp;' . $error;
                                    }
                                    if ( $key !== 0 ) {
                                        $message .= '<br/>';
                                    }
                                    $message .= $file['fileName'] . ' failed to upload. Error(s) : ' . '<br/>' . $errors;
                                    $has_errors = true;

                                // or report successful file upload                                         
                                } else {
                                    if ( $key !== 0 ) {
                                        $message .= '<br/>';
                                    }
                                    $message .= $file['results'];
                                }
                                
                            }

                        }

                    }

                // if only single file uploads are allowed 
                } else {

                    if ( isset( $this->files['error'] ) ) {

                        $message .= $this->files['error'];
                        $has_errors = true;

                    } else {

                        if ( empty( $this->files ) ) {

                            $message .= 'No files uploaded.';
                            $has_errors = true;

                        } else {

                            // report errors 
                            if ( ! empty( $this->files['errors'] ) ) {
                                $errors = '';
                                foreach( $this->files['errors'] as $ekey => $error ) {
                                    if ( $ekey !== 0 ) {
                                        $errors .= '<br/>';
                                    }
                                    $errors .= '&nbsp;&nbsp;-&nbsp;' . $error;
                                }
                                $message .= $this->files['fileName'] . ' failed to upload. Error(s) : ' . '<br/>' . $errors;
                                $has_errors = true;

                            // or report successful file upload                                         
                            } else {
                                $message .= $this->files['results'];
                            }

                        }

                    }

                } // end : if multiple vs single 

                // flag if there are errors
                if ( $has_errors ) {
                    $report = array(
                        'has_error' => 'true',
                        'message'   => $message,
                    );
                } else {
                    $report = array(
                        'has_error' => 'false',
                        'message'   => $message,
                    );
                }

                return $report;

            } // end : get_upload_report()



            // ---------------------------------------------------
            //  HANDLE THE UPLOAD DIRECTORY
            // ---------------------------------------------------

            /*
             * returns the wordpress upload directory with a folder name added to it
            **/
            public static function get_wordpress_upload_path( $folder_name ) {

                $dir = wp_get_upload_dir();
                $path = $dir['basedir'] . '/' . $folder_name;
                return $path;

            }

            /* 
             * checks if a directory exists, 
             * if not creates one
             * 
             * @params
             *      $upload_folder : a file path to the folder to be created 
             *      $permissions   : the permissions for the folder in question
             * 
             * @returns 
             *      (boolean) true if the folder exists, false if not exists
            **/
            protected function maybe_create_upload_directory( $upload_folder, $permissions = 0644 ) {

                // check if the directory does not already exist
                if ( ! file_exists( $upload_folder ) ) {

                    // try to create the upload directory 
                    // TODO: PERMISSIONS DID NOTHING                    
                    @mkdir( $upload_folder, $permissions );

                    // check if the creation was successful
                    if ( file_exists( $upload_folder ) ) {

                        // the directory was created
                        $upload_folder_exists = true;

                    // else (not successful)                        
                    } else {

                        // the directory could not be created
                        $upload_folder_exists = false;

                    }

                // else (directory already exists)                    
                } else { 

                    $upload_folder_exists = true;

                }

                return $upload_folder_exists;

            } // end : maybe_create_upload_directory()



            // ---------------------------------------------------
            //  ACCESS FILE DATA
            // ---------------------------------------------------

            protected function save_files( $files ) {

                if ( $files ) {

                    // remember the file data in this object for future reference 
                    $this->files = $files;

                    // save the file data to wordpress options
                    update_option( self::WP_OPTION_PREFIX . $this->field_id, $files );

                }
                
            }

            /* 
             * get file data from previous file upload ( see: $this->handle_form_submission() )
             * 
             * returns: file data (array) or 
             *          false (boolean) 
            **/
            public function get_files() {

                // if $this->files has not been set 
                if ( is_null( $this->files ) ) {

                    // try to get the option from wordpress
                    $files = get_option( self::WP_OPTION_PREFIX . $this->field_id );

                    // if a valid option was available
                    if ( $files !== false && $files !== '' ) {

                        // set $this->files (for future reference by this object)
                        $this->files = $files;

                    }

                }

                // if file data exists
                if ( ! is_null( $this->files ) ) {

                    $results = $this->files;

                } else {

                    $results = false;

                }

                return $results;

            } // end : get_files()



    } // end class: ITW_File_Upload

endif;
