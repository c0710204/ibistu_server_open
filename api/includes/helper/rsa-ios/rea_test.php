<?php
include("rsa.php");
//RSAEncryptTest::load_pemformfile("","");
$pub=rtrim( file_get_contents("/root/www/api/settings/key/ibistu_privateKey_ios.pem"));
$pri=rtrim( file_get_contents("/root/www/api/settings/key/ibistu_privateKey_ios.pem"));
//echo base64_encode( file_get_contents("/root/temp/public_key.der"))."\n";
//RSAEncrypt::$PUBLIC_KEY=$pub;
RSAEncrypt::$PRIVATE_KEY=$pri;
//RSAEncryptTest::setup_key();
//$en=RSAEncryptTest::public_encrypt("test\n");
$en=base64_decode("tfLsaREr4q/txFvS5RD8eh39ucwfGmaOZZ6PlXFBMTWeUFOheTQn3if7f+iZRgdgSmolG7g6R2ScODawcKsYr5MZ3GFhpV8Gela9/8jr0QTxoVBsJPFHmli+JR0rNb8LBE6r5pTeeo4wwbGMggaZdvjsehv/9nH1mbwgkVzR60o=");
//echo base64_encode($en)."\n";

echo RSAEncrypt::private_decrypt($en);