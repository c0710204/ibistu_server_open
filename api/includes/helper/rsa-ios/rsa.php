<?php
class RSAEncrypt {
  public static $PRIVATE_KEY = "";
  public static $PUBLIC_KEY = "";
  private static $private_key;
  private static $public_key;
 
  public static function private_encrypt($str){
    self::setup_key();
    if(openssl_private_encrypt($str, $encrypted, self::$private_key))
      return $encrypted;
  }
  public static function private_decrypt($str){
    self::setup_key();
    if(openssl_private_decrypt($str, $decrypted, self::$private_key))
      return $decrypted;
    else
      return -1;
  }
  public static function public_decrypt($str){
    self::setup_key();
    if(openssl_public_decrypt($str, $decrypted, self::$public_key))
      return $decrypted;
  }
  public static function public_encrypt($str){
    self::setup_key();
    if(openssl_public_encrypt($str, $encrypted, self::$public_key))
      return $encrypted;
  }
  private static function setup_key(){
    if (!self::$private_key){
      // 这里的test就是在生成证书的时候设置的私钥密码
      self::$private_key = openssl_pkey_get_private(self::$PRIVATE_KEY, "1234");
    }
    if (!self::$public_key)
      self::$public_key = openssl_pkey_get_public(self::$PUBLIC_KEY);
  }
}