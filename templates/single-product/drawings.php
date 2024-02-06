<?php 

use ITW_Medical\Wordpress\WP_Expanded as WPX;

$drawings = false;
if ( isset( $product->product_drawings ) && $product->product_drawings !== '' ) {
    $drawings = WPX::simple_explode( $product->product_drawings );
    if ( empty ( $drawings ) ) {
        $drawings = false;
    }
} 

?>
<div class="itw-drawings">
    <?php 
        if ( $drawings ) {

            foreach( $drawings as $attachment_id ) {

                $url = WPX::get_filename_from_attachment_id( $attachment_id ); 
                $filename = basename( $url );
                $thumbnail = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                if ( $url && $thumbnail !== '' ) {
                    ?>
                        <div class="itw-drawings-container">
                            <div class="download_on_click" data-url="<?php echo $url ?>" data-filename="<?php echo $filename; ?>">
                                <?php echo $thumbnail ?>
                            </div>
                        </div>
                    <?php
                }
               
            }
        
        } else {

            ?>
                <p>
                    Drawings are not available for this product.
                </p>
            <?php

        }
    ?>
</div>    
