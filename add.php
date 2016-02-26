<?php

session_start();
include_once('header.php');
include_once('/var/www/html/hw6/hw6-lib.php');

isset( $_REQUEST['s'] ) ? $s = strip_tags(trim($_REQUEST['s'])) : $s = "";
isset( $_REQUEST['uname'] ) ? $uname = strip_tags(trim($_REQUEST['uname'])) : $uname = "";
isset( $_REQUEST['pwd'] ) ? $pwd = strip_tags(trim($_REQUEST['pwd'])) : $pwd = "";
isset( $_REQUEST['side'] ) ? $side = strip_tags(trim($_REQUEST['side'])) : $side = "";
isset( $_REQUEST['race'] ) ? $race = strip_tags(trim($_REQUEST['race'])) : $race = "";
isset( $_REQUEST['cname'] ) ? $cname = strip_tags(trim($_REQUEST['cname'])) : $cname = "";
isset( $_REQUEST['curl'] ) ? $curl = strip_tags(trim($_REQUEST['curl'])) : $curl = "";
isset( $_REQUEST['charid'] ) ? $charid = strip_tags(trim($_REQUEST['charid'])) : $charid = "";
isset( $_REQUEST['bookid'] ) ? $bookid = strip_tags(trim($_REQUEST['bookid'])) : $bookid = "";

isset( $_REQUEST['username'] ) ? $username = strip_tags(trim($_REQUEST['username'])) : $username = "";
isset( $_REQUEST['password'] ) ? $password = strip_tags(trim($_REQUEST['password'])) : $password = "";
isset( $_REQUEST['email'] ) ? $email = strip_tags(trim($_REQUEST['email'])) : $email = "";

icheck($s);
icheck($charid);
icheck($bookid);

var_dump($_REQUEST);

var_dump($username;
	
$out ='';


if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "yes"){
	handleCharacterForm();
}else{
	if($uname == null || $pwd == null){
		header("Location:/hw6/login.php");
	}
	connect($db);
	if (!authenticate($db,$uname,$pwd) ){
		header("Location:/hw6/login.php");
	}else{
		handleCharacterForm();
	}
}

echo $out;

