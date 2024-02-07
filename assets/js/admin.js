/*
 * ITW PRODUCT - ADMIN SCRIPTS 
*/

// -----------------------------------------------------
// IMPORT PRODUCT DATA 
// -----------------------------------------------------

/*
jQuery(document).ready(function() {
    jQuery( '#' + itw.import_form_id ).submit(function (event) {

    alert( 'submitting form: ' + itw.import_form_id );

    /*
    var formData = {
      name: $("#name").val(),
      email: $("#email").val(),
      superheroAlias: $("#superheroAlias").val(),
    };

    $.ajax({
      type: "POST",
      url: "process.php",
      data: formData,
      dataType: "json",
      encode: true,
    }).done(function (data) {
      console.log(data);
    });
    * /

    event.preventDefault();
  });    
});
*/

function itw_import_product_data() {

    alert( itw.import_form_id );

}


// -----------------------------------------------------
// EXPORT PRODUCT DATA 
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