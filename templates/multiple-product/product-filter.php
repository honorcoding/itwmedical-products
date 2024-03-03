<div class="itw-product-filter-container">
    <div class="itw-product-filter">
        <form class="itw-product-filter-form" method="GET">

        <?php 
            // itw_category filter
            if ( ! empty( $options ) ) {

                $output = '<select id="itw_category" name="itw_category">';
                $current = $filter->get_query_var( 'itw_category' );

                foreach( $options as $value => $label ) {

                    $selected = ( $current !== '' && $value == $current ) ? ' selected' : '';
                    //$link = $filter->add_filter_params_to_url( array( 'itw_category' => $value ) );

                    $output .= '<option value="' . $value . '"' . $selected . '>';
                        $output .= $label;
                    $output .= '</option>';

                }

                $output .= '</select>';
                echo $output;

            } else {
                ?>
                <p>No categories available to filter.</p>
                <?php
            }
        ?>

        <?php 
            // submit button
            // NOTE: this is not required, because javascript automatically submits the forms 
            if ( ! empty( $options ) ) {
                ?>
                <input type="submit" value="Filter" />
                <?php 
            }
        ?>   

        </form>
    </div>
</div>