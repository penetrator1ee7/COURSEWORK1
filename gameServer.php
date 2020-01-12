<?php

if(isset($_COOKIE['Cookie'])) {
    setcookie('game', 1, time() + 60 * 5);
    $errFlag = 0;
    $time = date('y/m/d H:i:s', time());
    $YouWin = 0;
    $YourTurn = 0;

    $tmpname = "Max";
    $YourScore = 'aaa';
    $OpScore = 'aaa';
    $Bet = 0;
    $Rolled = 0;

    $data = mysqli_connect('localhost', "root", '', 'coursework');
    $cookie = $_COOKIE['Cookie'];

    //getting user id
    $result = mysqli_query($data, "select `user_id` from `tokens` where `auth_token`='$cookie'");
    $tmpId = mysqli_fetch_row($result)[0];
    if ($tmpId) {
    } else {
        $errFlag = 1;
    }


    //getting game id
    $result = mysqli_query($data, "select `game_id` from `game` where (`player1_id`='$tmpId' or `player2_id`='$tmpId') and (`game_status`='t1' or `game_status`='t2' )");
    $gameId = mysqli_fetch_row($result)[0];


        //getting user number(1 or 2)
        $result = mysqli_query($data, "select `player1_id` , `player2_id` from `game` where `game_id`='$gameId'");
    $possiblePId = mysqli_fetch_row($result);
    if ($possiblePId[0] == $tmpId) {
        $userNumber = 1;
    } elseif ($possiblePId[1] == $tmpId) {
        $userNumber = 2;
    }

    //wincondition
    $result = mysqli_query($data, "select `player1_score` , `player2_score` from `game` where `game_id`='$gameId'");
    $Scores = mysqli_fetch_row($result);
    if ($Scores[0] > 99) {
        $result = mysqli_query($data, "update `game` set `game_status`='w1',`game_ended_in`='$time' where `game_id`='$gameId'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        if ($userNumber == 1) {
            $YouWin = 1;
        } else {
            $YouWin = -1;
        }
    } elseif ($Scores[1] > 99) {
        $result = mysqli_query($data, "update `game` set `game_status`='w2',`game_ended_in`='$time' where `game_id`='$gameId'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        if ($userNumber == 2) {
            $YouWin = 1;
        } else {
            $YouWin = -1;
        }
    }

    //game status
    $YourTurn = 0;
    $result = mysqli_query($data, "select `game_status` from `game` where `game_id`='$gameId'");
    $gameStatus = mysqli_fetch_row($result)[0];
    if ($gameStatus == 't1') {
        if ($userNumber == 1) {
            $YourTurn = 1;
        }
    } elseif ($gameStatus == 't2') {
        if ($userNumber == 2) {
            $YourTurn = 1;
        }
    }

    //another user
    if ($userNumber == 1) {
        $anotherUser = 't2';
    } elseif ($userNumber == 2) {
        $anotherUser = 't1';
    }
    if (!isset($anotherUser)) {
        $errFlag = 1;
    }

    //time update
    if (!$errFlag) {
        $result = mysqli_query($data, "update `gameLobby` set `last_update`='$time' where `player_id`='$tmpId'");
        if (!$result) {
            echo mysqli_error($data) . ' line 32';
            $errFlag = 0;
        }
    }

    //userScore
    if ($userNumber == 1) {
        $YourScore = $Scores[0];
        $OpScore = $Scores[1];
    } elseif ($userNumber == 2) {
        $YourScore = $Scores[1];
        $OpScore = $Scores[0];
    }

    //username
    $result = mysqli_query($data, "select `login` from `users` where `id`='$tmpId'");
    $tmpname = mysqli_fetch_row($result)[0];

    //current bet
    $result = mysqli_query($data, "select `current_bet` from `game` where `game_id`='$gameId'");
    if (!$result) {
        $errFlag = 1;
        echo mysqli_error($data);
    };
    $Bet = mysqli_fetch_row($result)[0];

    if ($userNumber == 1) {
        $result = mysqli_query($data, "select `player2_id` from `game` where `game_id`='$gameId'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        $id2 = mysqli_fetch_row($result)[0];
        $result = mysqli_query($data, "select `last_update` from `gamelobby` where `player_id`='$id2'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        $user2update = mysqli_fetch_row($result);
        if (strtotime($user2update[0]) + 60 + 1.5 < time()) {
            $result = mysqli_query($data, "update `game` set `game_status`='w1',`game_ended_in`='$time' where `game_id`='$gameId'");
            if (!$result) {
                $errFlag = 1;
                echo mysqli_error($data);
            };
        }
    } else {
        $result = mysqli_query($data, "select `player1_id` from `game` where `game_id`='$gameId'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        $id1 = mysqli_fetch_row($result)[0];
        $result = mysqli_query($data, "select `last_update` from `gamelobby` where `player_id`='$id1'");
        if (!$result) {
            $errFlag = 1;
            echo mysqli_error($data);
        };
        $user1update = mysqli_fetch_row($result);
        if (strtotime($user1update[0]) + 60 + 1.5 < time()) {
            $result = mysqli_query($data, "update `game` set `game_status`='w2',`game_ended_in`='$time' where `game_id`='$gameId'");
            if (!$result) {
                $errFlag = 1;
                echo mysqli_error($data);
            };
        }
    }

    if (!$errFlag) {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' && $YourTurn) {
            $Rolled = rand(1, 6);
            if ($Rolled == 1) {
                $result = mysqli_query($data, "update `game` set `current_bet`='0' where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
                $result = mysqli_query($data, "update `game` set `game_status`='$anotherUser' where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
            } else {
                $Bet += $Rolled;
                $result = mysqli_query($data, "update `game` set `current_bet`='$Bet' where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
            }
            $response[] = ['name' => $tmpname, 'YourScore' => $YourScore, 'OpScore' => $OpScore, 'Bet' => $Bet, 'Rolled' => $Rolled, 'win' => $YouWin, 'turn' => $YourTurn];
            header("HTTP/1.0 200 OK");
            echo json_encode($response);
        } elseif ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT' && $YourTurn)) {
            if ($userNumber == 1) {
                $result = mysqli_query($data, "select `current_bet` from `game` where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
                $Bet = mysqli_fetch_row($result)[0];
                $YourScore = $YourScore + $Bet;
                $result = mysqli_query($data, "update `game` set `player1_score`='$YourScore',`current_bet`=0 where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
            } elseif ($userNumber == 2) {
                $result = mysqli_query($data, "select `current_bet` from `game` where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
                $Bet = mysqli_fetch_row($result)[0];
                $YourScore = $YourScore + $Bet;
                $result = mysqli_query($data, "update `game` set `player2_score`='$YourScore',`current_bet`=0 where `game_id`='$gameId'");
                if (!$result) {
                    $errFlag = 1;
                    echo mysqli_error($data);
                };
            }
            $result = mysqli_query($data, "update `game` set `game_status`='$anotherUser' where `game_id`='$gameId'");
            if (!$result) {
                $errFlag = 1;
                echo mysqli_error($data);
            };
            $response[] = ['name' => $tmpname, 'YourScore' => $YourScore, 'OpScore' => $OpScore, 'Bet' => $Bet, 'Rolled' => $Rolled, 'win' => $YouWin, 'turn' => $YourTurn];
            header("HTTP/1.0 200 OK");
            echo json_encode($response);
        } else {
            if($YourTurn=0){header("HTTP/1.0 403 Forbidden");}
            $response[] = ['name' => $tmpname, 'YourScore' => $YourScore, 'OpScore' => $OpScore, 'Bet' => $Bet, 'Rolled' => $Rolled, 'err' => $_SERVER['REQUEST_METHOD'], 'win' => $YouWin, 'turn' => $YourTurn];
            echo json_encode($response);
        }
    } else {
        header("HTTP/1.0 400 Bad Request");
    }
    /*$response[] = ['name' => $tmpname, 'YourScore' => $YourScore, 'OpScore' => $OpScore, 'Bet' => $Bet, 'Rolled' => $Rolled];
    //var_dump(json_encode($response));
    echo json_encode($response);*/

}else{
    header('HTTP/1.1 302 Redirect');
}
