/*
 * ITW PRODUCT - SCRIPTS
*/

(function($) {
    $(document).ready(function() {
    
        /*
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

    });
})(jQuery)
