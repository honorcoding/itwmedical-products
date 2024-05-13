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
                    <div id="itw-product-single-Product-Details" class="itw-tab">
                        <div class="itw-tab-header itw-show-mobile">
                            Product Details 
                        </div>
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
                    <div id="itw-product-single-Product-Drawings" class="itw-tab">
                        <div class="itw-tab-header itw-show-mobile">
                            Product Drawings 
                        </div>
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
                    <div id="itw-product-single-Warranty" class="itw-tab">
                        <div class="itw-tab-header itw-show-mobile">
                            Warranty
                        </div>
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
                    <div id="itw-product-single-Technical-Literature" class="itw-tab">
                        <div class="itw-tab-header itw-show-mobile">
                            Technical Literature
                        </div>
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
                    <div id="itw-product-single-Related" class="itw-tab">
                        <div class="itw-tab-header itw-show-mobile">
                            Related Products 
                        </div>
                        <?php 
                            echo $data; 
                        ?>
                    </div>
                <?php
            }
        ?>

    </div>
</div>    
