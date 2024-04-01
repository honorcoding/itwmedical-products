# ITWMEDICAL PRODUCTS 

ITWMedical Products is a Wordpress plugin that displays ITW medical products.
 
 
 
## Installation and Setup

Download and install the plugin. 

### The Product Listing Page 

#### 1. The Listing Page 

Create a page for listings (or choose an existing page). Add the following shortcodes to the page. 

```
[itw_product_filters parent="Standard Products"]
[itw_products]
```
 
#### 2. Settings 

In the left-sidebar, go to ITW Medical Products > Settings and scroll to the "Product List Page" section. Select the page with the above shortcodes. 

Note: Importing CSV files may not work with certain browsers. This is because some browsers do not support the javascript feature FormData. If it is not working on the browser of your choice, try Firefox, Edge, Chrome or Safari. 
 
 
### Individual Products 

#### 1. The page template 

The itwmedical.com website uses the twentytwentythree theme, which is a block theme. This means that the actual template is not found in the Wordpress files. Instead, the individual product page template was created in Appearance > Editor. That template handles the header/footer/sidebar. The shortcode [itw_medical_product_single_content] was placed in that template to refer to this template file (See: /templates/single-itw-product-content.php and /client/class-itw-product-client-view.php). Only the content can be modified here. The Appearance > Editor tool handles the remainder of the page.

Alternatively, it is possible to add a page template to a Wordpress theme the old fashioned way. Add a page named "page-itw-medical-product.php". (Simply copy the original page.php template). Then add the shortcode [itw_medical_product_single_content] where the content should go. 

#### 2. Permalinks 

In the left-sidebar, go to Settings > Permalinks. Then scroll to the bottom of the page and click "Save Changes". This will reset permlinks. Without this, navigating to the individual products will produce a 404 error (page not found).

#### 3. Warranty 

In the left sidebar, go to ITW Medical Products > Settings and scroll to the "Warranty" section. Enter the warranty text. This will display on all products. 
 
 
 
### Import Products and Categories

#### 1. Product Categories 

Import the product categories before importing the products. 

Install the plugin "WP Import Export Lite". Import the file /itwmedical-products/imports/categories.csv. Import as "Taxonomies | Categories | Tags" : itw-medical-product-category. Use the the name , description and (under "Other Category Options") use the parent slug and the category slug. 

(Note: if there are issues with parent-child categories on import, then rearrange the csv file to put all the parents categories first.)

#### 2. Products 

Import the product categories before importing the products. 

Import the product files from /itwmedical-products/imports/. You can import them all at once (which takes a long time and might cause the site to time out.) Or you can import them in steps. 
 
 
 
## Usage 

TODO: Add explanation here 
1. Adding / Editing individual products 
2. Importing / Exporting products 
