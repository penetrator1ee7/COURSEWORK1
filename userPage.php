<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <style>
        body {
            background-image: url(img.jpg);
            background-size: 100% auto;
        }.alert{
            margin: 200px;
                 }
    </style>
	<body>
    <div class="container">
        <div class="alert ">
            <b >
    <?php
    $errFlag=1;
    if(isset($_COOKIE['Cookie'])) {
        $data = mysqli_connect('localhost', "root", '', 'coursework');
      if ( !$data ) {
          $errFlag = 1;
          header('HTTP/1.1 302 Redirect');
          header('Location: loginPage.php?alert=database'); // cannot connect to mysql database
      }

      if (isset($_GET['alert'])) {
          if ($_GET['alert'] === 'database') echo "We have issues connecting to servers. Please try again a bit later or contact us by link below";
      }

      $tmpToken=$_COOKIE['Cookie'];
      $query="SELECT `user_id` FROM `tokens` WHERE `auth_token`='$tmpToken'";
      $result=mysqli_query($data,$query);
      $tmpId=mysqli_fetch_row($result);
      if($tmpId){
          $errFlag=0;
        $query="SELECT `login` FROM `users` WHERE `id`='$tmpId[0]'";
        $result=mysqli_query($data,$query);
          if(!$result) $errFlag=1;
          $tmpName=mysqli_fetch_row($result);
      }

      $result=mysqli_query($data,"select `verified` from `users` where `id`=$tmpId[0]");
      $ver=mysqli_fetch_row($result)[0];
      if($ver==0){
          header('HTTP/1.1 302 Redirect');
          header('Location: mailver.php');
      }

    }

    if(isset($_COOKIE['game'])) {
        $result = mysqli_query($data, "select `game_status`,`player1_id`,`player2_id` from `game` where `player1_id`='$tmpId[0]' or `player2_id`='$tmpId[0]'");
        while($rest=mysqli_fetch_row($result)){
            if($rest[0]=='w1') {
                if($rest[1]==$tmpId[0]){
                    $status='You won the game.';
                }elseif($rest[2]==$tmpId[0]){
                    $status='You lost the game';
                }
            }elseif($rest[0]=='w2'){
                if($rest[1]==$tmpId[0]){
                    $status='You lost the game';
                }elseif($rest[2]==$tmpId[0]){
                    $status='You won the game.';
                }
            }
        }
        echo $status;
        setcookie('game');
    }

    if($errFlag){
        header('HTTP/1.1 302 Redirect');
        header('Location: loginPage.php?errFlag=userPage');
    }
    ?>
            </b>
        </div>
    </div>

    <div class="container">
        <div class="col-4"></div>
        <div class="row justify-content-center" >
            <div class="col-4" style="margin-top: -150px;">
            <p >Hello, user</p>
            <b>
                <?php
                echo $tmpName[0];
                ?>
            </b><br><br>
                <?php
                $result=mysqli_query($data,"select `status` from `users` where `id`='$tmpId[0]'");
                $userStatus=mysqli_fetch_row($result)[0];
                if($userStatus=='st'){
                    echo
                    '<button type="button" class="btn btn-primary" onclick="document.location.href =`lobby.php`">Click here to start the game!</button><br>
                    ';

                }elseif($userStatus=='ad'){
                    echo (
                    '<button type="button" class="btn btn-primary" onclick="document.location.href =`lobby.php`">Click here to start the game!</button><br>
                     <button type="button" class="btn btn-dark" onclick="document.location.href =`admpage.html`">Admin page</button><br>'
                );
                }elseif ($userStatus=='bd'){
                    echo 'You are banned.<br>';
                }
                if($ver==1){
                    echo'<button type="button" class="btn btn-secondary" onclick="document.location.href =`mailver.html`">Bind your phone to account.</button><br>
                    ';
                }
                ?>

                <a href="scoreboard.php">Scoreboard</a><br>
            <a href="logOut.php">Click here to log out</a><br>
                <br><br>
                <a href="https://vk.com/write242015264">Contact me</a>
        </div>
        </div>
    </div>

	</body>
    </html>