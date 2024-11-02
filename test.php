<?php

require "vendor/autoload.php";
use ShiWildy\phptotp;
$phptotp = new phptotp();

// Generate Secret Key
$secret = $phptotp->getSecret();
echo "Secret Key: " . $secret . PHP_EOL;

// Generate Current auth code
$auth = $phptotp->getAuth($secret);
echo  "Current Auth Code:" . $auth . PHP_EOL;

// Verify Auth code with secret key
$verify = $phptotp->verify($auth, $secret);
if ($verify === true) {
    echo "Verify status: Correct" . PHP_EOL;
} else {
    echo "Verify status: Incorrect" . PHP_EOL;
}

// Generate TOTP Link
$qrcodelink = $phptotp->getQRCodeUrl("MyApp", "ismy@email.com", $secret);
echo "Importable Link for QR: "  . $qrcodelink . PHP_EOL;
