<?php

if(isset($_COOKIE['Cookie'])){
    $data = mysqli_connect('localhost', "root", '', 'coursework');
    if ( !$data ) {
        $errFlag=1;
        header('HTTP/1.1 302 Redirect');
        header('Location: userPage.php?alert=database'); // cannot connect to mysql database
    }


    $tmpCookie=$_COOKIE['Cookie'];
    $query="DELETE FROM `tokens` where `auth_token`='$tmpCookie'";
    $result=mysqli_query($data,$query);
    if(!$result)echo mysqli_error($data) . ' line 16';
}

header('HTTP/1.1 302 Redirect');
header('Location: loginPage.php');


?>