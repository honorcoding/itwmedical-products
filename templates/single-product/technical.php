<?php 
    $content = nl2br( $product->technical_literature );
    if ( ! $content ) {
        $content = 'Technical documents are not available for this product.';
    }
?>
<div class="itw-technical">
    <?php echo $content; ?>
</div>    
