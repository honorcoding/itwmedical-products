<div class="itw-header">
        <div class="itw-col">
            <h2><?php echo $categories_imploded; ?></h2>
            <h1 class="itw-title"><?php echo $product->title; ?></h1>
            <div class="itw-long-description"><?php echo $product->long_description; ?></div>
            <?php echo do_shortcode('[itw_product view="order"]'); ?>
        </div>
        <div class="itw-col">
            <div class="itw-image"><?php echo $product->image; ?></div>
        </div>
</div>