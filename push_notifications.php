<?php
    require_once('config.php');
    test();
    function test()
    {
        $registrationIds = array();
        array_push($registrationIds, "d6ZwtsVssq4:APA91bH55mQW7qaTG_rI-hE-R0z3TSs3YtaFReP7yuSfabgzyTk_RZGw5G-5MOb5dMsJ84FUOzLmfxS43GbNjfQe9ACnBSUrpS_OKyJfSnAF75Z33Iac_DjgPHR-aibQo05V3oN6f1mz");

        // prep the bundle
        $msg = array
        (
            'message'       => "corpo prova",
            'title'         => "titolo prova",
            'vibrate'   => 1,
            'sound'     => 1
        );

        $fields = array
        (
            'registration_ids'  => $registrationIds,
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

        echo $result;
    }
    function sendPush_Android($id_news, $titolo, $anteprima, $autore, $devices)
    {
        $registrationIds = $devices;

        // prep the bundle
        $msg = array
        (
            'messaggio'       => $anteprima,
            'titolo'         => $titolo,
            'id'          => $id_news,
            'autore'          => $autore,
            'vibrate'   => 1,
            'sound'     => 1
        );

        $fields = array
        (
            'registration_ids'  => $registrationIds,
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

        return $result;
    }
    function sendPush_iOS($titolo, $anteprima, $autore, $devices)
    {

    }
    function sendPush_Windows($titolo, $anteprima, $autore, $devices)
    {
        //https://arjunkr.quora.com/How-to-Windows-10-WNS-Windows-Notification-Service-via-PHP
    }
?>