<?php

const login='hihihiihhi';
const pass='hihihihihihiihih';
const mail='ihhihiihhihi';
/*
//$request='https://smsc.ru/sys/send.php?login='.login.'&psw='.pass.'&phones=89619547885&mes=test';
//file_get_contents($request);
$request='https://smsc.ru/sys/send.php?login='.login.'&psw='.pass.'&phones='.mail.'%40mail.ru&mes=secret pass:132&subj=Pass&sender='.mail.'%40mail.ru &mail=1';
file_get_contents($request);
*/

$errFlag=0;
$data = mysqli_connect('localhost', "root", '', 'coursework');
$cookie = $_COOKIE['Cookie'];
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $result = mysqli_query($data, "select `user_id` from `tokens` where `auth_token`='$cookie' ");
    $tmpId = mysqli_fetch_row($result)[0];
    $result = mysqli_query($data, "select `m_secret` from `users` where `id`='$tmpId' ");
    $secret = mysqli_fetch_row($result)[0];
    $stream = json_decode(file_get_contents("php://input"));
    if($stream->flag==-1) {
        if (isset($stream->secret)) {
            if ($stream->secret == $secret) {
                $result = mysqli_query($data, "update `users` set `verified`=1 where `id`=$tmpId");
                header("HTTP/1.0 200 OK");
            } else {
                header("HTTP/1.0 412 Precondition Failed");
            }
        } else {
            header("HTTP/1.0 400 Bad Request");
        }
    }elseif($stream->flag==1) {
        if (isset($stream->secret)) {
            $result = mysqli_query($data, "select `p_secret` from `users` where `id`='$tmpId'");
            $secret = mysqli_fetch_row($result)[0];
            if ($stream->secret == $secret) {
                $result = mysqli_query($data, "update `users` set `verified`=2 where `id`='$tmpId'");
                header("HTTP/1.0 200 OK");
            } else {
                header("HTTP/1.0 412 Precondition Failed");
            }
        } else {
            header("HTTP/1.0 400 Bad Request");
        }
    }
}elseif($_SERVER['REQUEST_METHOD']=='POST'){
    $secret=bin2hex(random_bytes(4));
    $result = mysqli_query($data, "select `user_id` from `tokens` where `auth_token`='$cookie' ");
    $tmpId = mysqli_fetch_row($result)[0];
    $stream = json_decode(file_get_contents("php://input"));
    if($stream->flag==-1) {
        $result = mysqli_query($data, "update `users` set `mail`='$stream->mail' where `id`='$tmpId'");
        if (!$result) $errFlag = 1;
        $result = mysqli_query($data, "update `users` set `m_secret`='$secret' where `id`='$tmpId'");
        if (!$result) $errFlag = 1;
        $request = 'https://smsc.ru/sys/send.php?login=' . login . '&psw=' . pass . '&phones=' . $stream->mail . '%40mail.ru&mes=verification code:' . $secret . '&subj=Pass&sender=' . mail . '%40mail.ru &mail=1';
        file_get_contents($request);
        if ($errFlag) {
            header("HTTP/1.0 200 OK");
        } else {
            header("HTTP/1.0 400 Bad Request");
        }
    }elseif($stream->flag==1){
        $result=mysqli_query($data,"update `users` set `phone`='$stream->mail' where `id`='$tmpId'");
        if (!$result) $errFlag = 1;
        $result=mysqli_query($data,"update `users` set `p_secret`='$secret' where `id`='$tmpId'");
        if (!$result) $errFlag = 1;
        $request='https://smsc.ru/sys/send.php?login='.login.'&psw='.pass.'&phones='.$stream->mail.'&mes=Your verification code: '.$secret;
        file_get_contents($request);
        if($errFlag){
            header("HTTP/1.0 200 OK");
        } else {
            header("HTTP/1.0 400 Bad Request");
        }
    }
}
