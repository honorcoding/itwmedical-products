<?php
$file_upload = $args['file_upload'];
$example_csv_url = ITW_MEDICAL_PRODUCTS_URL . 'assets/example.csv';

?>
<p>
    Imports a CSV file with the necessary headings: <a href="<?php echo $example_csv_url; ?>">Download an example CSV file</a><br/>
    <ul>
    Notes: <br/>
        <li>If the "image" field is left blank, then it will do nothing. Also, "image" should be a full URL.</li>
        <li>Categories are listed by name and separated by commas. (e.g. Category 1, Category 3)</li>
        <li>Importing CSV files only works with certain browsers. If it is not working on the browser of your choice, try using Firefox, Edge, Chrome or Safari. </li>
    </ul>
</p>
<br />
<p>
    <?php 
    if ( $file_upload ) {
        echo $file_upload->get_form_html();
    }
    ?>
    <div id="itw_import_results"></div>
</p>

