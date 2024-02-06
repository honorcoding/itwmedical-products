<div class="itw-details">

    <?php if ( isset( $product->product_details_materials_of_construction ) && $product->product_details_materials_of_construction !== '' ) { ?>
        <h2>Materials of Construction</h2>
        <p>
            <?php echo nl2br( $product->product_details_materials_of_construction ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_connections ) && $product->product_details_connections !== '' ) { ?>
        <h2>Connections</h2>
        <p>
            <?php echo nl2br( $product->product_details_connections ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_design ) && $product->product_details_design !== '' ) { ?>
        <h2>Design</h2>
        <p>
            <?php echo nl2br( $product->product_details_design ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_performance_data ) && $product->product_details_performance_data !== '' ) { ?>
        <h2>Performance Data</h2>
        <p>
            <?php echo nl2br( $product->product_details_performance_data ); ?>
        </p>
    <?php } ?>

    <?php if ( isset( $product->product_details_packaging ) && $product->product_details_packaging !== '' ) { ?>
        <h2>Packaging</h2>
        <p>
            <?php echo nl2br( $product->product_details_packaging ); ?>
        </p>
    <?php } ?>

</div>    
