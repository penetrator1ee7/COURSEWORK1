<?php
const token='HIHIHIHIHIHIHIHHIHIHIHIHIHIHIHHIIH';
const base='https://api.telegram.org/bot';
const path='HIIHIH';
const server='https://12345679.ngrok.io';
const greet='Hello.Type /login:username;password to log in.Type /logout to logout.Type /list to see all registered users and their statuses.Type /ban:nickname or /unban:nickname to ban or unban user.Type /help for help.';

$url=base.token.'/setWebhook?url='.server.path;       //!!!to start hook!!!
$response=file_get_contents($url);
echo $response;

$newMsg=file_get_contents("php://input");
//$newMsg=file_get_contents('tgbot.json');
file_put_contents('tgbot.json',$newMsg); //to check response
$newMsg=json_decode($newMsg);

$data = mysqli_connect('localhost', "root", '', 'coursework');

$errFlag=0;
$result=mysqli_query($data,
    'CREATE TABLE if not exists `tgtokens`(
user_id INT UNSIGNED NOT NULL,
tg_id INT UNSIGNED NOT NULL
)' );
if(!$result) echo mysqli_error($data);

if(strpos($newMsg->message->text,'login')){

    $info=substr($newMsg->message->text ,'7');
    $login=explode(';',$info);


    $unfoundFlag=1;
    $query="SELECT login FROM users where login='$login[0]'";
    $result=mysqli_query( $data,  $query );
    if(!null==mysqli_fetch_row($result)){
        $unfoundFlag=0;
    }
    if($unfoundFlag){
        $errFlag=1;
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=We cannot find nickname'.$login[0].'in our database.';
        echo file_get_contents($sendMessage);
    }

    if(!$errFlag){
        $query="SELECT pass_h,pass_s,pass_exp FROM users Where login='$login[0]'";
        $result=mysqli_query( $data,  $query );
        $row=mysqli_fetch_row($result);
        if($row[0]===hash('sha256',$login[1].$row[1])) {
        }else{
            $errFlag=1;
            $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=Password'.$login[1].'is wrong.';
            echo file_get_contents($sendMessage);
        }
    }

    $result=mysqli_query($data,"SELECT id FROM users WHERE login = '$login[0]'");
    $id=mysqli_fetch_row($result)[0];

    $result=mysqli_query($data,"select `status` from `users` where `id`='$id'");
    $status=mysqli_fetch_row($result)[0];
    $tmp=$newMsg->message->chat->id;
    if($status=='ad'){ $result=mysqli_query($data,"insert into `tgtokens` (user_id,tg_id) value ('$id','$tmp' )");
        if(!$result) echo mysqli_error($data);
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=User ' . $login[0].' is successfully authorised.';
        echo file_get_contents($sendMessage);
    }else{
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=This bot can only be used by admins.' ;
        echo file_get_contents($sendMessage);
    }


}elseif ( strpos($newMsg->message->text,'ban')){

    $tmp=$newMsg->message->chat->id;
    $result=mysqli_query($data,"select `user_id` from `tgtokens` where `tg_id`='$tmp'");
    $id=mysqli_fetch_row($result)[0];
    if(!$id){
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=You shall authorise first.' ;
        echo file_get_contents($sendMessage);
    }else {


        if (strpos($newMsg->message->text, 'unban')) {
            $login = substr($newMsg->message->text, '7');
            $tmp = $newMsg->message->chat->id;
            $result = mysqli_query($data, "select `user_id` from `tgtokens` where `tg_id`='$tmp'");
            if (!$result) {
                $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=You shall authorise first.';
                echo file_get_contents($sendMessage);
            } else {
                $result = mysqli_query($data, "update `users` set `status`='st' where `login`='$login'");
                $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=User ' . $login . ' successfully unbanned.';
                echo file_get_contents($sendMessage);
            }

        } else {
            $login = substr($newMsg->message->text, '5');
            $tmp = $newMsg->message->chat->id;
            $result = mysqli_query($data, "select `user_id` from `tgtokens` where `tg_id`='$tmp'");
            if (!$result) {
                $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=You shall authorise first.';
                echo file_get_contents($sendMessage);
            } else {
                $result = mysqli_query($data, "update `users` set `status`='bd' where `login`='$login'");
                $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=User ' . $login . ' successfully banned.';
                echo file_get_contents($sendMessage);
            }
        }

    }

}elseif(strpos($newMsg->message->text,'list')){
    $tmp=$newMsg->message->chat->id;
    $result=mysqli_query($data,"select `user_id` from `tgtokens` where `tg_id`='$tmp'");
    $id=mysqli_fetch_row($result)[0];
    if(!$id){
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=You shall authorise first.' ;
        echo file_get_contents($sendMessage);
    }else{
        $table=NULL;
        $result=mysqli_query($data,'select  `login`,`status` from `users` where 1');
        while($tmp = mysqli_fetch_row($result)) {
            $table=$table.','.$tmp[0].':'.$tmp[1];
        }
        $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text='.$table ;
        echo file_get_contents($sendMessage);
    }


}elseif(strpos($newMsg->message->text,'logout')){
    $tmp=$newMsg->message->chat->id;
    $result=mysqli_query($data,"delete from `tgtokens` where `tg_id`='$tmp'");
    if(!$result){}
    $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=You were successfully loged out.';
    echo file_get_contents($sendMessage);
}elseif(strpos($newMsg->message->text,'start')||strpos($newMsg->message->text,'help')){
    $sendMessage = base . token . '/sendMessage?chat_id=' . $newMsg->message->chat->id . '&text=' . greet;
    echo file_get_contents($sendMessage);

}
