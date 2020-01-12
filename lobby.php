<html>
<head> <meta charset =utf-8 http-equiv="Refresh" content="15">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<style>
    body {
        background-image: url(img.jpg);
        background-size: 100% auto;
    }
</style>
<?php
$errFlag=1;
if(isset($_COOKIE['Cookie'])) {
    $errFlag = 0;
    $data = mysqli_connect('localhost', "root", '', 'coursework');
    if (!$data) {
        $errFlag = 1;
        header('HTTP/1.1 302 Redirect');
        header('Location: loginPage.php?alert=database'); // cannot connect to mysql database
    }

    $tmpToken = $_COOKIE['Cookie'];
    $query = "SELECT `user_id` FROM `tokens` WHERE `auth_token`='$tmpToken'";
    $result = mysqli_query($data, $query);
    $tmpId = mysqli_fetch_row($result);
    if (!$tmpId) $errFlag = 1;
//check if banned
    $result=mysqli_query($data,"select `status` from `users` where `id`='$tmpId[0]'");
    $status=mysqli_fetch_row($result)[0];
    if($status=='bd'){
        header("HTTP/1.0 302 Redirect");
        header('Location: userPage.php');
    }
// on enter check for created game
    $result=mysqli_query($data,"select `game_id` from `game` where (`player1_id`='$tmpId[0]' or `player2_id`='$tmpId[0]') and (`game_status`='t1' or `game_status`='t2')");
    $gameExistance=mysqli_fetch_row($result);
    if($gameExistance){
        header('HTTP/1.1 302 Redirect');
        header('Location: game.html');
    }

    $query = 'CREATE TABLE if not exists gameLobby(
player_id INT UNSIGNED NOT NULL,
last_update timestamp ,
search_completed tinyint unsigned default 0
)';
    if (!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 24';
    $time = date('y/m/d H:i:s', time());
    $result = mysqli_query($data, "select `player_id` from `gameLobby` where `player_id`='$tmpId[0]' and `search_completed`='0'"); //first time to check for lobby
    $tmp = mysqli_fetch_row($result);
    if (!$tmp[0]) {
        $result = mysqli_query($data, "INSERT INTO `gameLobby` (player_id,last_update)value('$tmpId[0]','$time')");
        if (!$result) {
            echo mysqli_error($data) . ' line 29';
            $errFlag = 0;
        }
    } else {
        $result = mysqli_query($data, "update `gameLobby` set `last_update`='$time' where `player_id`='$tmpId[0]'");     //already in queue
        if (!$result) {
            echo mysqli_error($data) . ' line 32';
            $errFlag = 0;
        }
    }

// left the lobby-page
    $result = mysqli_query($data, "SELECT `last_update`FROM `gamelobby`");
    for ($row = mysqli_fetch_row($result); $row == !NULL; $row = mysqli_fetch_row($result)) {
        if (strtotime($row[0]) + 60 * 3 < time()) {
            $query = mysqli_query($data, "update `gameLobby` set `search_completed`='1' where `last_update`='$row[0]'");
            if (!$query) {
                echo mysqli_error($data) . ' line 41';
                $errFlag = 0;
            }
        }
    }

    if (!$errFlag) {
        $query = "create table if not exists game(
game_id int unsigned not null auto_increment primary key,
player1_id int unsigned not null,
player2_id int unsigned not null,
current_bet int unsigned not null default 0,
game_status varchar (2),
player1_score int unsigned default 0,
player2_score int unsigned default 0,
game_ended_in timestamp 
)";
        if (!mysqli_query($data, $query)) echo mysqli_error($data) . ' line 53';
        $result = mysqli_query($data, "select `player_id` from `gameLobby` where `search_completed`='0'");
        $player1_id = mysqli_fetch_row($result);
        $player2_id = mysqli_fetch_row($result);

        if ($player2_id[0]) {
            echo 'game done';// if game not created

            $checkerQuery = mysqli_query($data, "select `game_id` from `game` where (`player1_id`='$tmpId[0]' or `player2_id`='$tmpId[0]') and (`game_status`='t1' or `game_status`='t2')");
            $checker = mysqli_fetch_row($checkerQuery);
            if (!$checker[0]) {
                $result = mysqli_query($data, "insert into `game` (player1_id,player2_id,game_status)value('$player1_id[0]','$player2_id[0]','t1')");
                if (!$result) {
                    echo mysqli_error($data) . ' line 60';
                } else {
                    $result=mysqli_query($data,"update `gameLobby` set `search_completed`=1 where `player_id`='$player1_id[0]' or `player_id`='$player2_id[0]'");
                    if(!$result){$errFlag=1;echo mysqli_error($data);};
                    header('HTTP/1.1 302 Redirect');
                    header('Location: game.html');
                }
            }
        }
    }
}

if($errFlag){
    header('HTTP/1.1 302 Redirect');
    header('Location: loginPage.php?alert=errFlag');
}
    ?>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-6 " style="margin-top: 150px;">
            <p>Your Lobby is created.Please wait until another player joins.)</p>
    </div>
    </div>
</div>

</body>
</html>