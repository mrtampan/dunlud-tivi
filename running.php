<?php


echo "Download Siaran TV gratis bro \n";

echo "Pilih Menu: \n";
echo "[1] tampil nomor channel \n";
echo "[2] jadwal siaran \n";
echo "[3] Download Siaran berdasarkan judul (masih error) \n";
echo "[4] Download Siaran secara manual \n";

$choose = trim(fgets(STDIN));


if($choose == 1){
    $channelData = array();
    for($i = 1; $i <= 3; $i++){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://dens.tv/tvpage_octo/channelgen/".$i);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        
        $jsondata = json_decode($output, true)["data"];

        for($ii = 0; $ii < count($jsondata); $ii++){
            array_push($channelData, $jsondata[$ii]);
        }

        curl_close($ch); 
    
    }


    $showChannel = "";
    for($k = 0; $k < count($channelData); $k++){
        $showChannel .= '[' . $channelData[$k]["seq"] .'] ' . $channelData[$k]["title"] . "\n\r";
    }

    echo $showChannel;
}else if($choose == 2){

    echo "Masukan nomor channel: \n";
    $channel = trim(fgets(STDIN));
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "https://dens.tv/tvpage_octo/epgchannel2/0/".$channel);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    $jsondata = json_decode($output, true)["data"];

    curl_close($ch); 

    $showTitle = "";
    for($k = 0; $k < count($jsondata); $k++){
        $showTitle .= $jsondata[$k]["title"] . "(Waktu penayangan:" . $jsondata[$k]["starttime"] . " - " . $jsondata[$k]['endtime'] . ')' . "\n\r";
    }

    echo $showTitle;
}else if($choose == 3){
    echo "Masukan nomor channel: \n";
    $channel = trim(fgets(STDIN));
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, "https://dens.tv/tvpage_octo/epgchannel2/0/".$channel);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch); 
    $jsondata = json_decode($output, true)["data"];

    curl_close($ch); 

    $showTitle = "";
    for($k = 0; $k < count($jsondata); $k++){
        $showTitle .= '[' . $k . '] ' . $jsondata[$k]["title"] . "\n\r";
    }

    echo $showTitle;

    echo "Pilih Siaran Yang Mau di Download: \n";
    $seqment = trim(fgets(STDIN));

    $startTime = strtotime($jsondata[$seqment]["starttime"] . "GMT+07:00");
    $endTime  = strtotime($jsondata[$seqment]["endtime"] . "GMT+07:00");

    $playUrl = explode("&",$jsondata[$seqment]["play"])[0] . '&starttime=' . $startTime . '&endtime=' . $endTime;

    echo "Pilih Ekstensi: \n";
    echo "mp4 \n";
    echo "m3u8 \n";
    $extensi = trim(fgets(STDIN));
    
    $myfile = fopen("dunlud.sh", "w") or die("Unable to open file!");
    $txt = "M3U8_URL='$playUrl' \n";
    fwrite($myfile, $txt);
    $txt = 'echo $M3U8_URL' . "\n";
    fwrite($myfile, $txt);
    if($extensi == 'mp4'){
        $txt = 'ffmpeg -i $M3U8_URL -c copy -bsf:a aac_adtstoasc dunlud.mp4 ';
    }elseif($extensi == 'm3u8'){
        $txt = 'youtube-dl $M3U8_URL';
    }else{
        $txt = 'ffmpeg -i $M3U8_URL -c copy -bsf:a aac_adtstoasc dunlud.mp4 ';        
    }


    fwrite($myfile, $txt);
    fclose($myfile);
    echo shell_exec("bash dunlud.sh" );

    
}else if($choose == 4){
    $channelData = array();
    for($i = 1; $i <= 3; $i++){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://dens.tv/tvpage_octo/channelgen/".$i);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        
        $jsondata = json_decode($output, true)["data"];

        for($ii = 0; $ii < count($jsondata); $ii++){
            array_push($channelData, $jsondata[$ii]);
        }

        curl_close($ch); 
    
    }

    $showChannel = "";
    for($k = 0; $k < count($channelData); $k++){
        $showChannel .= '[' . $k .'] ' . $channelData[$k]["title"] . "\n\r";
    }

    echo $showChannel;

    echo "Pilih Siaran Yang Mau di Download: \n";
    $seqment = trim(fgets(STDIN));

    $playUrl = $channelData[$seqment]['playurl'];
    
    echo $playUrl;

    echo shell_exec("youtube-dl " . $playUrl );


}


?>