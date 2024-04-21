<?php
//This PHP script for telegram bot is created by Hadi Alnabriss (alnabris@gmail.com), you can use it, modify it,
//redistribute without any license or legal circumestances.


// To start working with telegram bot you need to login to your telegram app and go to the botFather channel
// Then you should create your own bot, instantly you will get a token like the one in the next line:
//    6707415253:AAGEG-aHrfNHBJh1jbsYrJ1Rtc6nbvNmTe8
//Now to manage your bot api , you will use links like the following one:
// https://api.telegram.org/bot6707415253:AAGEG-aHrfNHBJh1jbsYrJ1Rtc6nbvNmTe8/getUpdates
// note the second part which includes the word "bot" followed by your token

//Now I'm going to make an API request to create a webhook
//https://api.telegram.org/bot6707415253:AAGEG-aHrfNHBJh1jbsYrJ1Rtc6nbvNmTe8/setWebhook?url=https://hadi-tech.com/hooking2024/hook.php&&secret_token=123412341234
// Note that the webhook creation URL includes two important parts:
// 1. The URL supposed to be requested when any messages are sent to your bot
// 2. The shared password or secret 

//Now lets start the work......................

// the request will contain a header “X-Telegram-Bot-Api-Secret-Token" this header contains the secret configured
// when we created the webhook, we are supposed to check this secret to guarantee that the request is really authorized
// In this example I only read the secret and save it to a temporary file, but in real environment you should
// create something to check if the passowrd is invalid, then exit 
/*
* if ( $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] != "mypassord"){ exit(); }
*/
	    $secret_sent_from_telegram = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'];

//Whatever the sent secret is, I'm going to save it to the news directory (this directory has 777 permissions)
        file_put_contents('./news/repo_info', $secret_sent_from_telegram."\n" );

//Then read all the request input and append it to the same temporary file, the following command will read
// something like :
/*{"update_id":75330005,
*"message":{"message_id":20,
*           "from":{
*                "id":6998884741,
*                "is_bot":false,
*                "first_name":"Hadi",
*                "last_name":"Alnabriss",
*                "language_code":"en"
*                 },
*            "chat":{
*                "id":6998884741,
*                "first_name":"Hadi",
*                "last_name":"Alnabriss",
*                "type":"private"
*                 },
*            "date":1713657562,
*            "text":"Uu"
*           }
*        }
*/

        file_put_contents('./news/repo_info', file_get_contents('php://input')."\n" , FILE_APPEND );

        
//Now we need to read particular values from the previously shown array        
        $json = file_get_contents('php://input');

// Decode the JSON file
        $json_data = json_decode($json,true);

// In the following two lines we are reading the message text and the message chat id, the two values
// are also appended to the temporary file
        file_put_contents('./news/repo_info', $json_data['message']['text']."\n" , FILE_APPEND );
        file_put_contents('./news/repo_info', $json_data["message"]["chat"]["id"]."\n" , FILE_APPEND );
          

// For now, the previous code is going to save something like the following in our temp file:

// 123412341234       
// {"update_id":75330017,      
// "message":{"message_id":37,"from":{"id":1610460886,"is_bot":false,"first_name":"dr.Abeer","language_code":"en"},"chat":{"id":1610460886,"first_name":"dr.Abeer","type":"private"},"date":1713663755,"text":"Hi"}}
// Hi
// 1610460886


// In the following part of the code, we are going to make our bot make replies to the recieved messages
        
// Then we prepare the CURL request to contact the telegram bot API
        $website="https://api.telegram.org/bot6707415253:AAGEG-aHrfNHBJh1jbsYrJ1Rtc6nbvNmTe8";
	    $chatId=$json_data["message"]["chat"]["id"];

        //The following line includes the reply , if the message is "Hi", the reply will be :
        // I recieved your message: Hi
	    $reply_message = 'I recieved your message : '.$json_data['message']['text'];

        //Parameters required by the API : Chat ID and reply message
		$params=[
		        'chat_id'=>$chatId,
		        'text'=>$reply_message,
			  ];
        

        //Using the method sendMessage
	    $ch = curl_init($website . '/sendMessage');
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $result = curl_exec($ch);
	    curl_close($ch);

    //After completing the bot and requesting this Script, you should get two things:
    // 1. A temporary file includes the secret and the information we extracted from the request
    // 2. A message will be recieved instantly by the bot's sender

?>
