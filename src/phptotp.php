<?php

//
// PHPTOTP
// A simple TOTP library for PHP
//
// Author  : Wildy Sheverando <hai@shiwildy.com>
// Version : 1.0
//
// https://github.com/shiwildy/PHPTOTP.git
// 
// This project Licensed under The MIT License.
//

namespace ShiWildy;

class phptotp {
    private $secretLength = 16;
    private $timeStep = 30;
    private $digitLength = 6;
    private $secretKey;

    public function __construct() {
        $this->secretKey = $this->generateSecretKey();
    }

    public function generateSecretKey(): string {
        $validChars = array_merge(range('A', 'Z'), range('2', '7')); // use base32 charset
        $random = '';

        for ($i = 0; $i < $this->secretLength; $i++) {
            $random .= $validChars[random_int(0, count($validChars) - 1)];
        }

        return $random;
    }

    // Function to getsecret
    public function getSecret(): string {
        return $this->secretKey;
    }

    public function getAuth(string $secret = null): string {
        if ($secret === null) {
            $secret = $this->secretKey;
        }

        /*
            how it's work ?
            1. Decode secret key from Base32
            2. Count time step
            3. Pack counter time to binary strings.
            4. Hashing the timehex and secret to sha1
            5. Get offset from hash
            6. Generate binary code
            7. Convert it to strings.
        */
        $secret = $this->base32Decode($secret);
        $timeCounter = floor(time() / $this->timeStep);
        $timeHex = pack('N*', 0) . pack('N*', $timeCounter);
        $hash = hash_hmac('sha1', $timeHex, $secret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0xF;
        $binary = 
              ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        
        $otp = $binary % pow(10, $this->digitLength);
        return str_pad($otp, $this->digitLength, '0', STR_PAD_LEFT);
    }

    public function verify(string $code, string $secret = null): bool {
        return $this->getAuth($secret) === $code;
    }

    // function used to generate qrcode import format.
    public function getQRCodeUrl(string $label, string $issuer = ''): string {
        $secret = $this->getSecret();
        $label = rawurlencode($label);
        $issuer = rawurlencode($issuer);
        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}";
    }

    private function base32Decode(string $string): string {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $string = strtoupper($string);
        $n = 0;
        $j = 0;
        $binary = '';

        for ($i = 0; $i < strlen($string); $i++) {
            $n = $n << 5;
            $n = $n + strpos($base32chars, $string[$i]);
            $j += 5;

            if ($j >= 8) {
                $j -= 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }

        return $binary;
    }
}
