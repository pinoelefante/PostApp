<?php
    define("ANDROID_DEVICE", 1);
    define("IOS_DEVICE", 2);
    define("WIN10_DEVICE", 3);
    require_once('config.php');

    function sendPushNotification($titolo,$corpo,$autore,$id_news,$devices)
    {
        $dev_android = array_filter($devices, function($item) {return $item["deviceOS"]==ANDROID_DEVICE;} );
        $dev_ios = array_filter($devices, function($item) {return $item["deviceOS"]==IOS_DEVICE;} );
        $dev_win10 = array_filter($devices, function($item) {return $item["deviceOS"]==WIN10_DEVICE;} );
        $anteprima = $corpo; //TODO tagliare il corpo della news
        sendPush_Android($id_news, $titolo, $anteprima, $autore, $dev_android);
        sendPush_iOS($titolo, $anteprima, $autore, $id_news, $dev_ios);
        sendPush_Windows($titolo, $anteprima, $autore, $id_news, $dev_win10);
    }
    function elaborateResponseAndroid($response)
    {
        echo $response;
    }
    function sendPush_Android($id_news, $titolo, $anteprima, $autore, $devices)
    {
        // prep the bundle
        $msg = array
        (
            'message'   => $anteprima,
            'title'     => $titolo,
            'id'        => $id_news,
            'author'    => $autore,
            'vibrate'   => 1,
            'sound'     => 1
        );
        $fields = array
        (
            'registration_ids'  => $devices,
            'data'              => $msg
        );
        $headers = array
        (
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        elaborateResponseAndroid($result);
    }
    function sendPush_iOS($titolo, $anteprima, $autore, $id_news, $devices)
    {

    }
    function sendPush_Windows($titolo, $anteprima, $autore, $id_news, $devices)
    {
        //https://arjunkr.quora.com/How-to-Windows-10-WNS-Windows-Notification-Service-via-PHP
    }

    function testAndroidEmulatorPush()
    {
        $registrationIds = array();
        array_push($registrationIds, "d6ZwtsVssq4:APA91bH55mQW7qaTG_rI-hE-R0z3TSs3YtaFReP7yuSfabgzyTk_RZGw5G-5MOb5dMsJ84FUOzLmfxS43GbNjfQe9ACnBSUrpS_OKyJfSnAF75Z33Iac_DjgPHR-aibQo05V3oN6f1mz");

        sendPush_Android(0, "titolo prova", "corpo prova", "autore prova", $registrationIds);
    }
    //testAndroidEmulatorPush();
?>