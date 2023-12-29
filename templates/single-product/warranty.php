<?php 
    $content = nl2br( $product->warranty );
    if ( ! $content ) {
        $content = 'A warranty is not available for this product.';
    }
?>
<div class="itw-warranty">
    <?php echo $content; ?>
</div>    
