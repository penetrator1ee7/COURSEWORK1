<?php
$pass=$_GET['password'];
$newPass=$_GET['newPassword'];
$newPass2=$_GET['newPassword2'];
$name=$_GET['username'];
$errFlag=0;
$foundFlag=1;

if(!($newPass === $newPass2)){
    header('HTTP/1.1 302 Redirect');
    header('Location: updatePass.php?alert=newPass'); // pass shall be similar
    $errFlag=1;
}

$data = mysqli_connect('localhost', "root", '', 'coursework');
if ( !$data ) {
    mysql_close($data);
    $errFlag=1;
    header('HTTP/1.1 302 Redirect');
    header('Location:updatePass.php?alert=database.');
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
    header('Location: updatePass.php?alert=name');
}

if(!$errFlag){
    $query="SELECT pass_h,pass_s,pass_exp FROM users Where login='$name'";
    $result=mysqli_query( $data,  $query );
    if(!$result) echo mysqli_error($data) . ' line 33';
    $row=mysqli_fetch_row($result);
    if($row[0]===hash('sha256',$pass.$row[1])) {
    }else{
        $errFlag=1;
        header('HTTP/1.1 302 Redirect');
        header('Location: updatePass.php?alert=pass');
    }
}

if(!$errFlag){
    $tmpSalt=bin2hex(random_bytes ( 32));
    $tmpPass=hash('sha256',$newPass.$tmpSalt);
    $query="SELECT id FROM users Where login='$name'";
    $result=mysqli_query( $data,  $query );
    if(!$result) echo mysqli_error($data) . ' line 53';
    $row=mysqli_fetch_row($result)[0];
    $query="UPDATE `users` SET `pass_h` = '$tmpPass',`pass_s` = '$tmpSalt',`verified` = '0' WHERE `users`.`id` = '$row'";
    $result=mysqli_query( $data,  $query );
    if(!$result) echo mysqli_error($data) . ' line 57';   //redirect
}
header('HTTP/1.1 302 Redirect');
header('Location: userPage');
?>