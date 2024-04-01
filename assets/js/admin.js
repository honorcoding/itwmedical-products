/*
 * ITW PRODUCT - ADMIN SCRIPTS 
*/

// -----------------------------------------------------
// IMPORT PRODUCT DATA - SETTINGS PAGE
// -----------------------------------------------------

jQuery(document).ready(function() {

    /**  
    * "submit" event on file import form
    */
    jQuery( '#' + itw.import_form_id ).submit(function (event) {

        // do not process this form via PHP. process here (in Javascript).
        event.preventDefault(); 

        // clear the progress view 
        itw_clear_import_progress();

        // begin the import process 
        itw_upload_import_file( this );

    });    

});


/**  
 * upload the file via ajax. on success, import products.
 */
function itw_upload_import_file( form ) {

    var files = jQuery( '#' + itw.import_field_id ).val();
    if ( files !== '' ) {  // if file upload field is not empty

        itw_show_import_progress( 'UPLOADING FILE...' );

        var formData = new FormData( form );
        
        jQuery.ajax({
            url : itw.ajax_url + '?action=itw_upload_import_file',  // wordpress ajax hook 
            type : 'POST',
            data : formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success : function(response) {
                if( response.success === true ) {  

                    // file upload progress 
                    itw_show_import_progress( ' - File successfully uploaded.' );
                    
                    // begin product import                  
                    itw_get_csv_data_from_import_file();            

                } else {

                    // file upload error
                    itw_show_import_progress( ' - File could not be uploaded.<br/> - Error: ' + response.data.error );

                }
            }, 
            error : function(e) {
                // file upload error
                itw_show_import_progress( ' - File could not be uploaded.<br/> - Javascript error. (see Console).' );
            }
        });

    } else { 

        itw_show_import_progress( 'Please select a file.' );

    }// end : if file upload field is not empty

} // end : iwt_upload_import_file()


/**  
 * gets a list of all products, then imports each (via itw_import_product() )
 */
function itw_get_csv_data_from_import_file() {

    // update progress 
    itw_show_import_progress( '<br/>EXTRACTING PRODUCT DATA...' );

    jQuery.ajax({
        url : itw.ajax_url + '?action=itw_get_csv_data_from_import_file',  // wordpress ajax hook 
        type : 'POST',
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success : function(response) {
            if( response.success === true ) {  

                // file upload progress 
                itw_show_import_progress( ' - Data successfully extracted from import file.' );
                
                // begin product import       
                itw_show_import_progress( '<br/>IMPORTING PRODUCTS...' );
                itw_import_products_in_batches();            

            } else {

                // file upload error
                itw_show_import_progress( ' - Data could not be extracted.<br/> - Error: ' + response.data.error );

            }
        }, 
        error : function(e) {
            // file upload error
            itw_show_import_progress( ' - Data could not be extracted.<br/> - Javascript Error. (see Console).' );
        }
    });

    
} // end : itw_import_products()


/**  
 * imports a single product (via ajax) and displays progress 
 *
 * @param (integer) product_id 
 */
function itw_import_products_in_batches( current_batch = 0 ) {

    var data = {
        action: 'itw_import_products_in_batches',
        current_batch: current_batch
    }

    jQuery.ajax({
        url : itw.ajax_url,
        type : 'POST',
        data : data,
        success : function(response) {
            if( response.success === true ) {                      

                var batch = response.data;

                if ( 
                    typeof batch.current_batch !== 'undefined' && 
                    typeof batch.next_batch !== 'undefined' && 
                    typeof batch.total_batches !== 'undefined' && 
                    typeof batch.results !== 'undefined' 
                ) {

                    // display import progress 
                    var batch_message = '';
                    if ( batch.current_batch > 0 ) { 
                        batch_message = '<br/>';
                    }
                    batch_message = 'BATCH: ' + ( Number(batch.current_batch) + 1 ) + ' of ' + batch.total_batches;

                    itw_show_import_progress( batch_message );                    
                    itw_show_import_progress( batch.results );

                    // recursive call to import the next batch 
                    if ( batch.next_batch !== 'END' ) {
                        itw_show_import_progress( '' );
                        itw_import_products_in_batches( batch.next_batch );
                    }

                } else { 

                    // file upload error
                    itw_show_import_progress( 'BATCH: Error importing batch. Ajax response does not contain data.<br/>' );

                }
                

                // begin product import                  
                //itw_import_products_in_batches( next_batch );            

            } else {

                // file upload error
                itw_show_import_progress( ' - Batch could not be imported.<br/> - Error: ' + response.data.error );

            }
        }, 
        error : function(e) {
            // file upload error
            itw_show_import_progress( ' - Batch could not be imported.<br/> - Javascript error. (see Console).' );
        }
    });

} // end : itw_import_product()


/**  
 * displays the file import progress 
 *
 * @param (string) update - the message to add to the progress view
 */
function itw_show_import_progress( update ) {

    var has_content = jQuery( '#itw_import_results' ).hasClass( 'has_content' );
    if ( ! has_content ) {
        jQuery( '#itw_import_results' ).addClass( 'has_content' );
    }

    var so_far = jQuery( '#itw_import_results' ).html();
    var progress = '';
    if ( so_far !== '' ) {
        progress = so_far + '<br/>' + update;
    } else {
        progress = update;
    }
    jQuery( '#itw_import_results' ).html( progress );

}


/**
 * clears the file import progress 
 */
function itw_clear_import_progress() {
    jQuery( '#itw_import_results' ).html('');
    jQuery( '#itw_import_results' ).removeClass( 'has_content' );
}



// -----------------------------------------------------
// EXPORT PRODUCT DATA - SETTINGS PAGE 
// -----------------------------------------------------

/**
 * Initiate the process to download products as CSV data
 */ 
function itw_download_export_file() {

    ITW_Get_CSV_Data();

}

/**
 * Ajax request to get csv data 
 */ 
function ITW_Get_CSV_Data(){

    var data = {
        action: 'itw_get_product_csv_data',
    };

    jQuery.get(ajaxurl, data, function(response) {
        if( response.success === true ) {    
            ITW_Export_Data_to_CSV_File( response.data, 'all_products.csv' );       
        }
    });

}

/**
 * Automatically download data as csv file
 */
function ITW_Export_Data_to_CSV_File( data, filename ) {
    
    var c = document.createElement("a");
    c.download = filename;
    var t = new Blob([data.products], {
        type: "text/csv"
    });

    c.href = window.URL.createObjectURL(t);
    c.click();

}