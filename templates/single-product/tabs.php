<div class="itw-tabs">
    <div class="itw-tabs-bar">
        <button class="itw-tabs-button active" data-tab="itw-product-single-Product-Details">Product Details</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Product-Drawings">Product Drawings</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Warranty">Warranty</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Technical-Literature">Technical Literature</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Related">Related Products</button>
    </div> 
    <div class="itw-tabs-section">
        <div id="itw-product-single-Product-Details" class="itw-tab">
            <?php 
                echo do_shortcode('[itw_product view="details"]');
            ?>
        </div>

        <div id="itw-product-single-Product-Drawings" class="itw-tab" style="display:none">
            <?php 
                echo do_shortcode('[itw_product view="drawings"]');
            ?>
        </div>

        <div id="itw-product-single-Warranty" class="itw-tab" style="display:none">
            <?php 
                echo do_shortcode('[itw_product view="warranty"]');
            ?>
        </div>    

        <div id="itw-product-single-Technical-Literature" class="itw-tab" style="display:none">
            <?php 
                echo do_shortcode('[itw_product view="technical"]');
            ?>
        </div>    

        <div id="itw-product-single-Related" class="itw-tab" style="display:none">
            <?php 
                echo do_shortcode('[itw_product view="related"]');
            ?>
        </div>    
        
    </div>
</div>    
