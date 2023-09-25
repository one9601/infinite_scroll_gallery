<?php

if($wr_9){

  function get_vimeoThumb($id) {
    $apiurl = "http://vimeo.com/api/v2/video/".$id.".json";
    $curlsession = curl_init (); 
    curl_setopt ($curlsession, CURLOPT_URL, $apiurl); 
    curl_setopt ($curlsession, CURLOPT_POST, 0); 
    curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec ($curlsession); 
  
    $JSONreturns = json_decode($response);
    return  $JSONreturns[0]->thumbnail_large;
  }

  $wr_8 = get_vimeoThumb($wr_9);
}

$sql = " update $write_table set
wr_8 = '$wr_8'
where wr_id = '$wr_id' ";
    sql_query($sql);

?>