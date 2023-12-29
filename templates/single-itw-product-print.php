<?php
/*
 * SINGLE MEDICAL PRODUCT - PRINT TEMPLATE 
*/

$css_filetime = filemtime( ITW_MEDICAL_PRODUCTS_PATH . 'assets/style.css' );
$css_file = ITW_MEDICAL_PRODUCTS_URL . 'assets/style.css?ver=' . $css_filetime;

?>
<!DOCTYPE html>
<html lang="en-US">
<head> 
    <meta charset="UTF-8" />
    <link type="text/css" rel="stylesheet" href="<?php echo $css_file; ?>">
</head>
<body>
    <div class="itw-product-single print">

        <div class="itwmedical-brand">
            <div class="itwmedical-logo">
                <!-- brand logo goes here -->
            </div>
            <div class="itwmedical-actions">
                <a href="javascript:;" onClick="javascript:window.print();"><span class="plus">+</span> print</a><br />
                <a href="javascript:;" onClick="javascript:window.close();"><span class="plus">+</span> close</a>
            </div>
        </div>

        <?php    
            echo do_shortcode('[itw_product view="header-print"]');            
        ?>

        <div class="itw-tabs-section-print">

            <h2 class="tab">Product Details</h2><br/>
            <?php echo do_shortcode('[itw_product view="details"]'); ?><br/><br/>

            <h2 class="tab">Product Drawings</h2><br/>
            <?php echo do_shortcode('[itw_product view="drawings"]'); ?><br/><br/>

            <h2 class="tab">Warranty</h2><br/>
            <?php echo do_shortcode('[itw_product view="warranty"]'); ?><br/><br/>

            <h2 class="tab">Technical Literature</h2><br/>
            <?php echo do_shortcode('[itw_product view="technical"]'); ?><br/><br/>

            <h2 class="tab">Related Products</h2><br/>
            <?php echo do_shortcode('[itw_product view="related"]'); ?><br/><br/>

        </div>

        <?php    
            echo do_shortcode('[itw_product view="footer-print"]');            
        ?>
        
    </div>
</body>
</html>