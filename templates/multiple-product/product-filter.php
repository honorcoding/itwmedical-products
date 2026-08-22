<div class="itw-product-filter-container">
    <div class="itw-product-filter">
        <form class="itw-product-filter-form" method="GET" aria-label="Product filter">

            <fieldset>
                <legend>Filter products</legend>        
                <?php 
                // itw_category filter
                if ( ! empty( $options ) ) {

                    $output = '<div class="itw-form-field">';

                        $output .= '<label for="itw_category">Category</label>';
                        $output .= '<select id="itw_category" name="itw_category">';

                            // select options 
                            $current = $filter->get_query_var( 'itw_category' );

                            foreach( $options as $value => $label ) {

                                $selected = ( $current !== '' && $value == $current ) ? ' selected' : '';
                                //$link = $filter->add_filter_params_to_url( array( 'itw_category' => $value ) );

                                $output .= '<option value="' . $value . '"' . $selected . '>';
                                    $output .= $label;
                                $output .= '</option>';

                            }

                        $output .= '</select>';
                        $output .= '<input type="submit" value="Filter" />';

                    $output .= '</div>';
                    echo $output;

                }
                ?>
            </fieldset>
        </form>
    </div>
</div>