<?php
//dati per il login 

$login_user="prova@prova.it"; 
$pass_user="prova"; //passwd="prova" 
$redirect="http://localhost/~m3xican/m3xican/su.php"; 

//gestione della sessione nel caso in cui i cookie sono disabilitati 

if(IsSet($_POST['PHPSESSID']) && !IsSet($_COOKIE['PHPSESSID'])) 
{ 
	$PHPSESSID=$_POST['PHPSESSID']; 
	header("Location: $redirect?PHPSESSID=$PHPSESSID"); //si ricarica la pagina di login 

} 

session_start(); //si inizia o continua la sessione 
//controllo user e passwd da login 
if(IsSet($_POST['inputEmail']) && IsSet($_POST['inputPassword'])) 
{ 
	if($login_user==($_POST['inputEmail']) && $pass_user==($_POST['inputPassword'])) // md5($_POST['posted_password'])) 
		$_SESSION['user']=$_POST['inputEmail']; 
} 

//creazione cookie per login automatico 

if(IsSet($_POST['ricorda']) && IsSet($_SESSION['user'])) 
{ 
	$cok=md5($login_user)."%%".$pass_user; 
	setcookie("sav_user",$cok,time()+31536000); 
} 

//logout 

if(isset($_GET['logout']) && $_GET['logout']==1) 
{ 
	$_SESSION=array(); // Desetta tutte le variabili di sessione. 
	session_destroy(); //DISTRUGGE la sessione. 
	if(IsSet($_COOKIE['sav_user'])) //se presente si distrugge il cookie di login automatico 
		setcookie("sav_user",$cok,time()-31536000); 
	header("Location: $redirect"); //si ricarica la pagina di login 
	exit; //si termina lo script in modo da ritornare alla schermata di login 
} 

//controllo user e passwd da cookie 
if(IsSet($_COOKIE['sav_user'])) 
{ 
$info_cok=$_COOKIE['sav_user']; 
$cok_user=strtok($info_cok,"%%"); 
$cok_pass=strtok("%%"); 
setcookie("sav_user",$info_cok,time()+31536000); 
if($cok_user==md5($login_user) && $cok_pass==$pass_user) 
	$_SESSION['user']=$login_user; 
} 

//caso in cui si vuole ricordare il login, ma i cookie sono off 

if(!IsSet($_COOKIE['PHPSESSID']) && IsSet($_POST['ricorda'])) 
	header("Location: $redirect?nocookie=1"); 

?> 