<?php
class Employee
{
	function save_user($first_name,$last_name,$email_address,$username,$password,$user_id=0)
	{
		//Global classes inherited from includes/database.php and includes/form.php 
		global $database, $form;
		
		//Server-side error checking on the form
		if($first_name=="")
		{
			$field="first_name";
			$form->setError($field, "* First name required.");
		}
		if($last_name=="")
		{
			$field="last_name";
			$form->setError($field, "* Last name required.");
		}
		if($email_address=="")
		{
			$field="email_address";
			$form->setError($field, "* Email address required.");
		}
		if($username=="")
		{
			$field="username";
			$form->setError($field, "* Username required.");
		}
		if($password=="")
		{
			$field="pasword";
			$form->setError($field, "* Password required.");
		}
		
		//Setting values
	   	$form->SetValue("first_name",$first_name);
		$form->SetValue("last_name",$last_name);
		$form->SetValue("email_address",$email_address);
		$form->SetValue("username",$username);
		$form->SetValue("password",$password);

		//No errors in the form so proceed to either add or edit the data
		if($form->num_errors == 0)
		{
			//If a userid has been passed, it means we are editiong a record. 
			//Otherwise it means we are adding a record.
			if($user_id==0)
			{
				$q="INSERT INTO ".TBL_ADMIN_USERS."(first_name,last_name,email_address,username,password)
				    VALUES('".$first_name."','".$last_name."','".$email_address."','".$username."','".$password."')";
			}
			else
			{
				$q="UPDATE ".TBL_ADMIN_USERS.
				   " SET first_name='".$first_name."',
				         last_name='".$last_name."',
				         email_address='".$email_address."',
				         username='".$username."',
				         password='".$password."' 
				    WHERE user_id=".$user_id;
			}

			//Run query using the query function in includes/database.php
			$r = $database->query($q) or die("Error inserting or updating area details ".__LINE__.mysql_error());
			
			//In this case $r returns true or false.
			return $r;
		}
	}
	
	function delete_user($user_id)
	{
		//Global database class from includes/database.php
		global $database;
		
		//Run query using the query function in includes/database.php
		$q="DELETE FROM ".TBL_ADMIN_USERS." WHERE user_id=".$user_id;
		$r = $database->query($q) or die("Error deleting user ".__LINE__.mysql_error());
		
		//In this case $r returns true or false.
		return $r;
	}
	
	function get_user($user_id=0)
	{
		//Global class inherited from includes/database.php
		global $database;
		
		//If we are passing in a user id, it means we are getting one record.
		//Otherwise we are getting the entire recordset.
		$q="SELECT * FROM ".TBL_ADMIN_USERS;
		
		if($user_id > 0)
		{
			$q.=" WHERE user_id=".$user_id;
		}

		//Run query using the query function in includes/database.php
		$r = $database->query($q) or die("Error getting user details ".__LINE__.mysql_error());
		
		//In this case, $r is an associative array containing the records we want to retrieve
		return $r;
	}
	
	
}

$emp = new Employee;
?>