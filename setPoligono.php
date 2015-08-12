<?php
session_start();
$puntiArr = $_GET["poligono"];
$puntiArrSession = $_SESSION["poligono"];
print_r($puntiArrSession);
print($puntiArr);

for ($i=0; $i < $puntiArr.lenght; $i ++ ){
	
	//echo (puntiArr[$i] . "<br /> ");
}

?>

<script>window.open('index.php')</script>