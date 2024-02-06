<?php 

use ITW_Medical\Wordpress\WP_Expanded as WPX;

$literature = false;
if ( isset( $product->technical_literature ) && $product->technical_literature !== '' ) {
    $literature = WPX::simple_explode( $product->technical_literature );
    if ( empty ( $literature ) ) {
        $literature = false;
    }
} 

?>
<div class="itw-technical">
    <?php 
        if ( $literature ) {

            foreach( $literature as $attachment_id ) {

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
                    Technical literature is not available for this product.
                </p>
            <?php
            
        }
    ?>
</div>    
