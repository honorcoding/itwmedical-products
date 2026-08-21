<?php 
// =========================================
// client view resources
// =========================================


/**
 * modify yoast breadcrumb output from <span> tags to <ul>/<li> format 
 * 
 * note: the breadcrumb output originally is composed of <span> tags. 
 * example: 
 *     <span>
 *          <span><a href="http://localhost/itwmedical.com/">Home</a></span> / <span class="breadcrumb_last" aria-current="page">About</span>
 *     </span>
 * 
 * modified: 
 *     <ul class="breadcrumb">
 *         <li><a href="http://localhost/itwmedical.com/">Home</a></li>
 *         <li class="breadcrumb_last" aria-current="page">About</li>
 *     </ul>
 *
 * ideally: 
 *     encapsulate with: <nav aria-label="Breadcrumb">...</nav>
 *  
 * @return {string} : the revised output, or the original output on error 
 */
function itw_yoast_breadcrumb_output( $output ) {

    // prepare for string editing 
    $revised = trim( $output );

    // trim the first <span> 
    $first_span = strpos( $revised, '<span>' );              
    if ( $first_span === false ) { 
        return $output;
    }
    $revised = substr( $revised, strlen( '<span>' ) );    

    // trim last </span> 
    $first_span_close = strrpos( $revised, '</span>' );      
    if ( $first_span_close === false ) {
        return $output;
    }
    $revised = substr( $revised, 0, $first_span_close );

    // convert into unordered list  
    $revised = str_replace( ' / ', '', $revised );
    $revised = str_replace( 'span', 'li', $revised );
    $revised = '<ul class="breadcrumb">' . $revised . '</ul>';

wp_die( htmlentities( debugger()->dump( $revised)));    
    return $revised;

}
add_filter( 'wpseo_breadcrumb_output', 'itw_yoast_breadcrumb_output' );

