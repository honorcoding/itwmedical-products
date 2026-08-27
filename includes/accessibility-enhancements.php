<?php 
// =======================================
// Accessibility Enhancements for ITW Medical 
// =======================================


/**
 * adds aria to blocks that meet conditions 
 */
add_filter( 'render_block', 'itw_add_aria_to_blocks', 10, 2 ); 
function itw_add_aria_to_blocks( $block_content, $block ) {

    $conditions = [
        [
            'className' => 'capabilities-product-sidebar',
            'htmlElement' => 'aside',
            'aria' => 'aria-label="Capabilities and product categories"'
        ],
    ];

    foreach( $conditions as $condition ) {

        if (
            ! empty( $block['attrs']['className'] ) &&
            str_contains( $block['attrs']['className'], $condition['className'] )
        ) {
            $search = '/^<'.$condition['htmlElement'].'\b/';
            $replace = '<'.$condition['htmlElement'].' '.$condition['aria'];
            $block_content = preg_replace(
                $search,
                $replace,
                $block_content,
                1
            );
        }

    }    

    return $block_content;

}

