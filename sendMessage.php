private function sendError($pesan) {

        $client = new Client();

        $token = 'token bot';
        $method	= "sendMessage";
        $url    = "https://api.telegram.org/bot" . $token . "/". $method;

        $chatId = 'group chat id ex: -290325513';

        $myBody['chat_id'] = $chatId;
        $myBody['text'] = $pesan;
        $request = $client->post($url,  ['form_params'=>$myBody]);

        return $request;
        
    }

    public function sendException(Exception $e)
    {

        $traceArray    = $e->getTrace();
        $filteredTrace = [];

        foreach ($traceArray as $trace) {
            if (strpos(@$trace["class"], "App\\") !== false) {
                $filteredTrace[] = @$trace["class"] . @$trace["type"] . @$trace["function"].(@$trace["line"]?":".$trace["line"]:null);
            }
        }

        $hideParams = ["password"];
        $param = request()->all();

        foreach ($hideParams as $hideParam){
            if(@$param[$hideParam]){
                $param[$hideParam] = "****";
            }
        }

        $messages =
            'ADA ERROR MAS BRO:'. "\n" .
            "------------------------------------------\n" .
            "Messages: " . $e->getMessage() . "\n" .
            "------------------------------------------\n" .
            "File: " . $e->getFile() . "\n" .
            "------------------------------------------\n" .
            "Line: " . $e->getLine() . "\n" .
            "------------------------------------------\n" .
            "URL: [" . request()->method() . "]" . request()->fullUrl() . "\n" .
            "------------------------------------------\n" .
            "PORT: ". request()->getPort() . "\n" .
            "------------------------------------------\n" .
            "IP: " . request()->getClientIp(). "\n" .
            "------------------------------------------\n" .
            "USER AGENT: " . request()->header("user-agent"). "\n" .
            "------------------------------------------\n" .
            "PARAM: " . json_encode($param) . "\n" .
            "------------------------------------------\n" .
            "CLASS TRACE:\n" . implode("\n", $filteredTrace) . "\n" .
            "------------------------------------------\n" .
            "EXCEPTION CLASS: ". get_class($e);


        $this->sendError($messages);
    }


    public function toDiscord(Exception $e)
    {
        $traceArray    = $e->getTrace();
        $filteredTrace = [];

        foreach ($traceArray as $trace) {
            if (strpos(@$trace["class"], "App\\") !== false) {
                $filteredTrace[] = @$trace["class"] . @$trace["type"] . @$trace["function"].(@$trace["line"]?":".$trace["line"]:null);
            }
        }

        $webhookurl = "webhook_url";

        //=======================================================================================================
        // Compose message. You can use Markdown
        // Message Formatting -- https://discordapp.com/developers/docs/reference#message-formatting
        //========================================================================================================

        $timestamp = date("c", strtotime("now"));

        $json_data = json_encode([
            // Message
            "content" => "@everyone",

            // Username
            // "username" => "Ahmad Saubani",

            // Avatar URL.
            // Uncoment to replace image set in webhook
            //"avatar_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=512",

            // Text-to-speech
            "tts" => false,

            // File upload
            // "file" => "",

            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => "500 Server Error",

                    // Embed Type
                    "type" => "rich",

                    // Embed Description
                    "description" => "Ada error mas bro.. silahkan di fix ya jangan sampai ada bug diaplikasi kita",

                    // URL of title link
                    "url" => "https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c",

                    // Timestamp of embed must be formatted as ISO8601
                    "timestamp" => $timestamp,

                    // Embed left border color in HEX
                    "color" => hexdec( "3366ff" ),

                    // Footer
                    "footer" => [
                        "text" => "Error Log",
                        // "icon_url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=375"
                    ],

                    // Image to send
                    // "image" => [
                    //     "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=600"
                    // ],

                    // Thumbnail
                    //"thumbnail" => [
                    //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"
                    //],

                    // Author
                    "author" => [
                        "name" => "BOT ERROR",
                        "url" => "https://ahmadsaubani.com/"
                    ],

                    // Additional Fields array
                    "fields" => [
                        // Field 1
                        [
                            "name" => "Error Message",
                            "value" => $e->getMessage(),
                            "inline" => false
                        ],
                        [
                            "name" => "Error Method",
                            "value" => request()->method(),
                            "inline" => true
                        ],
                        [
                            "name" => "Error ClientIp",
                            "value" => request()->getClientIp(),
                            "inline" => true
                        ],
                        [
                            "name" => "Error Line",
                            "value" => $e->getLine(),
                            "inline" => true
                        ],
                        [
                            "name" => "Error UserAgent",
                            "value" => request()->header("user-agent"),
                            "inline" => true
                        ],
                        [
                            "name" => "Error File",
                            "value" => $e->getFile(),
                            "inline" => false
                        ],
                        [
                            "name" => "Error URL",
                            "value" => request()->fullUrl(),
                            "inline" => false
                        ],
                        [
                            "name" => "Error Port",
                            "value" => request()->getPort(),
                            "inline" => true
                        ],
                        [
                            "name" => "Error Trace",
                            "value" =>  implode("\n", $filteredTrace) ,
                            "inline" => false
                        ]
                        
                    ]
                ]
            ]

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


        $ch = curl_init( $webhookurl );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec( $ch );
        // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
        curl_close( $ch );
    }
