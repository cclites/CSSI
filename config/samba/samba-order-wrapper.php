<?php

function orderWrapper($inOrder){
	
	return '<inOrders xmlns:i="http://www.w3.org/2001/XMLSchema-instance">' .
		   '<OrderEntity>' .
    	   '<OrderXml>' .
    	   $inOrder .
    	   '</OrderXml>' . 
		   '</OrderEntity>' .
		   '</inOrders>';
}
