<?php

// get current product list page 
$product_list_page_id = $args['product_list_page_id'];

// get a list of all pages 
$pages = $args['all_pages'];

?>
<form class="itwmp-product-list-page-form" method="post">
    <p>
        <label for="itwmp-plp-field">Product List Page</label>
        <select id="itwmp-plp-field" name="itwmp-plp-field" class="itwmp-plp-field">
            <?php   
                foreach( $pages as $page ) {
                    if ( isset( $page->ID ) && isset( $page->post_title ) ) {
                        $selected = ( intval( $page->ID ) === intval( $product_list_page_id ) ) ? ' selected' : '';
                        ?>
                            <option value="<?php echo $page->ID; ?>"<?php echo $selected; ?>><?php echo $page->post_title; ?></option>
                        <?php
                    }
                }
            ?>
        </select>        
    </p>
    <p>
        <ul>
        Note: <br/>
            <li>
                Add the following shortcodes to this page:<br/>
                [itw_product_filters parent="Standard Products"]<br/>
                [itw_products]<br/>
            </li>
        </ul>
    </p>
    <br />
    <p>        
        <input id="itwmp-plp-submit" name="itwmp-plp-submit" type="submit" value="Update" />
    </p>
</form>