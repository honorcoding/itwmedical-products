<div class="itw-tabs">
    <div class="itw-tabs-bar itw-hide-mobile">
        <button class="itw-tabs-button active" data-tab="itw-product-single-Product-Details">Product Details</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Product-Drawings">Product Drawings</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Warranty">Warranty</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Technical-Literature">Technical Literature</button>
        <button class="itw-tabs-button" data-tab="itw-product-single-Related">Related Products</button>
    </div> 
    <div class="itw-tabs-section">

        <?php 
            $data = do_shortcode('[itw_product view="details"]');
            if ( $data !== '' ) {
                ?>
                    <div class="itw-tab-header itw-show-mobile">
                        Product Details 
                    </div>
                    <div id="itw-product-single-Product-Details" class="itw-tab">
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

        <?php 
            $data = do_shortcode('[itw_product view="drawings"]');
            if ( $data !== '' ) {
                ?>
                    <div class="itw-tab-header itw-show-mobile">
                        Product Drawings 
                    </div>
                    <div id="itw-product-single-Product-Drawings" class="itw-tab">
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

        <?php 
            $data = do_shortcode('[itw_product view="warranty"]');
            if ( $data !== '' ) {
                ?>
                    <div class="itw-tab-header itw-show-mobile">
                        Warranty
                    </div>
                    <div id="itw-product-single-Warranty" class="itw-tab">
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

        <?php 
            $data = do_shortcode('[itw_product view="technical"]');
            if ( $data !== '' ) {
                ?>
                    <div class="itw-tab-header itw-show-mobile">
                        Technical Literature
                    </div>
                    <div id="itw-product-single-Technical-Literature" class="itw-tab">
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

        <?php 
            $data = do_shortcode('[itw_product view="related"]');
            if ( $data !== '' ) {
                ?>
                    <div class="itw-tab-header itw-show-mobile">
                        Related Products 
                    </div>
                    <div id="itw-product-single-Related" class="itw-tab">
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

    </div>
</div>    
