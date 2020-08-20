<?php
class MySQLDB
{
	var $connection; 
	var $email;
	var $password;
	var $host;
	var $dbname;
	var $dsn;
	var $options;
	
	//The MySQL database connection
	function MySQLDB()
	{
		//localhost
		if($_SERVER['REMOTE_ADDR']=="127.0.0.1" || $_SERVER['SERVER_NAME'] == "localhost")
		{
		  define("SITE_PATH", "http://".$_SERVER['SERVER_NAME']."/php_crud/");
			
  		  $this->host='localhost';
  		  $this->username = 'root';
  		  $this->password = 'T@lulop3'; //Replace this your local database password
  		  $this->dbname = 'php_crud'; 
  			
  		  /*$this->dsn = 'mysql:host=localhost;dbname=aiti';
  		  $this->options = array(
  			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
  		  ); 
  			
  		  $this->connection = new PDO($this->dsn, $this->username, $this->password, $this->options);
         */
         $this->connection = mysql_connect($this->host, $this->username, $this->password) or die(mysql_error());
         mysql_select_db($this->dbname, $this->connection) or die(mysql_error());
		}
		
		
	}
	
	/* confirmUserPass - Checks whether or not the given

    * email is in the database, if so it checks if the

    * given password is the same password in the database

    * for that user. If the user doesn't exist or if the

    * passwords don't match up, it returns an error code

    * (1 or 2). On success it returns 0.

    */

   function confirmUserPass($email, $password){

      /* Add slashes if necessary (for query) */

      if(!get_magic_quotes_gpc()) {

	      $email = addslashes($email);

      }



      /* Verify that user is in database */

      $q = "SELECT password FROM ".TBL_ADMIN_USERS." WHERE email_address = '$email'";
	  
      $result = $this->query($q);

      if(!$result || (mysql_numrows($result) < 1)){

         return 1; //Indicates email failure

      }



      /* Retrieve password from result, strip slashes */

     /* $dbarray = mysql_fetch_array($result);

      $dbarray['password'] = stripslashes($dbarray['password']);

      $password = stripslashes($password);*/

      /* Validate that password is correct */

      /*if($password == $dbarray['password']){

         return 0; //Success! email and password confirmed

      }

      else{

         return 2; //Indicates password failure

      }*/

   }

   

   /**

    * confirmUserID - Checks whether or not the given

    * email is in the database, if so it checks if the

    * given userid is the same userid in the database

    * for that user. If the user doesn't exist or if the

    * userids don't match up, it returns an error code

    * (1 or 2). On success it returns 0.

    */

   function confirmUserID($email, $userid){

      /* Add slashes if necessary (for query) */

      if(!get_magic_quotes_gpc()) {

	      $email = addslashes($email);

      }



      /* Verify that user is in database */

      $q = "SELECT user_id FROM ".TBL_ADMIN_USERS." WHERE email_address = '$email'";

      $result = $this->query($q);

      if(!$result || (mysql_numrows($result) < 1)){

         return 1; //Indicates email failure

      }



      /* Retrieve userid from result, strip slashes */

      $dbarray = mysql_fetch_array($result);

      $dbarray['userid'] = stripslashes($dbarray['user_id']);

      $userid = stripslashes($userid);



      /* Validate that userid is correct */

      if($userid == $dbarray['userid']){

         return 0; //Success! email and userid confirmed

      }

      else{

         return 2; //Indicates userid invalid

      }

   }

   
   function confirmAdminUserID($email, $userid){

      /* Add slashes if necessary (for query) */

      if(!get_magic_quotes_gpc()) {

	      $email = addslashes($email);

      }



      /* Verify that user is in database */

      $q = "SELECT user_id FROM ".TBL_ADMIN_USERS." WHERE email_address = '$email'";

      $result = $this->query($q);

      if(!$result || ($result->rowCount < 1)){

         return 1; //Indicates email failure

      }



      /* Retrieve userid from result, strip slashes */

      $dbarray = mysql_fetch_array($result);

      $dbarray['userid'] = stripslashes($dbarray['user_id']);

      $userid = stripslashes($userid);



      /* Validate that userid is correct */

      if($userid == $dbarray['userid']){

         return 0; //Success! email and userid confirmed

      }

      else{

         return 2; //Indicates userid invalid

      }

   }


   /**

    * emailTaken - Returns true if the email has

    * been taken by another user, false otherwise.

    */

   function emailTaken($email){

      if(!get_magic_quotes_gpc()){

         $email = addslashes($email);

      }

      $q = "SELECT email_address FROM ".TBL_ADMIN_USERS." WHERE email_address = '$email'";

      $result = $this->query($q);

      return ($result->rowCount > 0);

   }

   

   /**

    * emailBanned - Returns true if the email has

    * been banned by the administrator.

    */

   function emailBanned($email){

      if(!get_magic_quotes_gpc()){

         $email = addslashes($email);

      }

      $q = "SELECT email FROM ".TBL_BANNED_USERS." WHERE email = '$email'";

      $result = $this->query($q);

      return ($result->rowCount > 0);

   }


   /**

    * getUserInfo - Returns the result array from a mysql

    * query asking for all information stored regarding

    * the given email. If query fails, NULL is returned.

    */

   function getAdminUserInfo($email, $password){

      $q = "SELECT * FROM ".TBL_ADMIN_USERS." WHERE email_address = '$email' AND password = '$password'";
	  
      $result = $this->query($q);

      /* Error occurred, return given name by default */

      if(!$result || ($result->rowCount() < 1)){

         return NULL;

      }

      /* Return result array */

      $dbarray = $result->fetch(PDO::FETCH_ASSOC);

      return $dbarray;

   }

	function query($q)
	{
	   $conn = $this->connection;
	   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	   $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	   return $conn->query($q);
  }
   
};


/* Create database connection */
$database = new MySQLDB;

?>
