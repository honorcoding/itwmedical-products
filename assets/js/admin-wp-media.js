/*
 * ADMIN-WP-MEDIA.JS 
 * 
 * Handles WP Media interactions for Admin pages. 
 * 
*/

jQuery(document).ready( function($) {

    jQuery('.itw_wp_media_button').click(function(e) {

        e.preventDefault();
        var image_frame;
        if(image_frame){
            image_frame.open();
        }

        // get the input field and img tag ids
        var $wp_media_field = $(this).closest( '.itw_wp_media_field' );

        // Define image_frame as wp.media object
        image_frame = wp.media({
            title: 'Select Media',
            multiple : true,
            //library : {
            //    type : 'image',
            //}
        });

        image_frame.on('close',function() {

            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            var selection =  image_frame.state().get('selection');
            var gallery_ids = new Array();
            var i = 0;
            selection.each(function(attachment) {
                gallery_ids[i] = attachment['id'];
                i++;
            });
            var attachment_ids = gallery_ids.join(",");

            if ( $wp_media_field.length > 0 ) {
                var $input_field = $wp_media_field.find( '.itw_wp_media_text' ).first();
                var $attachments_section = $wp_media_field.find( '.itw_wp_media_images' ).first();
                if ( 
                    $input_field.length > 0 && 
                    $attachments_section.length > 0 
                ) {              
                    $input_field.val( attachment_ids );
                    Refresh_Image( attachment_ids, $attachments_section );
                }
            }
 
        });

        image_frame.on('open',function() {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            var selection =  image_frame.state().get('selection');
            var $input_field = $wp_media_field.find( '.itw_wp_media_text' ).first();
            if ( $input_field.length > 0 ) {
                var ids = $input_field.val().split(',');
                ids.forEach(function(id) {
                    var attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add( attachment ? [ attachment ] : [] );
                });
            }
        });

        image_frame.open();

   });

});

// Ajax request to refresh the image preview
function Refresh_Image( attachment_ids, $attachment_section ){

    var data = {
        action: 'itw_wp_media_get_image',
        attachment_ids: attachment_ids,
    };

    jQuery.get(ajaxurl, data, function(response) {
        if( response.success === true ) {           
            $attachment_section.replaceWith( response.data.html );
        }
    });

}