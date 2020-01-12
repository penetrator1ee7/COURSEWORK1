<?php
$pass=$_GET['password'];
$name=$_GET['username'];     //LOGIN SHALL BE UNIQUE
$pass2=$_GET['password2'];
$mail=$_GET['mail'];
$errFlag=0;
$salt=bin2hex(random_bytes ( 32));
$time=date('y/m/d H:i:s',time()+60*60*24*30);

if(!($pass === $pass2)){
header('HTTP/1.1 302 Redirect');
header('Location: regPage.php?alert=pass'); // pass shall be similar
$errFlag=1;
}

if(!$mail){
    header('HTTP/1.1 302 Redirect');
    header('Location: regPage.php?alert=mail'); // insert phone
    $errFlag=1;
}


$data = mysqli_connect('localhost', "root", '', 'coursework');
if ( !$data ) {
mysql_close($data);
echo "no";
$errFlag=1;
header('HTTP/1.1 302 Redirect');
header('Location: regPage.php?alert=connect'); // cannot connect to mysql database
}

$query="SELECT login FROM users where login= '$name'";
$result=mysqli_query( $data,  $query );
if(!$result) echo mysqli_error($data) . ' line 25';
$row= mysqli_fetch_row($result);
if ($row[0]!=null)
{$errFlag=1;
header('HTTP/1.1 302 Redirect');
header('Location: regPage.php?alert=name');   //LOGIN SHALL BE UNIQUE
}

$secret=bin2hex(random_bytes(4));
if(!$errFlag){
//mysqli_query($data,'DROP TABLE users');     //DROP
$query = 'CREATE TABLE if not exists users(
    id INT UNSIGNED NOT NULL auto_increment primary key,
    login VARCHAR(50) not null ,
    pass_h VARCHAR(64) not null,
    pass_s VARCHAR(64) not null,
    pass_exp timestamp ,
    verified tinyint unsigned default 0,
    mail varchar(64) default null,
    m_secret varchar(8) default null,
    phone VARCHAR(16) default null,
    p_secret varchar(8) default null,
    status VARCHAR(2) default "st"
)';

if(!mysqli_query($data,$query)) echo mysqli_error($data) . ' line 38';
$passHash=hash('sha256',$pass.$salt);
$query="INSERT INTO users (login,pass_h,pass_s,pass_exp,mail,m_secret) value ('$name','$passHash','$salt','$time','$mail','$secret')" ;
if(!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 41';
/*$test = "SELECT pass_exp FROM users";
$result  =  mysqli_query( $data,  $test );
if(!$result) echo mysqli_error($data) . ' line 37';
$row = mysqli_fetch_row($result);
echo $row[0];*/




//mysqli_query($data,'DROP TABLE tokens');  //DROP
$cookie=bin2hex(random_bytes(32));
$query = 'CREATE TABLE if not exists tokens(
user_id INT UNSIGNED NOT NULL,
auth_token varchar(64) NOT NULL,
token_exp timestamp
)';

if(!mysqli_query($data,$query)) echo mysqli_error($data) . ' line 65';
$result=mysqli_query($data,"SELECT id FROM users WHERE login = '$name'");
if(!$result) echo mysqli_error($data) . ' line 68';
$id=mysqli_fetch_row($result);

$query="INSERT INTO tokens (user_id,auth_token,token_exp) value ('$id[0]','$cookie','$time')" ;
if(!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 70';
setcookie("Cookie", $cookie, time()+60*60*24*30);
ECHO "COOKIE SET";

//$result=mysqli_query($data,"SELECT auth_token FROM tokens");
//if(!$result) echo mysqli_error($data) . ' line 75';
//$test=mysqli_fetch_row($result);
//echo $test[0] . "\n";
//echo $cookie;

header('HTTP/1.1 302 Redirect');
header('Location: mailver.html');
}

?>