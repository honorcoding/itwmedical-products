<div class="itw-header">
        <div class="itw-col">
            <?php 
                if ( $category_html !== '' ) {
                    ?>
                    <div class="category-meta">
                        <span class="category-label">Category:&nbsp;</span>
                        <span class="category-name"><?php echo $category_html; ?></span>
                    </div>
                    <?php 
                } 
            ?>
            <h1 class="itw-title"><?php echo esc_html( $product->title ); ?></h1>
            <div class="itw-long-description"><?php echo $product->long_description; ?></div>
        </div>
        <div class="itw-col">
            <div class="itw-image"><?php echo wp_get_attachment_image( $product->image, 'medium' ); ?></div>
        </div>
</div>