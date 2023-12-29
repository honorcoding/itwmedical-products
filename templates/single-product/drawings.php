<?php 
    $content = nl2br( $product->product_drawings );
    if ( ! $content ) {
        $content = 'Drawings are not available for this product.';
    }
?>
<div class="itw-drawings">
    <?php echo $content; ?>
</div>    
