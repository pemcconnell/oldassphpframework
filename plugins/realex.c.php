<?php

// GLOBALS
$xml;
$parentElements;
$currentElement;
$currentTSSCheck;
$TSSChecks = array();

class Realex
{
    static public   $bReady = false,
				    $sOrderId,
				    $sResponse,
				    $oResponseXML,
    				$sResponseResult,
    				$sResponseResultMsg;
    
    static private  $sMerchantId,
                    $sSecret,
				    $sAccount = 'internet',
				    $sRequestType = 'auth',
				    $acs_url,
				    $tdsData,
    				$sTimestamp;

    /**
     * Assign the configuration settings for Realex. The account name can also 
     * be set here if you wish to overwrite the default.
     * 
     * @param type $sMerchantId
     * @param type $sSecret
     * @param type $sAccount 
     */
    static public function prepare($sMerchantId, $sSecret, $sAccount = false)
    {
        self::$sMerchantId = $sMerchantId;
        self::$sSecret = $sSecret;
		if(!$sAccount) self::$sAccount = $sAccount;
        self::$bReady = true;
        self::$sTimestamp = strftime("%Y%m%d%H%M%S");
    }
    
    /**
     * Overwrite the default account name that the Realex transaction will be
     * applied to.
     * 
     * @param type $sAccount 
     */
    static public function changeAccount($sAccount)
    {
		global $CONSOLE;
		
		if(!self::$bReady) $CONSOLE->exception('Realex prepare method has not been called yet.');
		
		self::$sAccount = $sAccount;
    }
    
    /**
     * Overwrite the default request type that is sent to Realex. i.e. 
     * <request type='{!THIS!}'
     * 
     * @param type $sRequestType 
     */
    static public function changeRequestType($sRequestType)
    {
		global $CONSOLE;
		
		if(!self::$bReady) $CONSOLE->exception('Realex prepare method has not been called yet.');
		
		self::$sRequestType = $sRequestType;
    }
    
    /**
     * Creates the authentication hash that is passed to Realex.
     * 
     * @param array $array
     * @return type 
     */
    static public function createHash(array $array)
    {
		global $CONSOLE;
		
		if(!self::$bReady) $CONSOLE->exception('Realex prepare method has not been called yet.');
		
		while(list($k,$v) = each($array)) $$k = $v;
		
		mt_srand((double)microtime()*1000000);
		
		$tmp = self::$sTimestamp . "." . self::$sMerchantId . ".$orderid.$amount.$currency.$cardnumber";
		$md5hash = md5($tmp);
		$tmp = "$md5hash." . self::$sSecret;
		return md5($tmp);
    }
    
    /**
     * Generates the XML data which will be sent to Realex.
     * 
     * @global string $xml
     * @param array $array 
     */
    static public function createRequest(array $array)
    {
		global $xml, $CONSOLE;
		
		if(!self::$bReady) $CONSOLE->exception('Realex prepare method has not been called yet.');
		
		$array = self::validateFields($array);
		
		while(list($k,$v) = each($array)) $$k = $v;
	
		$md5hash = self::createHash($array);
	
		$xml = "<request type='" . self::$sRequestType . "' timestamp='" . self::$sTimestamp . "'>
	                    <merchantid>" . self::$sMerchantId . "</merchantid>
	                    <account>$account</account>
	                    <orderid>$orderid</orderid>
	                    <amount currency='$currency'>$amount</amount>
	                    <card>
	                        <number>$cardnumber</number>
	                        <expdate>$expdate</expdate>
	                        <type>$cardtype</type>
	                        <chname>$cardname</chname>
	                    </card>
	                    <autosettle flag='$autosettleflag'/>
	                    <md5hash>$md5hash</md5hash>
	                    <tssinfo>
	                        <address type='billing'>
	                            <country>ie</country>
	                        </address>
	                    </tssinfo>
	            </request>";
		#die('<pre>' . htmlspecialchars($xml) . '</pre>');
    }
    
