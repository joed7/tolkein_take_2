<?php
//Name: login.php
//Purpose: Login Page.
//Author: Rohit Gupta rohit.gupta@colorado.edu
//Version: 1.0
//Date : 24-Feb-2016
include_once('header.php');

echo showLoginForm();


function showLoginForm(){
	return "
	<form method=post action=add.php>

	<table>
	<tr>
	Login Form
	</tr>
	<tr>
		<td>Username:</td>
		<td><input required type=\"text\" id=\"uname\" name=\"uname\"></td>
	</tr>	
	<tr>
		<td>Password</td>
		<td><input required type=\"password\" id=\"pwd\" name=\"pwd\"></td>
	</tr>	
	<tr>
		<td>
		<input type=\"submit\" value=\"submit\">
		</td>
	</tr>
	</table>
	</form>";
}


?>