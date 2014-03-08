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
		$accessKeyId = "AKIAJOOEHP3JYETPUQDA";
		$associateTag = "bumybo06-20";
		$secretAccessKey = "H1oSOgIweSWSc8HANqPp1iz7cVc2QCWJjmX5lq5r";
	
		$timestamp = gmdate("Y-m-d\TH:i:s\Z");
		$timestamp = str_replace(":", "%3A", $timestamp);

		$canonicalQuery = "AWSAccessKeyId=".$accessKeyId;
		$canonicalQuery .= "&AssociateTag=".$associateTag;
		$canonicalQuery .= "&IdType=ISBN&ItemId=".$isbn_13;
		$canonicalQuery .= "&Operation=ItemLookup";
		$canonicalQuery .= "&ResponseGroup=Medium&SearchIndex=All";
		$canonicalQuery .= "&Service=AWSECommerceService";
		$canonicalQuery .= "&Timestamp=".$timestamp;
		
		$stringToSign="GET\n".$host."\n/onca/xml\n".$canonicalQuery;
		
		$signature = base64_encode(hash_hmac(
			"sha256",
			$stringToSign,
			$secretAccessKey,
			true));
		$signature = str_replace("+", "%2B", $signature);
		$signature = str_replace("=", "%3D", $signature);

		$signedURL = "https://".$host."/onca/xml?".$canonicalQuery."&Signature=".$signature;
	
		$result = file_get_contents($signedURL);
		return $result;
	}
}