    /**
     * Sends the request to Realex.
     * 
     * @global type $CONSOLE
     * @global type $xml 
     */
    static public function send()
    {
		global $CONSOLE, $xml;
		
		if(!self::$bReady) $CONSOLE->exception('Realex prepare method has not been called yet.');
		
		$RealMPIURL = "https://epage.payandshop.com/epage-remote.cgi";
		if(self::$sRequestType == '3ds-verifyenrolled')
		{
		    $RealMPIURL = "https://epage.payandshop.com/epage-3dsecure.cgi";
		}
		
		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "cDataHandler");
	
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $RealMPIURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "payandshop.com php version 0.9");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_INTERFACE, '89.185.145.29');
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
		if($response === false)
		{
		    $CONSOLE->exception('Curl error: ' . curl_error($ch));
		} else {
		    if (xml_parse($xml_parser, $response))
		    {
				/** SUCCESS! **/
				xml_parser_free($xml_parser);
				self::$sResponse = $response;
		    	self::$oResponseXML = simplexml_load_string(self::$sResponse);
		    	self::$sResponseResult = @(string)self::$oResponseXML->result;
		    	self::$sResponseResultMsg = @(string)self::$oResponseXML->message;
		    	if(self::$sRequestType == '3ds-verifyenrolled')
				{
				    self::$acs_url = self::$oResponseXML->url;
				} elseif(self::$sRequestType == '3ds-verifysig') {
				    $status = self::$oResponseXML->threedsecure->status;
				    if(($status == 'Y') || ($status == 'A'))
				    {
						self::$tdsData = array(
						    'status' => $status,
						    'cavv' => self::$oResponseXML->threedsecure->cavv,
						    'xid' => self::$oResponseXML->threedsecure->xid,
						    'eci' => self::$oResponseXML->threedsecure->eci
						);
					}
				}
		    } else {
				$CONSOLE->exception(sprintf("XML error: %s at line %d",
								    xml_error_string(xml_get_error_code($xml_parser)),
								    xml_get_current_line_number($xml_parser)));
	    	}
		}
    }
    
    /**
     * Assesses the data passed into the Realex class and assigns defaults if 
     * needs be. Will throw an exception if a required field is not present.
     * 
     * @global type $CONSOLE
     * @param array $a
     * @return array 
     */
    static private function validateFields(array $a)
    {
    	$r = true; $aMissing = array();
		$aRequiredFields = array(
		    'orderid', 'amount', 'cardnumber', 'cardtype', 'expdate'
		);
		foreach($aRequiredFields as $k)
		{
		    if(!isset($a[$k]))
		    {
			$aMissing[] = $k;
			$r = false;
		    }
		}
		// OPTIONAL FIELDS
		$aOptionalFields = array(
		    'autosettleflag' => 1,
		    'issue' => '',
			'currency' => 'GBP',
			'account' => 'internet'
		);
		foreach($aOptionalFields as $k => $v)
		{
		    if(!isset($a[$k]))
		    {
				$a[$k] = $v;
		    }
		}
		if(!$r)
		{
		    global $CONSOLE;
		    $CONSOLE->exception('Some required fields were missing: ' . implode(',', $aMissing));
		}
		// ADJUST ORDER ID (AS SENT TO REALEX) SO AS TO AVOID SAME TRANSACTION PROBLEM
		$a['orderid'] .= '_' . time() . '_' . rand(1,100);
		return $a;
    }
}


function startElement($parser, $name, $attrs)
{
	global $parentElements;
	global $currentElement;
	global $currentTSSCheck;

	if(is_array($parentElements))
	{
		array_push($parentElements, $name);
		$currentElement = join("_", $parentElements);
	}

	foreach ($attrs as $attr => $value) {
		if ($currentElement == "RESPONSE_TSS_CHECK" and $attr == "ID")
		{
			$currentTSSCheck = $value;
		}
		$attributeName = $currentElement."_".$attr;
		global $$attributeName;
		$$attributeName = $value;
	}
}

function cDataHandler($parser, $cdata)
{
	global $currentElement;
	global $currentTSSCheck;
	global $TSSChecks;

	if(trim($cdata))
	{
		if($currentTSSCheck != 0)
		{
			$TSSChecks["$currentTSSCheck"] = $cdata;
		}

		global $$currentElement;
		$$currentElement = $cdata;
	}
}

function endElement($parser, $name)
{
	global $parentElements;
	global $currentTSSCheck;

	$currentTSSCheck = 0;
	if(is_array($parentElements)) array_pop($parentElements);
}