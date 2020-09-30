<?php

function inOrderWrapper($inOrder){
	
	return '<inOrder xmlns:i="http://www.w3.org/2001/XMLSchema-instance">' .
    	   '<OrderXml>' .
    	   $inOrder .
    	   '</OrderXml>' . 
		   '</inOrder>';
}
