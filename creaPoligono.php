<?php
require ("common/class/core.php");
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?=TITOLO_PAGINA?></title>
    <style>
		html, body, #map-canvas {
		height: 100%;
		margin: 0px;
		padding: 0px
		}
		 #panel {
		        position: absolute;
		        top: 5px;
		        left: 50%;
		        margin-left: -180px;
		        z-index: 5;
		        background-color: #fff;
		        padding: 5px;
		        border: 1px solid #999;
	      }
	</style>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
     <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
 <!--   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> -->
   <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  <script>

  




var map;
var infoWindow;
var bermudaTriangle;
var poligono;
var geocoder;
//Define the LatLng coordinates for the polygon.
var coordinate = [];

function codeAddress() {
  var address = document.getElementById('address').value;
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      var marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location
      });
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}



function initialize() {
	
	geocoder = new google.maps.Geocoder();
	var mapOptions = {
		zoom: 18,
		center: new google.maps.LatLng(45.403489, 10.998671),
		mapTypeId: google.maps.MapTypeId.HYBRID
	};

	

	map = new google.maps.Map(document.getElementById('map-canvas'),
			mapOptions);

	
	creaPoligono();	

	//This event listener will call addCoord() when the map is clicked.
	google.maps.event.addListener(map, 'click', function(event) {
		
		addVertex(event.latLng);
		
	});
	
	infoWindow = new google.maps.InfoWindow();
}


function addVertex(coords){
	//alert('Le nuove coordinate da aggiungere al poligono sono: ' + coords);
	coordinate.push(coords);
	coordinateStr = '';
	for (i=0; i<=coordinate.length; i++){
			coordinateStr = coordinateStr + coordinate[i];
		}
	//alert('Le coordinate sono: ' + coordinateStr);
	poligono.setPaths(coordinate);
	
}

function creaPoligono(){
	// Construct the polygon.
	poligono = new google.maps.Polygon({
		paths: coordinate,
		strokeColor: '#FF0000',
		strokeOpacity: 0.8,
		strokeWeight: 3,
		fillColor: '#FF0000',
		fillOpacity: 0.35,
		editable: true
	});

	poligono.setMap(map);
	
	// Add a listener for the click event.
	google.maps.event.addListener(poligono, 'click', showArrays);
	google.maps.event.addListener(poligono, 'rightclick', rightMenu);
	
	
}

//Add a marker to the map and push to the array.
function addCoord(location) {
  //var coordinata = new google.maps.Marker({
  //  position: location,
  //  map: map
  // });
  coordinate.push(new google.maps.LatLng(location.getPosition()));
  //alert(coordinate[coordinate.length-1].getPosition());
  alert('Coordinata nuova '+ location.toString());
  
  creaPoligono();
  
}

function rightMenu(event){
	
	var points = poligono.getPath();
	console.log("Scrivo points: ");
	console.log(points);
	var point = points.getAt(0);
	var contentString = '<b>Opzioni</b><br>' +
	'<a href="#" onclick="changeColor(\'#FFFF00\');">Giallo</a><br />'+
	'<a href="#" onclick="changeColor(\'#FFFFFF\');">Grigio</a><br />'+
    '<a href="#" onclick="inviaPoligono(poligono);">Invia il poligono</a>';

    infoWindow.setContent(contentString);
	infoWindow.setPosition(point);
	infoWindow.open(map);
	
}

function setSessionPoligono(poligono){
	
	
}

function inviaPoligono(poligono) {

	
	
	console.log("Scrivo poligono in 'inviaPoligono': ");
	console.log(poligono);
	$.ajax({
		
	  url: "setPoligono.php",
	  async: false,
	  type: "post",
	  dataType : 'json',
	  data: { poligono : poligono},
	  success: function(data) {
	    console.log("e qui intercetti la conclusione...") ;
	    },
	  error: function( xhr, status, errorThrown ) {
        alert( "Sorry, there was a problem!" );
        console.log( "Error: " + errorThrown );
        console.log( "Status: " + status );
        console.dir( xhr );
      },
      complete: function( xhr, status ) {
          alert( "The request is complete!" );
      }
	})
}


function changeColor(colore){

	poligono.setOptions({fillColor:colore});
}

//Deletes all markers in the array by removing references to them.
function clearPoligono() {
	//poligono.setPaths(null);
  coordinate = [];
  creaPoligono();
}

//Deletes all markers in the array by removing references to them.
function deletePoligono() {
	coordinate = [];
	initialize();
}

/** @this {google.maps.Polygon} */
function showArrays(event) {

	// Since this polygon has only one path, we can call getPath()
	// to return the MVCArray of LatLngs.
	var vertices = this.getPath();

	var contentString = '<b>Poligono</b><br>' +
	'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +
	'<br>';

	// Iterate over the vertices.
	for (var i =0; i < vertices.getLength(); i++) {
		var xy = vertices.getAt(i);
		contentString += '<br>' + 'Coordinate ' + i + ':<br>' + xy.lat() + ',' +
		xy.lng();
	}

	// Replace the info window's content and position.
	infoWindow.setContent(contentString);
	infoWindow.setPosition(event.latLng);

	infoWindow.open(map);
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>
  </head>
 
  <body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
  <!-- Importo questo menù da php in modo da uniformare le modifiche -->
  <?=MENU?>
  <div id="panel">
      <!--  <input onclick="clearMarkers();" type=button value="Hide Markers">
      <input onclick="creaPoligono();" type=button value="Show All Markers">
      <input onclick="clearPoligono();" type=button value="Nuovo poligono">-->
      <input onclick="deletePoligono();" type=button value="Cancella poligono">
      <input id="address" type="textbox" value="Verona, IT">
      <input type="button" value="Geocode" onclick="codeAddress()">
</div>
  </nav>
    

	   
	   <div id="map-canvas"></div>
	  

    

   
  </body>
</html>