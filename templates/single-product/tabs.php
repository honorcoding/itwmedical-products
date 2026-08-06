<div class="itw-tabs">
    <div class="tabs" role="tablist">
        <button role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1" tabindex="0">
            Product Details
        </button>

        <button role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2" tabindex="-1">
            Product Drawings
        </button>

        <button role="tab" aria-selected="false" aria-controls="panel-3" id="tab-3" tabindex="-1">
            Warranty
        </button>

        <button role="tab" aria-selected="false" aria-controls="panel-4" id="tab-4" tabindex="-1">
            Technical Literature
        </button>

        <button role="tab" aria-selected="false" aria-controls="panel-5" id="tab-5" tabindex="-1">
            Related Products
        </button>
    </div>

    <?php 
    // PRODUCT DETAILS PANEL 
    $data = do_shortcode('[itw_product view="details"]');
    if ( $data !== '' ) {
        ?>
        <div id="panel-1" role="tabpanel"  aria-labelledby="tab-1">
            <h2>Product Details</h2>
            <?php 
            echo $data; 
            ?>
        </div>
        <?php
    }
    ?>

    <?php 
    // PRODUCT DRAWINGS PANEL 
    $data = do_shortcode('[itw_product view="drawings"]');
    if ( $data !== '' ) {
        ?>
        <div id="panel-2" role="tabpanel" aria-labelledby="tab-2" hidden>
            <h2>Product Drawings</h2>
            <?php 
            echo $data; 
            ?>
        </div>
        <?php
    }
    ?>

    <?php 
    // WARRANTY PANEL 
    $data = do_shortcode('[itw_product view="warranty"]');
    if ( $data !== '' ) {
        ?>
        <div id="panel-3" role="tabpanel" aria-labelledby="tab-3" hidden>
            <h2>Warranty</h2>
            <?php 
            echo $data; 
            ?>
        </div>
        <?php
    }
    ?>

    <?php 
    // TECHNICAL LITERATURE PANEL 
    $data = do_shortcode('[itw_product view="technical"]');
    if ( $data !== '' ) {
        ?>
        <div id="panel-4" role="tabpanel" aria-labelledby="tab-4" hidden>
            <h2>Technical Literature</h2>
            <?php 
            echo $data; 
            ?>
        </div>
        <?php
    }
    ?>

    <?php 
    // RELATED PRODUCTS PANEL 
    $data = do_shortcode('[itw_product view="related"]');
    if ( $data !== '' ) {
        ?>
        <div id="panel-5" role="tabpanel" aria-labelledby="tab-5" hidden>
            <h2>Related Products</h2>
            <?php 
            echo $data; 
            ?>
        </div>
        <?php
    }
    ?>

</div><!-- end: tabs -->  
