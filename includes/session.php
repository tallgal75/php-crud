<?php
ob_start(); 
//stop caching
header("Pragma: no-cache");
header("Cache: no-cache");

date_default_timezone_set ('Europe/London');
ini_set('display_errors', true);
error_reporting (E_ALL);

include("constants.php");
include("connection.php");
include("form.php");

if($_SERVER['REMOTE_ADDR']=="127.0.0.1" || $_SERVER['SERVER_NAME'] == "localhost")
{
  ini_set("SMTP","smtp.blueyonder.co.uk");
  ini_set("smtp_port","25");
}
/*elseif($_SERVER['REMOTE_ADDR']=="obanya.co.uk" || $_SERVER['SERVER_NAME'] == "www.obanya.co.uk")
{
  ini_set("SMTP","webmail.obanya.co.uk");
  ini_set("smtp_port","25");
}
elseif($_SERVER['SERVER_NAME'] == "spearventures.net" || $_SERVER['SERVER_NAME'] == "www.spearventures.net")
{
  ini_set("SMTP","mail.spearventures.net");
  ini_set("smtp_port","25");
}*/

//ini_set("sendmail_from","SpearVentures Ltd <enquiries@spearventures.net>");


/*Session.php*/

class Session

{

   var $crud_username;     //Username given on sign-up

   var $crud_userid;       //Random value generated on current login

   var $time;         //Time user was last active (page loaded)

   var $crud_logged_in;    //True if user is logged in, false otherwise

   var $crud_userinfo = array();  //The array holding all user info

   var $url;          //The page url current being viewed

   var $crud_referrer;     //Last recorded site page viewed
   
   var $crud_useremail;  //Email address of currently logged in user
   
   //var $crud_access_areas;  //Role address of currently logged in user
   
   //var $crud_access_permissions;  //Role address of currently logged in user
   
   var $crud_userexp; //expiry date
   
   //var $section;  //Section of the application
   
   var $dbconn; //database connection


   /**

    * Note: referrer should really only be considered the actual

    * page referrer in process.php, any other time it may be

    * inaccurate.

    */

   /* Class constructor */

   function Session()
   {

      $this->time = time();

      $this->startSession();

   }
   
   function init()
   {
		  global $database;
		
		  /*$dbconn = new PDO($database->dsn, $database->username, $database->password);
		  $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	   	  $dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);*/

          $database->connection = mysql_connect($database->dsn, $database->username, $database->password) or die(mysql_error());
          $dbconn  =mysql_select_db($database->dbname, $database->connection) or die(mysql_error());
	   	
	   	  return $dbconn;
	}

   function startSession(){
   	
   	  global $database;

      session_start();   //Tell PHP to start the session

      /* Set referrer page */

      if(isset($_SESSION['crud_url']))
	  {
         $this->crud_referrer = $_SESSION['crud_url'];
      }
	  else
	  {
         $this->crud_referrer = "/";
      }

      /* Set current url */

      $this->crud_url = $_SESSION['crud_url'] = $_SERVER['PHP_SELF'];
      
      $dbconn=$this->init();  //The database connection
      
   }
   
    /* checkLogin - Checks if the user has already previously

    * logged in, and a session with the user has already been

    * established. Also checks to see if user has been remembered.

    * If so, the database is queried to make sure of the user's 

    * authenticity. Returns true if the user has logged in.

    */

