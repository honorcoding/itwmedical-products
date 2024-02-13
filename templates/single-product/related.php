<div class="itw-related">
    <?php
  
        if ( ! empty( $related_products ) ) {

            ?>
            <ul>
            <?php

                // list out related products 
                foreach( $related_products as $rel_prod ) {

                    // display the product 
                    $link = get_the_permalink( $rel_prod->post_id );
                    $title = $rel_prod->title;
                    $image = wp_get_attachment_image( $rel_prod->image, 'thumbnail' );
                    ?>
                    <li>     
                        <a href="<?php echo get_the_permalink( $rel_prod->post_id ); ?>"><?php echo wp_get_attachment_image( $rel_prod->image, 'thumbnail' ); ?></a> 
                        <a href="<?php echo get_the_permalink( $rel_prod->post_id ); ?>"><span class="itw-title"><?php echo $rel_prod->title; ?></span></a> 
                    </li>
                    <?php

                }

            ?>
            </ul>
            <?php

        }
      
    ?>
</div>    
