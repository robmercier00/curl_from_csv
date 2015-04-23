<?php

class Curlcsv {
	function csv_to_array($filename='', $delimiter=',') {
	    if(!file_exists($filename) || !is_readable($filename))
	        return FALSE;
	    
	    $header = NULL;
	    $data = array();
	    if (($handle = fopen($filename, 'r')) !== FALSE)
	    {
	        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
	        {
	            if(!$header){
	                $header = $row;
	                #$data[] = array_combine($header, $row);
	            }
	            else{
	                $data[] = array_combine($header, $row);
	            }
	        }
	        fclose($handle);
	    }
	    
	    return $data;
	}

	function do_http_post($url) {
	    echo "\nIn do_http_post() function:\n";
	    //open connection
	    $ch = curl_init();
	    //set the url
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // Tell curl to use HTTP POST. must be left unset if you need to use "multipart/form-data"
	    curl_setopt($ch, CURLOPT_POST, true);
	    //Don't need headers returned. set to true if you want http status code
	    curl_setopt($ch, CURLOPT_HEADER, true);
	    //but want the response returned
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    //turn off cert verificaiton
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    //set timeout in seconds
	    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	    //execute post
	    $response = curl_exec($ch);
	    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if ($response === false) {
	        $this->error = true;
	        if ($for_posting != '')
	            $this->sql_posting_error = true;
	        $response = 'http post failure: ' . curl_error($ch);
	        echo($response);
	    }
	    $return_val = $http_status . ' | '. str_replace('<script>','',str_replace('</script>','',str_replace('<script type=\'text/javascript\'>','',str_replace('document.location.href = ','',strip_tags($response,'<p><br><br /><br/>')))));
	    //close connection
	    curl_close($ch);
	    return $return_val;
	}
}

$curl_from_csv = new Curlcsv();
$upload_file = 'encoded_urls.csv';

$csv = csv_to_array($upload_file);
foreach($csv as $post_msg) {
	$post_log = $curl_from_csv->do_http_post($post_msg['msg'])
	echo $post_msg['msg'] . "\n" . $post_log . "\r\n";
}

?>