<?php
$pass=$_GET['password'];
$name=$_GET['username'];
$errFlag=0;
$foundFlag=1;
$time=date('y/m/d H:i:s',time()+60*60*24*30);
$tmptime=date('Y-m-d H:i:s',time());

$data = mysqli_connect('localhost', "root", '', 'coursework');
if ( !$data ) {
$errFlag=1;
header('HTTP/1.1 302 Redirect');
header('Location: regPage.php?alert=database');
}

$query="SELECT login FROM users where login='$name'";
$result=mysqli_query( $data,  $query );
if(!$result) echo mysqli_error($data) . ' line 7';
if(!null==mysqli_fetch_row($result)){
$foundFlag=0;
}
if($foundFlag){
$errFlag=1;
header('HTTP/1.1 302 Redirect');
header('Location: loginPage.php?alert=name');
}

if(!$errFlag){
$query="SELECT pass_h,pass_s,pass_exp FROM users Where login='$name'";
$result=mysqli_query( $data,  $query );
if(!$result) echo mysqli_error($data) . ' line 33';
$row=mysqli_fetch_row($result);
if($row[0]===hash('sha256',$pass.$row[1])) {
    if(strtotime($row[2])<strtotime($tmptime)){
        header('HTTP/1.1 302 Redirect');
        header('Location: updatePass.php');
    }
}else{
$errFlag=1;
header('HTTP/1.1 302 Redirect');
header('Location: loginPage.php?alert=pass');
}
}

if(!mysqli_query($data,$query)) echo mysqli_error($data) . ' line 43';
$result=mysqli_query($data,"SELECT id FROM users WHERE login = '$name'");
if(!$result) echo mysqli_error($data) . ' line 45';
$id=mysqli_fetch_row($result);

$result=mysqli_query($data,"select `verified`,`phone` from `users` where `id`='$id[0]'");
$twoFuck=mysqli_fetch_row($result);
$phone=$twoFuck[1];
$twoFuck=$twoFuck[0];
if($twoFuck==2){
    $secret=bin2hex(random_bytes(4));
    $result = mysqli_query($data, "update `users` set `p_secret`='$secret' where `id`='$id[0]'");
    if (!$result) $errFlag = 1;
    $request = 'https://smsc.ru/sys/send.php?login=penetrator1ee7&psw=p3n37r473&phones=' . $phone . '%40mail.ru&mes=verification code:' . $secret;
    file_get_contents($request);
    header('HTTP/1.1 302 Redirect');
    header('Location: 2Fuck.php?id='.$id[0]);
}else {
    if (!$errFlag) {
        if (isset($_COOKIE['Cookie'])) {
            $query = "SELECT auth_token,token_exp FROM tokens Where user_id='$id[0]'";
            $result = mysqli_query($data, $query);
            if (!$result) echo mysqli_error($data) . ' line 51';
            $foundFlag = 1;
            for ($row = mysqli_fetch_row($result); $row == !NULL; $row = mysqli_fetch_row($result)) {
                echo $row[0];
                if ($row[0] === $_COOKIE['Cookie']) {
                    $foundFlag = 0;
                    echo "found";
                    if (strtotime($row[1]) < strtotime($tmptime)) {
                        $query = "DELETE FROM tokens WHERE `auth_token`='$row[0]'";
                        $result = mysqli_query($data, $query);
                        if (!$result) echo mysqli_error($data) . ' line 64';
                        $foundFlag = 1;
                    }
                }
            }
            if ($foundFlag) {
                echo 'not';
                $cookie = bin2hex(random_bytes(32));
                $query = "INSERT INTO tokens (user_id,auth_token,token_exp) value ('$id[0]','$cookie','$time')";
                if (!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 70';
                setcookie("Cookie", $cookie, time() + 60 * 60 * 24 * 30);
                ECHO "COOKIE SET";
            }
        } else {
            $cookie = bin2hex(random_bytes(32));
            $query = "INSERT INTO tokens (user_id,auth_token,token_exp) value ('$id[0]','$cookie','$time')";
            if (!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 70';
            setcookie("Cookie", $cookie, time() + 60 * 60 * 24 * 30);
            ECHO "COOKIE SET";
        }
        header('HTTP/1.1 302 Redirect');
        header('Location: userPage.php');
    }
}
?>