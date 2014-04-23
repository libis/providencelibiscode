<?php
	require_once(__CA_LIB_DIR__.'/core/Configuration.php');
	require_once('cURL.php');

	function getDigitoolThumbnailLink($digitoolUrl)
	{
		$thumbnailUrl = getDigitoolThumbnailUrl($digitoolUrl);

		if (strlen($thumbnailUrl) > 0) {			
			return '<img src="'.$thumbnailUrl.'" style="max-height: 500px;">';					
		}

		return "";
	}
         // wordt gebruikt om de afbeelding in PDF te tonen.
        // om meerdere urls op te halen
        function getDigitoolThumbnailsBase($digitoolUrls)
        {
            $tempImgs = array();
            
            if(count($digitoolUrls) > 0) {
	     foreach($digitoolUrls as $digitoolUrl) {
                array_push($tempImgs,getDigitoolThumbnailBase($digitoolUrl));
             }
            }
            return $tempImgs;
        }
        // wordt gebruikt om de afbeelding in PDF te tonen.
        // voor 1 url op te halen
        function getDigitoolThumbnailBase($digitoolUrl)
	{
		$thumbnailUrl = getDigitoolThumbnailUrl($digitoolUrl);
		
		if(strlen($thumbnailUrl) > 0)
		{
			$vo_http_client = new Zend_Http_Client();
			$config = array(
			'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
			'proxy_host' => 'icts-http-gw.cc.kuleuven.be',
			'proxy_port' => 8080,
			'timeout'    => 30
			);
			$vo_http_client->setConfig($config);
			$vo_http_client->setUri($thumbnailUrl);
		
			$vo_http_response = $vo_http_client->request();
			$thumb = $vo_http_response->getBody();
			$try = 0;
			
			while($try < 10)
			{
				if (!$vo_http_response->isError()){
					return '<img src="data:image/jpeg;base64,'.base64_encode($thumb).'">';
					break;
				} else {
					
					   //retry
					   sleep(1);
					   $vo_http_client->setUri($thumbnailUrl);
					   $vo_http_response = $vo_http_client->request();
					   
					   if (!$vo_http_response->isError()){
					   	return '<img src="data:image/jpeg;base64,'.base64_encode($thumb).'">';
					   } else {
					   $try++;
					}
				}
			}
		}

		return "";
	}
        // wordt gebruikt in de thumbnail view van de zoekresultaten vb ca_objects_results_thumbnail_html.php
        function getDigitoolThumbnailView($digitoolUrl)
	{
		$thumbnailUrl = getDigitoolThumbnailUrl($digitoolUrl);

		if (strlen($thumbnailUrl) > 0) {			
			return '<img src="'.$thumbnailUrl.'" width="172px">';					
		}

		return "";
	}

	function getDigitoolThumbnailUrl($digitoolUrl)
	{
		if (strlen($digitoolUrl) > 0) {
			$digitoolUrls = explode('_,_', $digitoolUrl);
			
			return $digitoolUrls[1];					
		}

		return "";
	}
        /*
         * GetDigitoolUrls wordt gebruikt om meerdere afbeeldingen uit een afbeelding te halen.
         * Als er maar één afbeelding is wordt er een string teruggeven anders een array
         */
        function getDigitoolUrls($digitoolUrl)
	{
		if (strlen($digitoolUrl) > 0) {
                    // gebruik een gemakkelijkere waarde om op te splitten
                    $temp_dollar = str_replace("_,_", "$", $digitoolUrl);
                    
                    $pattern = '/(\d+),/';
                    $temp_pipe = preg_replace($pattern, "|", $temp_dollar);
                    // En terug naar de lastige waarden om de andere functies terug te kunnen gebruiken
                    $temp_reverse = str_replace("$", "_,_", $temp_pipe);
                    
                    $digitoolUrls = explode('|', $temp_reverse);
		}
                
		 return $digitoolUrls;
	}
?>
