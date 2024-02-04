/*
 * ITW PRODUCT - ADMIN SCRIPTS 
*/

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
            ITW_Export_Data_to_CSV_File( response.data );       
        }
    });

}

/**
 * Automatically download data as file: products.csv 
 */
function ITW_Export_Data_to_CSV_File( data ) {
console.log( data );
// TODO: WHY DOESN'T THIS EXPORT AS A CSV DOCUMENT? WHY ALL THE EXTRA WP HTML? HOW TO GET AROUND THAT? 
//       ONCE WE GET AROUND THAT, THEN WHY DO I NEED ALL THIS AJAX, ETC? CAN'T I JUST ADD A FUNCTION TO ITW_CSV_FILE.PHP?
    var c = document.createElement("a");
    c.download = "products.csv";
    var t = new Blob([data], {
        type: "text/csv"
    });

    c.href = window.URL.createObjectURL(t);
    c.click();

}