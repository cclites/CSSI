<?php

function sambaReceiveEnvelope($commsXML){
   	
	return  "<s:Envelope xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">".
    		"<s:Body>".
    		"<ReceiveRecords xmlns=\"http://adrconnect.mvrs.com/adrconnect/2013/04/\">" .
    		$commsXML .
    		"</ReceiveRecords>" .
    		"</s:Body>".
    		"</s:Envelope>";
   }