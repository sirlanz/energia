<?php
require ("common/class/core.php");
//dati per il login 
$login_user="prova@prova.it"; 
$pass_user="prova"; //passwd="prova" 
$redirect="index.php"; 

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
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="Andrea Lanzoni">
    <link rel="icon" href="../../favicon.ico">

    <title>Log in</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
    
    <link href="css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </head>

  <body>
	
    <div class="container">
	<?php 
	$PHPSESSID=session_id(); 
	if(!IsSet($_SESSION['user'])) //non siamo loggati, pagina di login 
	{ 
		if(isset($_GET['nocookie']) && $_GET['nocookie']==1) //i cookie sono off e si vuole ricordare il login 
			print("Spiacente, ma con i cookie disabilitati non posso fare i miracoli ;-) <BR> 
					Attivali se vuoi ricordare il tuo login.<BR>"); 
	?>
      <form class="form-signin" action="index.php" method="POST" enctype=”multipart/form-data”>
        <h2 class="form-signin-heading">Eseguire log in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="ok" name="ricorda"> Ricordami
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
      </form>
	<?php 
	}
	
	else //siamo loggati pagina riservata
	
	{ 
		$username=$_SESSION['user'];
		
		//print("Il tuo ID ?: $PHPSESSID <BR><BR>");
		
		//print("Sei loggato come: $login_user<BR><BR>");
		
		//print("<A HREF=\"index.php?logout=1\">logout</A>");
		?>
	
		<nav class="navbar navbar-inverse navbar-fixed-top">
		<!-- Importo questo menù da php in modo da uniformare le modifiche -->
		 <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://energia.innology.it/index.php">Energy Management</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="http://energia.innology.it/index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Strumenti <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Inserisci fattura</a></li>
                <li><a href="#">Cerca fattura</a></li>
               
                <li class="divider"></li>
                <li class="dropdown-header">Azienda</li>
                <li><a href="#">Elenco</a></li>
            
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> Salve <?=$username?>! <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Profilo</a></li>
                <!-- <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li> -->
                <li class="divider"></li>
                <li class="dropdown-header">Logout</li>
                <li><a href="index.php?logout=1">Logout</a></li>
                
              </ul>
            </li>
		<li></li>
		<li><a href="help.php">Help</a></li>
		</ul>
        </div><!--/.nav-collapse -->
      </div>
      
     </nav>
		    <div class="container-fluid">
			<div class="row">
			    
			    
			    <div class="col-sm-3 col-md-2 sidebar">
		          <ul class="nav nav-sidebar">
		          
		            <li class="active"><a href="#">Aziende<span class="sr-only">(current)</span></a></li>
		            <li><a href="azienda/new.php">>> Nuova</a></li>
		            
		           
		          </ul>
		         
		          <ul class="nav nav-sidebar">
			         
			         
			          <li><a href="#">Fornitori</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			          
			          
			          <li><a href="#">Contratti</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			         
			          <li><a href="#">Utenze</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			         
			          <li><a href="#">Bollette</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			         
			            <li><a href="#">Rilievi</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			        
			            <li><a href="#">Unit&agrave; locale</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			          
			            <li><a href="#">Indirizzi</a></li>
			          <li><a href="#">>> Nuovo</a></li>
		          </ul>
		           <ul class="nav nav-sidebar">
			          
			           <li><a href="#">Citt&agrave;</a></li>
			          <li><a href="#">>> Nuova</a></li>
		          </ul>
		        </div>
			    
			    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			      <h1><?=TITOLO_HOME?></h1>
			      <p class="lead">Questa pagina è il riferimento di tutta l'applicazione.<br> 
			      Se sei in difficoltà clicca su Home oppure consulta la guida Help
			      </p>
			      
			      <!-- PlaceHolder -->
			      
			       <div class="row placeholders">
		            <div class="col-xs-6 col-sm-3 placeholder">
		              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		              <h4>Azienda</h4>
		              <span class="text-muted">Something else</span>
		            </div>
		            <div class="col-xs-6 col-sm-3 placeholder">
		              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		              <h4>Contratto</h4>
		              <span class="text-muted">Something else</span>
		            </div>
		            <div class="col-xs-6 col-sm-3 placeholder">
		              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
		              <h4>Bolletta</h4>
		              <span class="text-muted">Something else</span>
		            </div>
		            <div class="col-xs-6 col-sm-3 placeholder">
		              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
		              <h4>Unit&agrave;</h4>
		              <span class="text-muted">Something else</span>
		            </div>
		          </div>
		       </div>
			      
			      <!-- PlaceHolder End -->
			      
			      
			      
			    </div>
		
		    </div><!-- /.container -->
		<?php 
		
	
	}
	
	?>
	
    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
