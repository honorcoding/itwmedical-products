<div class="itw-ordering-info">
    <div class="itw-order-border">
            <span>Ordering Information</span>
    </div>
    <div class="row">
        <div class="column">
            <div class="box-item fixed-1 shaded">PRODUCT NO.</div>
            <div class="box-item fixed-1 shaded">MFG NO.</div>
            <div class="box-item fixed-1 shaded">DESCRIPTION</div>
        </div>
        <div class="column">
            <div class="box-item"><?php echo esc_html( $product->product_number ); ?></div>
            <div class="box-item"><?php echo esc_html( $product->mfg_number ); ?></div>
            <div class="box-item"><?php echo esc_html( $product->short_description ); ?></div>
        </div>
    </div>
</div>    


