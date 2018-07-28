<?php
define('SEPARATOR', '。');

$config = require 'config.php';
$json = json_decode(file_get_contents('php://input'));

$text = str_replace(["\r", "\n"], '', $json->text);
$text = preg_replace('/ {2,}/', SEPARATOR, $text);
foreach (['?', '？', '!', '！'] as $from) {
    $text = str_replace($from, SEPARATOR, $text);
}

$parts = explode(SEPARATOR, $text);
$summaries = [];
$resultSummaries = [];

foreach ($parts as $i => $part) {
    $part = trim($part);
    if ($part && mb_strlen($part) < 200) {
        $summaries[] = $part;
    }

    if (count($summaries) >= 10
        || ($summaries && $i == count($parts) - 1)
    ) {
        $data = [
            'apikey' => $config['apikey'],
            'sentences' => join(SEPARATOR, $summaries) . SEPARATOR,
        ];
        $curl = curl_init('https://api.a3rt.recruit-tech.co.jp/text_summarization/v1/');

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $response = json_decode(curl_exec($curl));
        curl_close($curl);

        $resultSummaries[] = join(SEPARATOR, $response->summary) . SEPARATOR;
        $summaries = [];
    }
}

$result = [
    'summary' => join('', $resultSummaries),
];
echo json_encode($result);