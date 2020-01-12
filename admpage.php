<?php
$data = mysqli_connect('localhost', "root", '', 'coursework');
if ( !$data ) {
    $errFlag = 1;
    header('HTTP/1.1 302 Redirect');
}
$table=NULL;
$result=mysqli_query($data,'select `id` , `login` , `status` from `users` where 1');
while($tmp = mysqli_fetch_row($result)) {
    $table[]=$tmp;
}

if(isset($_COOKIE['Cookie'])) {
    $cookie=$_COOKIE['Cookie'];
    $result=mysqli_query($data,"select `user_id` from `tokens` where `auth_token`='$cookie'");
    $tmpId=mysqli_fetch_row($result)[0];
    $result=mysqli_query($data,"select `status` from `users` where `id`='$tmpId'");
    $status=mysqli_fetch_row($result)[0];
    if($status!='ad'){
        header("HTTP/1.0 302 Redirect");
    }

    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $delFlag = false;
        if (isset($_SERVER['REQUEST_URI'])) {
            $ExReq = explode('/', $_SERVER['REQUEST_URI']);
            $delId = $ExReq[count($ExReq) - 2];
            $method = $ExReq[count($ExReq) - 1];

            for ($i = 0; $i < count($table); $i++) {
                if ($table[$i][0] == $delId) {

                    if ($method) {
                        $result = mysqli_query($data, "update `users` set `status`='bd' where `id`='$delId'");
                        if (!!$result) {
                            $delFlag = true;
                        } else {
                            echo($data);
                        }
                    } else {
                        $result = mysqli_query($data, "update `users` set `status`='st' where `id`='$delId'");
                        if (!!$result) {
                            $delFlag = true;
                        } else {
                            echo($data);
                        }
                    }
                }
            }
        }
        if ($delFlag == true) {
            $result = mysqli_query($data, 'select `id` , `login` , `status` from `users` where 1');
            $table = NULL;
            while ($tmp = mysqli_fetch_row($result)) {
                $table[] = $tmp;
            };
            header("HTTP/1.0 200 OK");
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    } elseif (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'PUT') {
        $stream = json_decode(file_get_contents("php://input"));
        if (gettype($stream->id) == 'integer' && gettype($stream->itemName) == 'string') {

            for ($i = 0; $i < count($table); $i++) {
                if ($table[$i][0] == $stream->id) {
                    $result = mysqli_query($data, "update `users` set `login`='$stream->itemName' where `id`='$stream->id'");
                    if ($result) {
                        $updFlag = true;
                    } else {
                        echo($data);
                    }
                }
            }

            if ($updFlag == true) {
                $table = NULL;
                $result = mysqli_query($data, 'select `id` , `login` , `status` from `users` where 1');
                while ($tmp = mysqli_fetch_row($result)) {
                    $table[] = $tmp;
                };
                header("HTTP/1.0 200 OK");
            } else {
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            header("HTTP/1.0 400 BAD REQUEST");
        }
    }
    echo(json_encode($table));
}