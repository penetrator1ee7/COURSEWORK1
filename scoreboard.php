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
<div class="table-responsive">
<table class="table table-hover table-bordered">
    <thead>
    <tr>
        <th>Player 1</th>
        <th>Player 2</th>
        <th>Game result</th>
        <th>Player 1 score</th>
        <th>Player 2 score</th>
        <th>Date of game</th>
    </tr>
    </thead>
<?php
$data = mysqli_connect('localhost', "root", '', 'coursework');
if (!$data) {
    header('HTTP/1.1 302 Redirect');
    header('Location: loginPage.php?alert=database'); // cannot connect to mysql database
}

$result=mysqli_query($data,"select `player1_id`,`player2_id`,`game_status`,`player1_score`,`player2_score`,`game_ended_in` from `game`");
while ($userData=mysqli_fetch_row($result)){
    echo '<tr>';
    $rest=mysqli_query($data,"select `login` from `users` where `id`='$userData[0]'");
    $name1=mysqli_fetch_row($rest);
    echo "<td>".$name1[0]."</td>";
    $rest=mysqli_query($data,"select `login` from `users` where `id`='$userData[1]'");
    $name2=mysqli_fetch_row($rest);
    echo "<td>".$name2[0]."</td>";
    if($userData[2]=='t1'){
        $status='Player 1 turn';
    }elseif($userData[2]=='t2'){
        $status='Player 2 turn';
    }elseif($userData[2]=='w1'){
        $status='Player 1 won';
    }elseif($userData[2]=='w2'){
        $status='Player 2 won';
    }
    echo "<td>".$status."</td>";
    echo "<td>".$userData[3]."</td>";
    echo "<td>".$userData[4]."</td>";
    echo "<td>".$userData[5]."</td>";
    echo '</tr>';
}

?>
</table>
</div>
</body>
</html>