<?php
/*
 * SINGLE MEDICAL PRODUCT TEMPLATE - CONTENT ONLY
 * 
 * NOTE: 
 *      The twentytwentythree theme is a block theme, so the actual template is created in Appearance > Editor. 
 *      That template handles the header/footer/sidebar. The shortcode [itw_medical_product_single_content] was 
 *      placed in that template to refer to this template file (via /client/class-itw-product-client-view.php).
 *      Only the content can be modified here. The Appearance > Editor tool handles the remainder of the page.
*/
?>
<div class="itw-product-single">

    <?php    
        echo do_shortcode('[itw_product view="header"]');
        echo do_shortcode('[itw_product view="order"]');
        ?>

        <p class="bottom-space"><a href="#">+ print friendly version</a></p>

        <?php
        echo do_shortcode('[itw_product view="tabs"]');
    ?>
    
</div>
