<?php 
$warranty = nl2br( itw_prod()->get_warranty() );

if ( $warranty && $warranty !== '' ) {
    ?>
        <div class="itw-warranty">
            <?php 
                //echo $content; 
            ?>
            <h2>Product Warranty</h2>

            <?php echo $warranty; ?>
        </div>    
    <?php
}