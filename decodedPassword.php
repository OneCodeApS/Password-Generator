<?php

if (isset($_GET['GUID'])) {
    $key = $_GET['GUID'];
    $firebaseUrl = "https://password-generator-b5d55-default-rtdb.europe-west1.firebasedatabase.app/" . $key . "/";

    function getFirebaseData($key) {
        global $firebaseUrl;
        $url = $firebaseUrl . $key . ".json";
        $response = file_get_contents($url);
        $value = json_decode($response, true);
        return $value;
    }

    function deleteFirebaseData($key) {
        global $firebaseUrl;
        $url = $firebaseUrl . $key . ".json";
        $options = [
            'http' => [
                'method' => 'DELETE',
                'header' => "Content-Type: application/json\r\n"
            ]
        ];
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    }

    $decodedValue = getFirebaseData($key);
/* 
    if (empty($decodedValue)) {
        echo "The password could not be retrieved. It may have already been retrieved earlier or it might have expired.";
        exit;
    } */

    deleteFirebaseData($key);
    echo $decodedValue;

    exit;
}



?>