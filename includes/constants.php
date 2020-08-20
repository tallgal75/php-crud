<?php


/**

 * Constants.php

 *

 *

 */

/**

 * Database Constants - these constants are required

 * in order for there to be a successful connection

 * to the MySQL database. Make sure the information is

 * correct.

 */

 
/**

 * Database Table Constants - these constants

 * hold the names of all the database tables used

 * in the script.

 */

define("TBL_ADMIN_USERS", "users");
define("TBL_CUSTOMERS", "customers");
define("TBL_ORDERS", "orders");
define("TBL_ORDER_ITEMS", "order_items");
define("TBL_INVOICES", "invoices");
define("TBL_INVOICE_ITEMS", "invoice_items");
define("TBL_PRODUCTS", "products");
define("TBL_PRODUCT_SIZES", "users");
define("TBL_PRODUCT_CATEGORY", "product_category");
define("TBL_PRODUCT_COLOURS", "product_colours");



/**

 * Timeout Constants - these constants refer to

 * the maximum amount of time (in minutes) after

 * their last page fresh that a user and guest

 * are still considered active visitors.

 */

define("USER_TIMEOUT", 30);

define("GUEST_TIMEOUT", 5);



/**

 * Cookie Constants - these are the parameters

 * to the setcookie function call, change them

 * if necessary to fit your website. If you need

 * help, visit www.php.net for more info.

 * <http://www.php.net/manual/en/function.setcookie.php>

 */

define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default

define("COOKIE_PATH", "/");  //Avaible in whole domain



/**

 * Email Constants - these specify what goes in

 * the from field in the emails that the script

 * sends to users, and whether to send a

 * welcome email to newly registered users.

 */


	//define("EMAIL_FROM_NAME", "CANUK");
	//define("EMAIL_FROM_ADDR", "enquiries@canuk.org.uk");
    //define("EMAIL_WELCOME", false);

?>

