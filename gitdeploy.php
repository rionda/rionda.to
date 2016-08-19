<?php
    /**
     * GIT DEPLOYMENT SCRIPT
     *
     * Used for automatically deploying websites via github securely, more deets here:
     *
     *      https://gist.github.com/limzykenneth/baef1b190c68970d50e1
     */

    // The header information which will be verified
    $agent=$_SERVER['HTTP_USER_AGENT'];
    $signature=$_SERVER['HTTP_X_HUB_SIGNATURE'];
    $body=@file_get_contents('php://input');

    // The commands
    $commands = array(
        '/usr/local/bin/git pull',
        '/usr/local/bin/git submodule sync',
        '/usr/local/bin/git submodule update'
    );

    base64_encode($agent);
    base64_encode($signature);

    if (strpos($agent,'GitHub-Hookshot') !== false){
        if (hash_equals($signature, verify_request())){
            if (ini_get('safe_mode')){
                header('HTTP/1.1 501 Not Implemented');
                echo "PHP safe mode is enabled, so we cannot exec().";
            }else{
                // Run the commands
                foreach($commands AS $command){
                    $output = array();
                    $return = 0;
                    exec("$command 2>&1", $output, $return);
                    if ($return !== 0){
                        header('HTTP/1.1 500 Internal Server Error');
                        echo "ERROR: Command '$command' exited with status $return:\n";
                    }
                    foreach($output as $line){
                        echo "$line\n";
                    }
                }
                echo "Deploy successful.";
            }
        }else{
            header('HTTP/1.1 403 Forbidden');
            echo "Invalid request 1.";
        }
    }else{
        header('HTTP/1.1 403 Forbidden');
        echo "Invalid request 2.";
    }

    // Generate the hash verification with the request body and the key stored in your .htaccess file
    function verify_request(){
        $message = $GLOBALS['body'];
        $key = getenv('GIT_TOKEN');
        $hash = hash_hmac("sha1", $message, $key);
        $hash = "sha1=".$hash;
        return $hash;
    }


?>
