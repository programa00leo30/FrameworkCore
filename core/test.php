<?php

/* *******************************
 * esto sirve para probar distintas partes del core.
 * ***************************************************/
 
	error_reporting  (E_ALL); ini_set ('display_errors', true);

	include("html.php");

	$doc = new html("html");
	
	$doc->add( array( new html("head") , new html("body")) );
	$doc->head->add( new html("title" ) );
	$doc->head->title->SetContent("titulo");
	// $doc->head->SetContent("este contenido");
	$doc->body->add(new html("div",array("id"=>0) ));
	$doc->body->add(new html("div",array("id"=>1)));
	$doc->body->add(new html("div",array("id"=>2)));
	$doc->body->div->SetContent("el primer div que aparece");
	$t=0;
	
	// $doc->body->GetById("2")->SetContent("este es el ultimo div");
	foreach($doc->body as $k=>$v){
		
		echo "-->tipo:$k: ". $v->GetTab() . "conten:$v\n";
	}
	echo $doc;
