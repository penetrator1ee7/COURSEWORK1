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
                        if($_GET['alert']=='name') echo "Sorry,but we're convinced, that all the users shall have unique logins aka nicknames.
        	Please choose the login which is not in our system yet.";
                        if($_GET['alert']=='mail') echo "Insert your mail address please.";
                        if($_GET['alert']=='pass') echo "Passwords you inserted shall be similar to each other.";
                        if($_GET['alert']=='database') echo "We have issues connecting to servers. Please try again a bit later or contact us by link below";
                    }
                    ?>
                </b>
            </div>
<div class="container">
    <div class="row justify-content-center">
    <div class="col-3" >
    	<form method="get" action="create.php" role="form" class="form-horizontal ">
				<label>Your Gamer Nickname </label>
				<input type="text" class="form-control" id="exampleInputEmail1"  name="username" placeholder="Nickname">
				<label>Your gamer password </label>
				<input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
			    <label>Type your password again.</label>
			    <input type="password" class="form-control" id="exampleInputPassword1" name="password2" placeholder="Password">
                <label>Your mail address </label>
                <input type="text" class="form-control" id="exampleInputEmail1"  name="mail" placeholder="Mail Address">

            <button type="submit" class="btn btn-primary">Submit</button>
		</form>
    Already have an account?<a href="loginPage.php">Log in.</a>
</div>
</div>
</div>

	</body>
	</html>