   function checkLogin(){

      $dbconn=$this->init();  //The database connection

      /* Check if user has been remembered */

      if(isset($_COOKIE['crud_cookname']) && isset($_COOKIE['crud_cookname']))
	  {

         $this->crud_username = $_SESSION['crud_username'] = $_COOKIE['crud_username'];

         $this->crud_userid   = $_SESSION['crud_userid']   = $_COOKIE['crud_userid'];

      }

      /* Username and userid have been set and not guest */

      if(isset($_SESSION['crud_userinfo']) && isset($_SESSION['crud_userid']))
	  {

         /* Confirm that username and userid are valid */

         if($database->confirmUserID($_SESSION['crud_useremail'], $_SESSION['crud_userid']) != 0)
		 {

            /* Variables are incorrect, user not logged in */

            return false;

         }

         /* User is logged in, set class variables */

        $this->crud_userinfo  = $database->confirmUserID($_SESSION['aiti_username'], $_SESSION['crud_userid']);
			  
		$this->crud_user  = $_SESSION['crud_user'] = $this->crud_userid['first_name']."&nbsp;".$this->crud_userid['last_name'];
		  
		$this->crud_username  = $_SESSION['crud_username'] = $this->crud_userinfo['username'];
		
		$this->crud_userid    = $_SESSION['crud_userid'] = $this->crud_userinfo['userid'];
		  
		$this->crud_userid = $_SESSION['crud_userid'] = $this->crud_userinfo['email_address'];
			  
		//$this->aiti_acces_areas    = $_SESSION['aiti_acces_areas'] = $this->crud_userinfo['role_id'];
		
		$this->crud_userexp = $_SESSION['crud_userexp'] = $this->crud_userinfo['expiry_date'];
			  
		$this->crud_logged_in = $_SESSION['crud_logged_in']  = true;
		 
		return true;

      }

      /* User not logged in */
	else 
	{
	 return false; 
	
	}
 }



   /**

    * login - The user has submitted his username and password

    * through the login form, this function checks the authenticity

    * of that information in the database and creates the session.

    * Effectively logging in the user if all goes well.

    */

   function login($email, $subpass, $subremember){

      global $database,$form;  //The database and form object
      
      $dbconn = $this->init();

      /* Username error checking */

      if($email=="" || $session->checkmymail($email) == false)
	  {
			$field="email";
			$form->setError($field, "* Invalid email address.");
	  }
      /* Password error checking */

     
      if(!$subpass){

		 $field = "pass";  //Use field name for password
         $form->setError($field, "* Password not entered");

      }

      /* Return if form errors exist */

      if($form->num_errors > 0){

         return false;

      }
      else
	  {
		
		  $q = "SELECT user_id,first_name,last_name,email_address,expiry_date" .
			   " FROM  " .TBL_ADMIN_USERS.
			   " WHERE email_address = '$email' AND password = '$subpass'";
	      $result = $dbconn->query($q);
	      $result->setFetchMode(PDO::FETCH_ASSOC);
	      /* Check error codes */
	      /* Username and password correct, register session variables */
	
		  while ($row= $result->fetch(PDO::FETCH_ASSOC))
		  {
			  $this->crud_userinfo  = $database->getAdminUserInfo($email, $subpass);
			  
			  $this->crud_user  = $_SESSION['crud_user'] =$row['first_name']."&nbsp;".$row['last_name'];
		
		      $this->crud_userid  = $_SESSION['crud_userid'] =$row['user_id'];
			  
			  $this->crud_useremail = $_SESSION['crud_useremail']  =$row['email_address'];
			  
			  //$this->aiti_acces_areas    = $_SESSION['aiti_acces_areas'] = $row['role_id'];
		
		      $this->crud_userexp = $_SESSION['crud_userexp'] = $row['expiry_date'];
			  
			  $this->crud_logged_in = $_SESSION['crud_logged_in']  = true;
		  }
		 
		  if($subremember!="")
		  {
	         setcookie("crud_cookname", $this->crud_userinfo, time()+COOKIE_EXPIRE, COOKIE_PATH);
	         setcookie("crud_cookid",   $this->crud_userid,  time()+COOKIE_EXPIRE, COOKIE_PATH);
	      }
	
	   
	      /**
	
	       * This is the cool part: the user has requested that we remember that
	
	       * he's logged in, so we set two cookies. One to hold his username,
	
	       * and one to hold his random value userid. It expires by the time
	
	       * specified in constants.php. Now, next time he comes to our site, we will
	
	       * log him in automatically, but only if he didn't log out before he left.
	
	       */
	
	      
	      /* Login completed successfully */
		  
		 
	
	      return true;
		
	  }

   }



   /**

    * logout - Gets called when the user wants to be logged out of the

    * website. It deletes any cookies that were stored on the users

    * computer as a result of him wanting to be remembered, and also

    * unsets session variables and demotes his user level to guest.

    */

