<div class="itw-product-list-container">
    <div class="itw-product-list">
        <?php
            if ( ! empty( $products ) ) {
                ?>
                    <table>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Product No.</th>
                            <th>Mfg. No.</th>
                            <th>Product Name</th>
                            <th>Description</th>
                        </tr>
                        <?php 
                            foreach ( $products as $product ) {

                            $link = get_permalink( $product->post_id );                                
                            ?>
                                <tr>
                                    <td><a href="<?php echo $link; ?>">View</a></td>
                                    <td><?php echo $product->product_number; ?></td>
                                    <td><?php echo $product->mfg_number; ?></td>
                                    <td><?php echo $product->title; ?></td>
                                    <td><?php echo $product->short_description; ?></td>
                                </tr>
                            <?php
                            }
                        ?>
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