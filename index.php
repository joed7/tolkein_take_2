<?php
//Name: index.php
//Purpose: Create an application called tolkien with authentication.
//Author: Rohit Gupta rohit.gupta@colorado.edu
//Version: 1.0
//Date : 24-Feb-2016

include_once('/var/www/html/hw5/hw5-lib.php');

isset( $_REQUEST['s'] ) ? $s = strip_tags(trim($_REQUEST['s'])) : $s = "";
isset( $_REQUEST['sid'] ) ? $sid = strip_tags(trim($_REQUEST['sid'])) : $sid = "";
isset( $_REQUEST['bid'] ) ? $bid = strip_tags(trim($_REQUEST['bid'])) : $bid = "";
isset( $_REQUEST['cid'] ) ? $cid = strip_tags(trim($_REQUEST['cid'])) : $cid = "";

if($s == Null){
  $s=0;
}

icheck($sid);
icheck($bid);
icheck($cid);



include_once('header.php');

$out="";



if(is_numeric($s)){
	switch ($s) {
		case '1':
				if($sid == Null){
					print "<b> ERROR: </b> Invalid Sysntax.";
					exit;
				}
				$out = $out . "<table><tr><td><b><u>Books</b></u></td></tr></n>";
				
				connect($db);
				$sid=mysqli_real_escape_string($db,$sid);
				$stmt = mysqli_prepare($db,"SELECT bookid,title from books where storyid=?"); 
				
				if( $stmt != null){
					mysqli_stmt_bind_param($stmt,"i",$sid);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$bid,$title);

			    	$out= $out ."<table>";
	                while(mysqli_stmt_fetch($stmt)){
	                	$bid=htmlspecialchars($bid);
	                	$title=htmlspecialchars($title);
	                    $out = $out."<tr>
	                    		<td><a href=index.php?s=2&bid=$bid>$title</a></td></tr>\n";			
					}
					mysqli_stmt_close($stmt);
				}
				break;	
		case '2':
                if($bid == Null){
                	print "<b> ERROR: </b> Invalid Sysntax."; 
	       			exit;
                }

    			$out = $out . "<table><tr><td><b><u>Characters</b></u></td></tr></n>";

                connect($db);
				$bid=mysqli_real_escape_string($db,$bid);
				$stmt = mysqli_prepare($db,"select c.characterid,c.name from books a,appears b,characters c where a.bookid=b.bookid and c.characterid=b.characterid and a.bookid=?"); 

				if( $stmt != null){
					mysqli_stmt_bind_param($stmt,"i",$bid);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$characterid,$name);

			    	$out= $out ."<table>";
	                while(mysqli_stmt_fetch($stmt)){
	                	$characterid=htmlspecialchars($characterid);
	                	$name=htmlspecialchars($name);
	                    $out = $out."<tr>
	                    		<td><a href=index.php?s=3&cid=$characterid>$name</a></td></tr>\n";
					}
					mysqli_stmt_close($stmt);
				}
				break;
		case '3':
                if($cid == Null){
                	print "<b> ERROR: </b> Invalid Sysntax."; 
	      		 	exit;
                }
    			
    			$out = $out . "<table><tr><td colspan=\"3\"><b><u>Appearances</b></u></td></tr></n>";
    			$out = $out . "<tr><td>Character</td><td>Book</td><td>Story</td></tr>";

                connect($db);
				$bid=mysqli_real_escape_string($db,$cid);
				$stmt = mysqli_prepare($db,"select b.name,c.title,d.story from appears a,characters b,books c,stories d where a.bookid=c.bookid and b.characterid=a.characterid and d.storyid=c.storyid and b.characterid=?"); 

				if( $stmt != null){
					mysqli_stmt_bind_param($stmt,"i",$cid);
			    	mysqli_stmt_execute($stmt);
			    	mysqli_stmt_bind_result($stmt,$name,$title,$story);

			    	
	                while(mysqli_stmt_fetch($stmt)){
	                	$title=htmlspecialchars($title);
	                	$name=htmlspecialchars($name);
	                	$story=htmlspecialchars($story);
				        $out = $out."<div><tr>";
						$out = $out."<td><a href=index.php>".$name."</a></td>";	
						$out = $out."<td><a href=index.php>".$title."</a></td>";
						$out = $out."<td><a href=index.php>".$story."</a></td>";
						$out = $out."</div></tr>\n";
					}
					mysqli_stmt_close($stmt);
				}			
				break;								
		case '50':
				connect($db);

				$query="select b.characterid,a.url,b.name from pictures a,characters b where a.characterid=b.characterid;";
				$result=mysqli_query($db,$query);
				$out= $out ."<table>";
				while($row=mysqli_fetch_row($result)){
						$val1=htmlspecialchars($row[0]);
						$val2=htmlspecialchars($row[1]);
						$val3=htmlspecialchars($row[2]);
						
				        $out = $out."<tr>
				        <td><a href=index.php?s=3&cid=$val1>$val3</a></td>";
					$out = $out."<td><img src=\"".$val2."\"></td></tr>\n";	
				}

			break;
		default:
			connect($db);
			$query="SELECT storyid,story from stories";
			$result=mysqli_query($db,$query);
 		 	$out= $out."<table>";
			while($row=mysqli_fetch_row($result)){
				$val1=htmlspecialchars($row[0]);
				$val2=htmlspecialchars($row[1]);
				$out = $out."<tr>
        		<td><a href=index.php?s=1&sid=$val1>$val2</a></td></tr>\n";
			}
		                                 
		break;
	}
}










echo $out;



function showAddedPictureOuput($name,$charid){
	return "
	<form method=post action=index.php>
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
	<form method=post action=index.php>

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
	<form method=post action=index.php>

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
function showCharacterForm(){
	return "
	<form method=post action=index.php>

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

function showAddPictureForm($name,$charid){
		return "
	<form method=post action=index.php>

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

?>
