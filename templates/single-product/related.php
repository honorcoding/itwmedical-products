    <?php
        if ( ! empty( $related_products ) ) {

            ?>
            <div class="itw-related">
                <ul>
                <?php

                    // list out related products 
                    foreach( $related_products as $rel_prod ) {

                        // display the product 
                        $link = get_the_permalink( $rel_prod->post_id );
                        $title = esc_html( $rel_prod->title );
                        $image = wp_get_attachment_image( $rel_prod->image, 'thumbnail' );
                        ?>
                        <li>     
                            <a href="<?php echo $link; ?>"><?php echo $image ?></a> 
                            <a href="<?php echo $link; ?>"><span class="itw-title"><?php echo $title; ?></span></a> 
                        </li>
                        <?php

                    }

                ?>
                </ul>
            </div>    
            <?php

        }      
    ?>
