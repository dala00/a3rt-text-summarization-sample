<?php
$config = require 'config.php';
$json = json_decode(file_get_contents('php://input'));
$data = [
    'apikey' => $config['apikey'],
    'sentences' => $json->text,
];
$curl = curl_init('https://api.a3rt.recruit-tech.co.jp/text_summarization/v1/');

curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

$response = json_decode(curl_exec($curl));
$result = [
    'summary' => join($response->summary),
    'original' => $response,
];
echo json_encode($result);