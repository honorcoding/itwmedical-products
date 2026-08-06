<div class="itw-details">

    <?php if ( isset( $product->product_details_materials_of_construction ) && $product->product_details_materials_of_construction !== '' ) { ?>
        <h3>Materials of Construction</h3>
        <p>
            <?php echo nl2br( $product->product_details_materials_of_construction ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_connections ) && $product->product_details_connections !== '' ) { ?>
        <h3>Connections</h3>
        <p>
            <?php echo nl2br( $product->product_details_connections ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_design ) && $product->product_details_design !== '' ) { ?>
        <h3>Design</h3>
        <p>
            <?php echo nl2br( $product->product_details_design ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_performance_data ) && $product->product_details_performance_data !== '' ) { ?>
        <h3>Performance Data</h3>
        <p>
            <?php echo nl2br( $product->product_details_performance_data ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_packaging ) && $product->product_details_packaging !== '' ) { ?>
        <h3>Packaging</h3>
        <p>
            <?php echo nl2br( $product->product_details_packaging ); ?>
        </p>
    <?php } ?>

</div>    
