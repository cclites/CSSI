<?php

   function sambaEnvelope($command, $commsXML, $ordersXml){
   	
	return  "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">".
    		"<s:Body>".
    		"<SendOrders xmlns=\"http://adrconnect.mvrs.com/adrconnect/2013/04/\">" .
    		$commsXML . $ordersXml .
    		"</SendOrders>" .
    		"</s:Body>".
    		"</s:Envelope>";
   }

   function sambaInteractiveEnvelope($command, $commsXML, $ordersXml){
   	
	return  "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">".
    		"<s:Body>".
    		"<OrderInteractive xmlns=\"http://adrconnect.mvrs.com/adrconnect/2013/04/\">" .
    		$commsXML . $ordersXml .
    		"</OrderInteractive>" .
    		"</s:Body>".
    		"</s:Envelope>";
   }