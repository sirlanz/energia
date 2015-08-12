<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<title>Polygon Arrays</title>
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
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>

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
	console.log(points);
	var point = points.getAt(0);
	var contentString = '<b>Opzioni</b><br>' +
	'<a href="#" onclick="changeColor(\'#FFFF00\');">Giallo</a><br />'+
	'<a href="#" onclick="changeColor(\'#FFFFFF\');">Grigio</a><br />'+
	'<a href="#" onclick="inviaPoligono(\'Ciao Ciao \');" id="inviapoligono">Invia il poligono</a>';

   	infoWindow.setContent(contentString);
	infoWindow.setPosition(point);
	infoWindow.open(map);
	
}

function inviaPoligono(points) {
	
	console.log(points);
	
	$.ajax({
		url: "setPoligono.php",
    	type: "post",
    	
    	data: { String: points},
    	success: function(data) {
    		console.log("e qui intercetti la conclusione...") ;
    	},
    	error: function( xhr, status, errorThrown ) {
            alert( "Sorry, there was a problem!" );
            console.log( "Error: " + errorThrown );
            console.log( "Status: " + status );
            console.dir( xhr );
        }
    	})
    	
	}

function setSessionPoligono(points){
	
	
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
<div id="panel">
      <!--  <input onclick="clearMarkers();" type=button value="Hide Markers">
      <input onclick="creaPoligono();" type=button value="Show All Markers">
      <input onclick="clearPoligono();" type=button value="Nuovo poligono">-->
      <input onclick="deletePoligono();" type=button value="Cancella poligono">
      <input id="address" type="textbox" value="Verona, IT">
      <input type="button" value="Geocode" onclick="codeAddress()">
</div>
<div id="map-canvas"></div>

</body>
</html>