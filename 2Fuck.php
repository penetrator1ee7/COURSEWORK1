<html>
<head>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<style>
    body {
        background-repeat: no-repeat;
        background-image: url(img.jpg);
        background-size: 100% auto;
    }


</style>
<body>
<?php
$data = mysqli_connect('localhost', "root", '', 'coursework');
$id=$_GET['id'];
$time=date('y/m/d H:i:s',time()+60*60*24*30);
$result=mysqli_query($data,"select `p_secret` from `users` where `id`='$id'");
$p_secret=mysqli_fetch_row($result)[0];
echo $id;
if(isset($_POST['secret'])){
    $secret=$_POST['secret'];
    if($secret==$p_secret){
        $cookie=bin2hex(random_bytes(32));
        $query="INSERT INTO tokens (user_id,auth_token,token_exp) value ('$id','$cookie','$time')" ;
        if(!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 70';
        setcookie("Cookie", $cookie, time()+60*60*24*30);
        header('HTTP/1.1 302 Redirect');
        header('Location: userPage.php');
    }else{
        echo 'wrong verification code.';
    }
}

echo'
<div class="container">
    <div class="row justify-content-center">
    <div class="col-3" >
    	<form method="post" action="2Fuck.php?id='.$id.'" role="form" class="form-horizontal ">
				<label>Verification code </label>
				<input type="text" class="form-control" name="secret" placeholder="Code">
				
            <button type="submit" class="btn btn-primary">Submit</button>
		</form>
</div>
</div>
</div>

';
?>
</body>
</html>
