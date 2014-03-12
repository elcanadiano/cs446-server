<?php

/**
 * Amazon Product Advertising API library. Contains call with given ISBN.
 * Function modified from interactive HTML/JavaScript Amazon API call
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author	Khalim Husain
 */

class Amazon_lib {

	function get_info_from_amazon($isbn_13)
	{
		$host = "webservices.amazon.ca";
	
		$timestamp = gmdate("Y-m-d\TH:i:s\Z");
		$timestamp = str_replace(":", "%3A", $timestamp);

		$canonicalQuery = 'AWSAccessKeyId='.config_item('amazon_access_key')
		. "&AssociateTag=" . config_item('amazon_associate_tag')
		. "&IdType=ISBN&ItemId=" . $isbn_13
		. "&Operation=ItemLookup"
		. "&ResponseGroup=Medium&SearchIndex=All"
		. "&Service=AWSECommerceService"
		. "&Timestamp=" . $timestamp;
		
		$stringToSign="GET\n".$host."\n/onca/xml\n".$canonicalQuery;
		
		$signature = base64_encode(hash_hmac(
			"sha256",
			$stringToSign,
			config_item('amazon_secret_key'),
			true));
		$signature = str_replace("+", "%2B", $signature);
		$signature = str_replace("=", "%3D", $signature);

		$signedURL = "https://".$host."/onca/xml?".$canonicalQuery."&Signature=".$signature;
	
		$result = file_get_contents($signedURL);

		// If there is an error, return FALSE.
		if (!$result || strpos($result, '<Errors>'))
		{
			return FALSE;
		}

		return $result;
	}
}
