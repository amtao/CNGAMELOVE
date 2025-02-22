<?php

class AES
{
  /**
   *
   * @param string $string 需要加密的字符串
   * @param string $key 密钥
   * @return string
   */
  public static function encrypt($string, $key)
  {
    if(empty($key)){
      return $string;
    }
    // openssl_encrypt 加密不同Mcrypt，对秘钥长度要求，超出16加密结果不变
    $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
    return base64_encode($data);
  }
  /**
   * @param string $string 需要解密的字符串
   * @param string $key 密钥
   * @return string
   */
  public static function decrypt($string, $key)
  {
    if(empty($key)){
      return $string;
    }
    return openssl_decrypt(base64_decode($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
  }
  /**
   * 获取秘钥
   * @return string
   */
  public static function getSecretKey()
  {
    require_once dirname( __FILE__ ) . '/../config.php';
    if(defined('SP_DECODE') && SP_DECODE){
        return SP_DECODE;
    }
    $str='';//生成16位的字符窜
    return $str;
  }
}