function handleCharacterForm(){
		global $s;
		global  $out;

		switch ($s) {
		case '4':
				#echo showCharacterForm();
			   $out = $out . showCharacterForm();

			break;	
		case '5':
				global $cname;
				global $side;
				global $race;
				
				nullCheck($cname);
				nullCheck($side);
				nullCheck($race);

                connect($db);

				$cname=mysqli_real_escape_string($db,$cname);
				$side=mysqli_real_escape_string($db,$side);
				$race=mysqli_real_escape_string($db,$race);

				$stmt = mysqli_prepare($db,"insert into characters set characterid='', name=?,race=?,side=?");

				if($stmt != null){
					mysqli_stmt_bind_param($stmt,"sss",$cname,$race,$side);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_close($stmt);
				}
				$stmt = mysqli_prepare($db,"select characterid from characters where name=? and race=? and side = ? order by characterid desc limit 1");
	
				$charid='';
				
				if($stmt != null){
					mysqli_stmt_bind_param($stmt,"sss",$cname,$race,$side);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$cid);
	                while(mysqli_stmt_fetch($stmt)){
	                	$charid = htmlspecialchars($cid);	
					}	
					mysqli_stmt_close($stmt);
				}

				if($charid != null){
					$out = $out . showAddPictureForm($cname,$charid);
				}else{
                	print "<b> ERROR: </b> Invalid Sysntax."; 
	      		 	exit;					
				}
				break;
		case '6':
				global $curl;
				global $charid;
				global $cname;

				nullCheck($curl);
				nullCheck($charid);
				nullCheck($cname);

				connect($db);

				$curl=mysqli_real_escape_string($db,$curl);
				$charid=mysqli_real_escape_string($db,$charid);
				$cname=mysqli_real_escape_string($db,$cname);


				$stmt = mysqli_prepare($db,"insert into pictures set pictureid='', url=?,characterid=?");
	
				try{				
					if($stmt != null){
						mysqli_stmt_bind_param($stmt,"ss",$curl,$charid);
				    	mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);
					}
					$out = $out . showAddedPictureOuput($cname,$charid);
				}catch(Exception $e){
					print "<b>Some error Occured"; 
	      		 	exit;
				}

				break;		
		case 7:

				global $charid;
				global $cname;
						

				nullCheck($charid);
				nullCheck($cname);

				connect($db);
				
				$bookids=array();
				$booktitles=array();

				
				$cname=mysqli_real_escape_string($db,$cname);
				$charid=mysqli_real_escape_string($db,$charid);

				$stmt = mysqli_prepare($db,"SELECT distinct(a.bookid), b.title FROM
											books b, appears a WHERE a.bookid NOT IN
											(SELECT bookid FROM appears WHERE
											characterid=?) AND b.bookid=a.bookid");

				if($stmt != null){
					mysqli_stmt_bind_param($stmt,"i",$charid);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$bookid,$title);

			    	while(mysqli_stmt_fetch($stmt)){
			    		 array_push($bookids,htmlspecialchars($bookid));
						 array_push($booktitles,htmlspecialchars($title));
					}
					
					$out = $out . showAddBooksForm($charid,$cname,$bookids,$booktitles);

					mysqli_stmt_close($stmt);
				}
				break;	
		case 8:

				global $charid;
				global $cname;
				global $bookid;
	
				nullCheck($charid);
				nullCheck($cname);
				nullCheck($bookid);

				connect($db);
				
				$bookids=array();
				$booktitles=array();

				
				$bookid=mysqli_real_escape_string($db,$bookid);
				$charid=mysqli_real_escape_string($db,$charid);
				$cname=mysqli_real_escape_string($db,$cname);

				$stmt = mysqli_prepare($db,"insert into appears set appearsid='', bookid=?,characterid=?");
	
				try{				
					if($stmt != null){
						mysqli_stmt_bind_param($stmt,"ii",$bookid,$charid);
				    	mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);
					}
	
				}catch(Exception $e){
					print "<b>Some error Occured while inserting"; 
	      		 	exit;
				}


				$stmt = mysqli_prepare($db,"SELECT distinct(a.bookid), b.title FROM
											books b, appears a WHERE a.bookid NOT IN
											(SELECT bookid FROM appears WHERE
											characterid=?) AND b.bookid=a.bookid");

				if($stmt != null){
					mysqli_stmt_bind_param($stmt,"i",$charid);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$bookid,$title);

			    	while(mysqli_stmt_fetch($stmt)){
			    		 array_push($bookids,htmlspecialchars($bookid));
						 array_push($booktitles,htmlspecialchars($title));
					}
					
				$out = $out . showAddBooksFormAndDone($charid,$cname,$bookids,$booktitles);

					mysqli_stmt_close($stmt);
				}
				
				
				break;			
			case 90:

				if (adminCheck()){
					$out  = $out. showAddUserForm();	
				}else{
					$out = $out."<b> ERROR: </b> Not authorized to access this privilege";
					exit; 
				}
				break;
			case 91:
				if (!adminCheck()){

					$out = $out."<b> ERROR: </b> Not authorized to access this privilege";
					exit; 
				}
				//add user
				nullCheck($username);
				nullCheck($password);
				nullCheck($email);

				$username=mysqli_real_escape_string($db,$username);
				$password=mysqli_real_escape_string($db,$password);
				$email=mysqli_real_escape_string($db,$email);
				
				$salt = rand(100,20000);
				$hashed_salt=hash('sha256',$salt);

				$epass=hash('sha256',$password.$hashed_salt);	

				connect($db);

				$query = "insert into users set userid=' ',username=?, email=?,password=?,salt=? ";

				$stmt = mysqli_prepare($db,$query);
	
				try{				
					if($stmt != null){
						mysqli_stmt_bind_param($stmt,"sss",$username,$email,$epass,$hashed_salt);
				    	mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);
						$out = $out . "Added new user".$username;
					}
	
				}catch(Exception $e){
					print "<b>Some error Occured while inserting"; 
	      		 	exit;
				}

				break;
			default:
				$out = $out . showCharacterForm();

				break;
		}

		$out = $out . showAuthFooterLink();

}


function showAuthFooterLink(){
	return "<a href=logout.php>Logout</a>|<a href=add.php?s=90>add user</a>";
}


function showCharacterForm(){
	return "
	<form method=post action=add.php>

	<table>
	<tr>
	Add character to Books
	</tr>
	<tr>
		<td>Character Name</td>
		<td><input type=\"text\" id=\"cname\" name=\"cname\"></td>
	</tr>	
	<tr>
		<td>Race</td>
		<td><input type=\"text\" id=\"race\" name=\"race\"></td>
	</tr>	
	<tr>
		<td>Side</td>
		<td>  <input type=\"radio\" name=\"side\" value=\"good\"> Good
  			  <input type=\"radio\" name=\"side\" value=\"evil\"> Bad
  		</td>
  	</tr>
	<tr>
		<td>
		<input id=\"s\" name=\"s\" type=\"hidden\" value=\"5\">
		<input type=\"submit\" value=\"submit\">
		</td>
	</tr>
	</table>
	</form>";
}

function showAddedPictureOuput($name,$charid){
	return "
	<form method=post action=add.php>
	<table>
	<tr>
	<td>
	Added picture for $name
	</td>
	</tr>
	<tr>
	<td>
	<input type=\"submit\" value=\"Add character to books\">
	<input id=\"s\" name=\"s\" type=\"hidden\" value=\"7\">
	<input id=\"charid\" name=\"charid\" type=\"hidden\" value=\"$charid\">
	<input id=\"cname\" name=\"cname\" type=\"hidden\" value=\"$name\">
	</td>
	</tr>
	</table>
	</form>";
}

