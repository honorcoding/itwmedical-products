<?php 
    $content = nl2br( $product->related_products );
    if ( ! $content ) {
        $content = 'Related products are not available for this product.';
    }
?>
<div class="itw-related">
    <?php echo $content; ?>
</div>    
