<div class="itw-table-outer-container">
    <div class="itw-table-inner-container">
        <?php

            if ( $filter_term !== '' ) {
                $caption = 'List of ' . $filter_term;
            } else { 
                $caption = 'List of Products';
            }

            if ( $term_description !== '' ) {
                ?>
                    <div class="itw-term-description">
                        <?php echo $term_description; ?>
                    </div>
                <?php
            }


            if ( ! empty( $products ) ) {
                ?>
                    <table class="itw-table">
                        <caption><?php echo $caption; ?></caption>
                        <thead>
                            <tr class="itw-row itw-header-row">
                                <th scope="col" class="itw-col">Product Name</th>
                                <th scope="col" class="itw-col">Product No.</th>
                                <th scope="col" class="itw-col">Mfg. No.</th>
                                <th scope="col" class="itw-col">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach ( $products as $product ) {

                                $link = get_permalink( $product->post_id );                                
                                ?>
                                    <?php /* <a href="<?php echo $link; ?>" class="itw-row"> */ ?>
                                    <tr class="itw-row">
                                        <th scope="row" class="itw-col"><a href="<?php echo $link; ?>" aria-label="<?php echo $product->title . ' - ' . $product->product_number; ?>"><?php echo $product->title; ?></a></th>
                                        <td class="itw-col"><?php echo $product->product_number; ?></a></td>
                                        <td class="itw-col"><?php echo $product->mfg_number; ?></a></td>
                                        <td class="itw-col"><?php echo $product->accessibility_description; ?></a></td>
                                    </tr>
                                <?php

                                }
                            ?>
                        </tbody>
                    </table>
                <?php
            } else {
                ?>
                    <p>No products.</p>
                <?php
            }
        ?>
    </div>
</div>