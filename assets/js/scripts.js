/*
 * ITW PRODUCT - SCRIPTS
*/

(function($) {
    $(document).ready(function() {


        /**
         * TEST FOR TABLET OR MOBILE
         */ 

        // check if tablet screen size (based on css rule, not inaccurate $(window).width()
        $('body').append('<div class="hc-tablet-check"></div>');
        function isTablet(){
            if ( $( '.hc-tablet-check' ).css("visibility") == "visible" ){
                return true;
            } else {
                return false;
            }
        }

        // check if mobile screen size (based on css rule, not inaccurate $(window).width()
        $('body').append('<div class="hc-mobile-check"></div>');
        function isMobile(){
            if ( $( '.hc-mobile-check' ).css("visibility") == "visible" ){
                return true;
            } else {
                return false;
            }
        }

        // on page load
        if ( isMobile() ) {
            $( '.itw-hide-mobile' ).hide();
            $( '.itw-show-mobile' ).show();
        } else {
            $( '.itw-hide-mobile' ).show();
            $( '.itw-show-mobile' ).hide();
        }
        // if window resizes
        $( window ).on( "resize", function() {
            if ( isMobile() ) {
                $( '.itw-hide-mobile' ).hide();
                $( '.itw-show-mobile' ).show();
            } else {
                $( '.itw-hide-mobile' ).show();
                $( '.itw-show-mobile' ).hide();
            }
        } );


        /**
         * Filter forms are submitted when category changes
         */        
        $('.itw-product-filter-form #itw_category').on('change', function() {
            var $form = $(this).closest('form');
            $form.submit();
        });
    

        /**
         * TABS 
         * 
         * HOW TO USE:
         *   see style.css
         *
        itw_hide_all_tabs_except_active_on_desktop();
        $(window).on('resize', function(){
            itw_hide_all_tabs_except_active_on_desktop();
        });

        function itw_hide_all_tabs_except_active_on_desktop() {
            if ( isMobile() ) {
                // if mobile, show all tabs
                $('.itw-tab').show();
            } else {
                // if not mobile, then hide all tabs except the active one
                $('.itw-tabs-button.active').each(function() {
                    var $parent = $(this).closest('.itw-tabs');
                    var tab = $(this).data('tab');
                    var $tab = $parent.find( '#' + tab );            
                    $parent.find('.itw-tab').hide();
                    $tab.show();
                });
            }
        }


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
        */

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


// ---------------------------------------------
// single product tabs 
// ---------------------------------------------

const tabs = document.querySelectorAll('[role="tab"]');
const panels = document.querySelectorAll('[role="tabpanel"]');

tabs.forEach(tab => {
  tab.addEventListener('click', () => itwActivateTab(tab));
  tab.addEventListener('keydown', e => {
    const index = [...tabs].indexOf(tab);

    if (e.key === 'ArrowRight') {
      const next = tabs[index + 1] || tabs[0];
      next.focus();
      itwActivateTab(next);
    }

    if (e.key === 'ArrowLeft') {
      const prev = tabs[index - 1] || tabs[tabs.length - 1];
      prev.focus();
      itwActivateTab(prev);
    }
  });
});

function itwActivateTab(tab) {
  // deactivate all tabs
  tabs.forEach(t => {
    t.setAttribute('aria-selected', 'false');
    t.setAttribute('tabindex', '-1');
    t.classList.remove('active');
  });

  // hide all panels
  panels.forEach(p => p.hidden = true);

  // activate selected tab
  tab.setAttribute('aria-selected', 'true');
  tab.setAttribute('tabindex', '0');
  tab.classList.add('active');

  // show associated panel
  const panel = document.getElementById(tab.getAttribute('aria-controls'));
  panel.hidden = false;
}