   function logout()
   {

      $dbconn=$this->init();  //The database connection

      /**

       * Delete cookies - the time must be in the past,

       * so just negate what you added when creating the

       * cookie.

       */


      if(isset($_COOKIE['crud_cookname']) && isset($_COOKIE['crud_cookid'])){

         setcookie("crud_cookname", "", time()-COOKIE_EXPIRE, COOKIE_PATH);

         setcookie("crud_cookid",   "", time()-COOKIE_EXPIRE, COOKIE_PATH);

      }
	  if(isset($_SESSION['crud_userid']))
	  {
		  $q="UPDATE ".TBL_ADMIN_USERS." SET is_logged_in='0' WHERE user_id=".$_SESSION['crud_userid'];
		  $dbconn->query($q);
	  }
	  
      //$database->removeActiveUser($_SESSION['aiti_username']);

      /* Unset PHP session variables */

      unset($_SESSION['crud_userid']);
	  
	  unset($_SESSION['crud_useremail']);
	  
	  //unset($_SESSION['aiti_acces_areas']);
	  
	  //unset($_SESSION['aiti_acces_permissions']);
	  
	  unset($_SESSION['crud_userexp']);
		
	  unset($_SESSION['crud_user']);
	  
	  unset($_SESSION['crud_logged_in']);

	   
      /* Reflect fact that user has logged out */

      $this->crud_logged_in = false;

      $this->urlRedirect("index.php");
      /**

       * Remove from active users table and add to

       * active guests tables.

       */
	

      //$database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

      /* Set user level to guest */

      //$this->aiti_username  = GUEST_NAME;

      //$this->userlevel = GUEST_LEVEL;

   }


  

 
/***
  This is to check the pattern of an email address
  
  Created by Nkonye Oyewusi

  ***/ 

 function checkmymail($mailadresse){
  
  $email_flag=preg_match("!^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$!",$mailadresse);
  
  return $email_flag;
 }

    /* generateRandID - Generates a string made up of randomized

    * letters (lower and upper case) and digits and returns

    * the md5 hash of it to be used as a userid.

    */

 function generateRandID(){

      return md5($this->generateRandStr(16));

 }
   
 

   /**

    * generateRandStr - Generates a string made up of randomized

    * letters (lower and upper case) and digits, the length

    * is a specified parameter.

    */

   function generateRandStr($length){

      $randstr = "";

      for($i=0; $i<$length; $i++){

         $randnum = mt_rand(0,61);

         if($randnum < 10){

            $randstr .= chr($randnum+48);

         }else if($randnum < 36){

            $randstr .= chr($randnum+55);

         }else{

            $randstr .= chr($randnum+61);

         }

      }

      return $randstr;

   }
   
   function DayAdd($interval, $date) 
   {
	  $curdate = $date;
	  $cday = date('j', strtotime($curdate));
	  $cday = $cday + $interval;
	  $cmonth=date('m', strtotime($curdate));
	  $cyear=date('Y', strtotime($curdate));
	  
	  if ($cday > 30)
	  {
		  $cmonth = $cmonth + 1;
		  $cday = $cday - 30;
		  if ($cmonth == 13)
		  {
			  $cyear = $cyear + 1;
			  $cmonth = 1;
		  }
  
	  }
	 //$ourDate = array($cyear,$cmonth,$cday);
	 $ourDate=$cday."-".$cmonth."-".$cyear;
	 $ourDate=date('Y-m-d', strtotime($ourDate));
	 return $ourDate;
  }
  
  function MonthAdd($interval, $date) 
  {
	  $curdate = $date;
	  $cday = date('j', strtotime($curdate));
	  $cmonth=date('m', strtotime($curdate));
	  $cmonth=$cmonth + $interval;
	  $cyear=date('Y', strtotime($curdate));
	  
	  if ($cmonth == 13)
	  {
		  $cyear = $cyear + 1;
		  $cmonth = 1;
	  }
  
	 $ourDate=$cday."-".$cmonth."-".$cyear;
	 $ourDate=date('Y-m-d', strtotime($ourDate));
	 return $ourDate;
  }
  
