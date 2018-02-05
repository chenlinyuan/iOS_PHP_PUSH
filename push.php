<?php

// Put your device token here (without spaces):
$deviceToken = 'de5b0c3d1c7498a10335212c8aff88e9881ae9ed0f0b4fbb1f0d927905fd1925';

// Put your private key's passphrase here:密语
$passphrase = '970690';

// Put your alert message here:
$message = '<iq from="message.mk" to="100000062@mk/iOS-WIFI" id="Xq4iDN-15" type="result"><query xmlns="jabber:iq:message:tel:request"><channelId>chnnelid_1000000621000000321517449705</channelId><fromUser>100000062</fromUser><fromNick>女神</fromNick><fromAvatar>FiC6nYwjWLAbzLLKpGNnPJr1V8RX.jpg</fromAvatar><toUser>100000032</toUser><telType>voiceTel</telType><toNick>不知道</toNick><toAvatar>http://wx.qlogo.cn/mmopen/vi_32/U2zu4pb31ica7ib8XLIHxPYav7xDibE4RLjYv32atmfkt8dLZl5PbMWNaUPuib0RCr2ohmpMic5APzlhPRyoJ8cfNtQ/132</toAvatar></query></iq>';

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'voip.pem');
//stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');
//如果此处不加这个证书会报后面出现的错误：SSL3_GET_SERVER_CERTIFICATE:certificate verify failed
//此证书的下载地址：https://www.entrust.com/get-support/ssl-certificate-support/root-certificate-downloads/
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
    'ssl://gateway.sandbox.push.apple.com:2195', $err,
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
    exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
    'alert' => 'call',
    'sound' => 'default'
    );
    $body['tel'] = $message;

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
    echo 'Message not delivered' . PHP_EOL;
else
    echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);

?>
