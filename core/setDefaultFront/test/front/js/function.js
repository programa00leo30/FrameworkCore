/*$(document).ready(function() {
  var refreshId = setInterval(function() {
     $("#datos_mapa").load('css3.php?randval='+ Math.random());
    }, 120000);
  $.ajaxSetup({ cache: false });
});*/


$(function () {
 $('[data-toggle="tooltip"]').tooltip()
})

function mostrarInfo(cod){
 TEXT_CARGANDO = '<div id="cargando"> </div> <div class="spinner"> </div>';
 if (window.XMLHttpRequest)
   {// code for IE7+, Firefox, Chrome, Opera, Safari
   xmlhttp=new XMLHttpRequest();
   }
 else
   {// code for IE6, IE5
   xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
 xmlhttp.onreadystatechange=function()
   {
     if (xmlhttp.readyState==4 && xmlhttp.status==200)
     {
       document.getElementById("datos").innerHTML=xmlhttp.responseText;
     }else{
     document.getElementById("datos").innerHTML = TEXT_CARGANDO;
     }
   }
 switch(cod) {
 case '0':{
         xmlhttp.open("POST","partidos/partidos_0.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
   case '51':{
         xmlhttp.open("POST","partidos/partidos_51.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '29':{
      xmlhttp.open("POST","partidos/partidos_29.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
   case '19':{
      xmlhttp.open("POST","partidos/partidos_19.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '21':{
      xmlhttp.open("POST","partidos/partidos_21.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '32':{
      xmlhttp.open("POST","partidos/partidos_32.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '28':{
      xmlhttp.open("POST","partidos/partidos_28.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '37':{
      xmlhttp.open("POST","partidos/partidos_37.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '31':{
      xmlhttp.open("POST","partidos/partidos_31.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '26':{
      xmlhttp.open("POST","partidos/partidos_26.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '47':{
      xmlhttp.open("POST","partidos/partidos_47.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '23':{
      xmlhttp.open("POST","partidos/partidos_23.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '48':{
      xmlhttp.open("POST","partidos/partidos_48.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '30':{
      xmlhttp.open("POST","partidos/partidos_30.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '24':{
      xmlhttp.open("POST","partidos/partidos_24.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '41':{
      xmlhttp.open("POST","partidos/partidos_41.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '44':{
      xmlhttp.open("POST","partidos/partidos_44.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '22':{
      xmlhttp.open("POST","partidos/partidos_22.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '45':{
      xmlhttp.open("POST","partidos/partidos_45.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '42':{
      xmlhttp.open("POST","partidos/partidos_42.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '34':{
      xmlhttp.open("POST","partidos/partidos_34.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '38':{
      xmlhttp.open("POST","partidos/partidos_38.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '49':{
      xmlhttp.open("POST","partidos/partidos_49.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '52':{
      xmlhttp.open("POST","partidos/partidos_52.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
 case '36':{
      xmlhttp.open("POST","partidos/partidos_36.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
       break;
 }
   default:{
         xmlhttp.open("POST","partidos/partidos_sindatos.php",true);
     xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
     xmlhttp.send("codigo="+cod);
 }
}
 }
