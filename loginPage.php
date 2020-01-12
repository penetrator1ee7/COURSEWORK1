<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	</head>
    <style>
        body {
            background-image: url(img.jpg);
            background-size: 100% auto;
        }
    </style>
	<body>
    <div class="container">
        <div class="row justify-content-center">
            <b style="margin:80px">
	<?php
	if(isset($_GET['alert'])){
	if($_GET['alert']=='name') echo "We cannot find your login in our system.Make sure you have created your account
	(you can do in through the link below) and try again. \n";
	if($_GET['alert']=='pass') echo "Password you inserted doesn't match the one you chose while registration.";
	if($_GET['alert']=='database') echo "We have issues connecting to servers. Please try again a bit later or contact us by link below";
	}
	?>
            </b>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-5">
                <form method="get" action="auth.php" role="form" class="form-horizontal ">
          				<label>Your Gamer Nickname </label>
          				<input type="text" class="form-control" id="exampleInputEmail1" name="username" placeholder="nickname">
          				<label>Gamer password</label>
          				<input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
          		    	<br>
                        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
                <hr>  Do not have account??<a  href="regPage.php?alert=NULL">Create player account.</a>
                <br>
                You can <a href="updatePass.php"> update your password here. </a>
                </hr>
            </div>
        </div>
    </div>
    </body>
</html>