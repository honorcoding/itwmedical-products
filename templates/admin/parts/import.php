<?php
$file_upload = $args['file_upload'];
$example_csv_url = ITW_MEDICAL_PRODUCTS_URL . 'assets/example.csv';

// TODO: make this look nice 
// TODO: fill the example.csv with demo data for an example 
?>
<p>
    Imports a CSV file with the necessary headings: <a href="<?php echo $example_csv_url; ?>">Download an example CSV file</a><br/>
    <ul>
    Notes: <br/>
        <li>If the "image" field is left blank, then it will do nothing. Also, "image" should be a full URL.</li>
        <li>Categories are listed by name and separated by commas. (e.g. Category 1, Category 3)</li>
    </ul>
</p>
<br />
<p>
    <?php 
    if ( $file_upload ) {
        echo $file_upload->get_form_html();
    }
    ?>
</p>

