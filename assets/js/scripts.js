/*
 * ITW PRODUCT - SCRIPTS
*/

(function($) {
    $(document).ready(function() {
    
        /**
         * TABS 
         * 
         * HOW TO USE:
         *   see style.css
         */
        $('.itw-tabs-button').on( "click", function() {

            // if clicked on active tab, then do nothing 
            if ( $(this).hasClass('active') ) {
                return;
            }

            // otherwise, 
            // open that tab
            var $parent = $(this).closest('.itw-tabs');
            var tab = $(this).data('tab');
            var $tab = $parent.find( '#' + tab );            
            $parent.find('.itw-tab').hide();
            $tab.show();

            // and change the active tab 
            $parent.find('.itw-tabs-button').removeClass('active');
            $(this).addClass('active');

        });


        /**
         * Example:
         * <div class="download_on_click" data-url="https://mysite.com/downloadable.jpg" filename="downloadable.jpg">Click Me</div>
         */
        $('.download_on_click').on("click",function() {

            // get the image_url
            url = $(this).data('url');
            filename = $(this).data('filename');

            download_on_click( url, filename );

        });

    });
})(jQuery)


// ----------------------------------------------
// download on click 
// ----------------------------------------------

async function download_on_click( imageSrc, nameOfDownload ) {

    const response = await fetch(imageSrc);

    const blobImage = await response.blob();

    const href = URL.createObjectURL(blobImage);

    const anchorElement = document.createElement('a');
    anchorElement.href = href;
    anchorElement.download = nameOfDownload;

    document.body.appendChild(anchorElement);
    anchorElement.click();

    document.body.removeChild(anchorElement);
    window.URL.revokeObjectURL(href);

}

