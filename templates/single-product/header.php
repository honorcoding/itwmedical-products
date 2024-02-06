<div class="itw-header">
        <div class="itw-col">
            <h2>Medical Product</h2>
            <h1 class="itw-title"><?php echo $product->title; ?></h1>
            <div class="itw-long-description"><?php echo $product->long_description; ?></div>
        </div>
        <div class="itw-col">
            <div class="itw-image"><?php echo wp_get_attachment_image( $product->image, 'medium' ); ?></div>
        </div>
</div>