  function datediff($interval, $datefrom, $dateto, $using_timestamps = false) 
  {
    /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
        (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
    */
    
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }
    $difference = $dateto - $datefrom; // Difference in seconds
     
    switch($interval) {
     
    case 'yyyy': // Number of full years
 
        $years_difference = floor($difference / 31536000);
        if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
            $years_difference--;
        }
        if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
            $years_difference++;
        }
        $datediff = $years_difference;
        break;
 
    case "q": // Number of full quarters
 
        $quarters_difference = floor($difference / 8035200);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $quarters_difference--;
        $datediff = $quarters_difference;
        break;
 
    case "m": // Number of full months
 
        $months_difference = floor($difference / 2678400);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $months_difference--;
        $datediff = $months_difference;
        break;
 
    case 'y': // Difference between day numbers
 
        $datediff = date("z", $dateto) - date("z", $datefrom);
        break;
 
    case "d": // Number of full days
 
        $datediff = floor($difference / 86400);
        break;
 
    case "w": // Number of full weekdays
 
        $days_difference = floor($difference / 86400);
        $weeks_difference = floor($days_difference / 7); // Complete weeks
        $first_day = date("w", $datefrom);
        $days_remainder = floor($days_difference % 7);
        $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
        if ($odd_days > 7) { // Sunday
            $days_remainder--;
        }
        if ($odd_days > 6) { // Saturday
            $days_remainder--;
        }
        $datediff = ($weeks_difference * 5) + $days_remainder;
        break;
 
    case "ww": // Number of full weeks
 
        $datediff = floor($difference / 604800);
        break;
 
    case "h": // Number of full hours
 
        $datediff = floor($difference / 3600);
        break;
 
    case "n": // Number of full minutes
 
        $datediff = floor($difference / 60);
        break;
 
    default: // Number of full seconds (default)
 
        $datediff = $difference;
        break;
    }    
 
    return $datediff;
 
  }
  
  function Smiley($texttoreplace)
  {
    $smilies=array( ':)' => '<img src="images/smile.gif" height="20" width="20">',

                    ':blush' =>'<img src="images/blush.gif" height="20" width="20">',

				    ':angry' =>'<img src="images/angry.gif" height="20" width="20">',
					
					':wink' =>'<img src="images/wink.gif" height="20" width="20">',
					
					':cry' =>'<img src="images/cry.gif" height="20" width="20">',
					
					':sad' =>'<img src="images/sad.gif" height="20" width="20">',
					
					':smile' =>'<img src="images/smile.gif" height="20" width="20">',
					
					':(' => '<img src="images/sad.gif" height="20" width="20">',
					
					';)' => '<img src="images/wink.gif" height="20" width="20">',
				
				    ':o'=> '<img src="images/surprised.jpg" height="20" width="20">',  
				    
				    ':D'=> '<img src="images/laugh.gif" height="16" width="16">',
				
				    'fuck'=>"$#$%",
					'arse'=>"$#$%",
					'twat'=>"$#$%",
				    'Arse'=>"$#$%",
				    'Fuck'=>"&$#@",
					'Twat'=>"$#$%",
					'shit'=>"$#$%",
				    'Shit'=>"&$#@"
    );

    $texttoreplace=str_replace(array_keys($smilies), array_values($smilies), $texttoreplace);
    return $texttoreplace;
 }
   
  function urlRedirect($url)
  {
  	  if ( !headers_sent() ) 
  	  {
  	  		header('Location: '.$url);
  	  		exit;
  	  }
  	  
  	  echo '<script type="text/javascript">window.location.href="'.$url.'";</script>' .
  	  '<noscript><meta http-equiv="refresh" content="0;url="'.$url.'" /></noscript>';
  	  exit;
  }

};


/**

 * Initialize session object - This must be initialized before

 * the form object because the form uses session variables,

 * which cannot be accessed unless the session has started.

 */

$session = new Session;


/* Initialize form object */

$form = new Form;

?>

