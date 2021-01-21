<?php
function convertHtml2Confluence($html)
{
    //fix links to other issues
    $html = preg_replace_callback(
        '#/youtrack/issue/([a-z]+-[0-9]+)#',
        function($matches) {
            return strtoupper($matches[1]);
        },
        $html
    );

    $file = tempnam('/tmp', 'youtrack-export-');
    file_put_contents($file, $html);
    exec('ruby html2confluence/convert.rb ' . escapeshellarg($file), $output, $ret);
    unlink($file);
    if ($ret !== 0) {
        file_put_contents('php://stderr', "Error converting HTML to confluence\n");
        exit(5);
    }
    return trim(implode("\n", $output));
}


function login()
{
    global $username, $password, $url;

    $cookiefile = __DIR__ . '/restdata/cookie.txt';
    if (file_exists($cookiefile)) {
        return file_get_contents($cookiefile);
    }

    $context = stream_context_create(
        [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'login'    => $username,
                        'password' => $password
                    ]
                )
            ]
        ]
    );
    $response = file_get_contents($url . 'rest/user/login', false, $context);
    if ($response === false) {
        file_put_contents('php://stderr', "failed to login\n");
        exit(1);
    }
    $cookies = array();
    foreach ($http_response_header as $header) {
        if (substr(strtolower($header), 0, 11) == 'set-cookie:') {
            list($cookies[]) = explode(';', substr($header, 11));
        }
    }
    $cookieheader = 'Cookie: ' . implode(';', $cookies);
    file_put_contents($cookiefile, $cookieheader);
    return $cookieheader;
}
?>