function showAddBooksForm($charid,$name,$bookids,$booktitles){
		$out = "
	<form method=post action=add.php>

	<table>
	<tr>
	Add $name to Books
	</tr>
	<tr>
		<td>Select Books</td>
		<td><select  id=\"bookid\" name=\"bookid\">";
	
	for ($x = 0; $x < sizeof($bookids); $x++) {
    	$out = $out . "<option value=\"$bookids[$x]\">$booktitles[$x]</option>";
	} 			
	$out = $out ."

		</td>
	</tr>	
	<tr>
		<td>
		<input id=\"s\" name=\"s\" type=\"hidden\" value=\"8\">
		<input id=\"charid\" name=\"charid\" type=\"hidden\" value=\"$charid\">
		<input id=\"cname\" name=\"cname\" type=\"hidden\" value=\"$name\">
		<input type=\"submit\" value=\"Add to book\">
		</td>
	</tr>
	</table>
	</form>";

	return $out;

}

function showAddBooksFormAndDone($charid,$name,$bookids,$booktitles){
		$out = "
	<form method=post action=add.php>

	<table>
	<tr>
	Add $name to Books
	</tr>
	<tr>
		<td>Select Books</td>
		<td><select  id=\"bookid\" name=\"bookid\">";
	
	for ($x = 0; $x < sizeof($bookids); $x++) {
    	$out = $out . "<option value=\"$bookids[$x]\">$booktitles[$x]</option>";
	} 			
	$out = $out ."

		</td>
	</tr>	
	<tr>
		<td>
		<input id=\"s\" name=\"s\" type=\"hidden\" value=\"8\">
		<input id=\"charid\" name=\"charid\" type=\"hidden\" value=\"$charid\">
		<input id=\"cname\" name=\"cname\" type=\"hidden\" value=\"$name\">
		<input type=\"submit\" value=\"Add to Book\">
		<a href=index.php?s=3&cid=$charid>Done</a>
		</td>
	</tr>
	</table>
	</form>";

	return $out;

}

function showAddPictureForm($name,$charid){
		return "
	<form method=post action=add.php>

	<table>
	<tr>
	Add picture to character $name
	</tr>
	<tr>
		<td>Character Picture URL</td>
		<td><input type=\"text\" id=\"curl\" name=\"curl\"></td>
	</tr>	
	<tr>
		<td>
		<input id=\"s\" name=\"s\" type=\"hidden\" value=\"6\">
		<input id=\"charid\" name=\"charid\" type=\"hidden\" value=\"$charid\">
		<input id=\"cname\" name=\"cname\" type=\"hidden\" value=\"$name\">
		<input type=\"submit\" value=\"submit\">

		</td>
	</tr>
	</table>
	</form>";
}


function showAddUserForm(){
	return "
	<form method=post action=add.php>

	<table>
	<tr>
	Add User Form
	</tr>
	<tr>
		<td>Username:</td>
		<td><input required type=\"text\" id=\"username\" name=\"username\"></td>
	</tr>	
	<tr>
		<td>Password</td>
		<td><input required type=\"password\" id=\"password\" name=\"password\"></td>
	</tr>	
	<tr>
		<td>Email</td>
		<td><input required type=\"text\" id=\"email\" name=\"email\"></td>
	</tr>	
	<tr>
		<td>
		<input id=\"s\" name=\"s\" type=\"hidden\" value=\"91\">
		<input type=\"submit\" value=\"submit\">
		</td>
	</tr>
	</table>
	</form>";
}

function icheck($i){
	if($i != null){
		if(!is_numeric($i)){
			print "<b> ERROR: </b> some error occured";
			exit; 
		}
	}
}


function nullCheck($i){
	if($i == null){
		print "<b> ERROR: </b> some error occured";
		exit; 
	}
}

function adminCheck(){
	if ( isset($_SESSION['userid']) && $_SESSION['userid'] == 1){
		return true;
	}else{
		return false;
	}
}

function authenticate($db,$postUser,$postPass){

	$userId= '';
	$email= '';
	$password = '';
	$salt='';

	$postUser=mysqli_real_escape_string($db,$postUser);
	$postPass=mysqli_real_escape_string($db,$postPass);

	$query = "select userid,email,password,salt from users where username=?";

	$stmt = mysqli_prepare($db,$query);

	try{				
		if($stmt != null){
			mysqli_stmt_bind_param($stmt,"s",$postUser);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt,$uid,$mail,$pwd,$slt);

			while(mysqli_stmt_fetch($stmt)){
				 $userId =$uid;
				 $email=$mail;
				 $password=$pwd;
				 $salt=$slt;
			}

			mysqli_stmt_close($stmt);
			$epass=hash('sha256',$postPass.$salt);	


			if( $epass == $password){
				$_SESSION['userid']=$userId;
				$_SESSION['email']=$email;	
				$_SESSION['authenticated']="yes";							
				$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];	

				return true;
			}else{
				echo "Failed to login";
				return false;
				#header("Location:/hw6/login.php");
				#exit;
			}
  


		}

	}catch(Exception $e){
		print "<b>Some error Occured"; 
	 	exit;
	}

	return false;

}


?>