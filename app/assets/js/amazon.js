

/********* Custom Code Start ******************/
/**
 * Copyright (c) 2008 Amazon.com, Inc. or its affiliates.  All Rights Reserved.
 */



var de_ws_url = "//ws-eu.amazon-adsystem.com";
var us_ws_url = "//ws-na.amazon-adsystem.com";
var fr_ws_url = "//ws-eu.amazon-adsystem.com";
var ca_ws_url = "//ws-na.amazon-adsystem.com";
var cn_ws_url = "//ws-cn.amazon-adsystem.com";
var gb_ws_url = "//ws-eu.amazon-adsystem.com";
var jp_ws_url = "//ws-fe.amazon-adsystem.com";


function escapeParam(param){
	param = encodeURIComponent(param);
	param=param.replace("+", "%20");
	param=param.replace("/", "%2F");
	return param;
}

var amzn_MarketPlace =  null;
if( amzn_MarketPlace == null ||  amzn_MarketPlace == "" ){
	amzn_MarketPlace= "US";
}

var amzn_ws_url = us_ws_url;
switch(amzn_MarketPlace){
	case "US": amzn_ws_url = us_ws_url;break;
	case "DE": amzn_ws_url = de_ws_url;break;
	case "GB": amzn_ws_url = gb_ws_url;break;
	case "CA": amzn_ws_url = ca_ws_url;break;
	case "CN": amzn_ws_url = cn_ws_url;break;
	case "FR": amzn_ws_url = fr_ws_url;break;
	case "JP": amzn_ws_url = jp_ws_url;break;
	default: amzn_ws_url = us_ws_url;
}

if(typeof wsPreview != 'undefined'){
    amzn_ws_url = amzn_ws_url.replace(/^http:\/\//, "https://");
}

var amzn_ws_path = amzn_ws_url + "/widgets/q?";


/********* Custom Code End ******************/
