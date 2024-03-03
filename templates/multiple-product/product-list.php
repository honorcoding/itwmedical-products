<div class="itw-product-list-container">
    <div class="itw-product-list">
        <?php

            if ( $term_description !== '' ) {
                ?>
                    <div class="itw-term-description">
                        <?php echo $term_description; ?>
                    </div>
                <?php
            }

            if ( ! empty( $products ) ) {
                ?>
                    <div class="itw-table">
                        <div class="itw-row itw-header-row">
                            <div class="itw-col">Product Name</div>
                            <div class="itw-col">Product No.</div>
                            <div class="itw-col">Mfg. No.</div>
                            <div class="itw-col">Description</div>
                        </div>
                        <?php 
                            foreach ( $products as $product ) {

                            $link = get_permalink( $product->post_id );                                
                            ?>
                                <a href="<?php echo $link; ?>" class="itw-row">
                                    <div class="itw-col"><?php echo $product->title; ?></div>
                                    <div class="itw-col"><?php echo $product->product_number; ?></div>
                                    <div class="itw-col"><?php echo $product->mfg_number; ?></div>
                                    <div class="itw-col"><?php echo $product->short_description; ?></div>
                                </a>
                            <?php

                            }
                        ?>
                    </div>
                <?php
            } else {
                ?>
                    <p>No products.</p>
                <?php
            }
        ?>
    </div>
</div>