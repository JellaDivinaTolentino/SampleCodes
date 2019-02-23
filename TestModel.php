<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TestModel extends Model
{
    protected $table = 'samples';

	protected $primaryKey = 'sample_id';

    public static function convert_string($action, $string)
    {
        $output = '';
        $encrypt_method = 'AES-256-CBC';
        $secret_key = '<insert_value>';
        $secret_iv = '<insert_value>';
        $key = hash('sha256',$secret_key);
        $initialization_vector = substr(hash('sha256',$secret_iv),0,16);

        if($string != '')
        {
            if($action == 'encrypt')
            {
                $output = openssl_encrypt($string,$encrypt_method,$key,0,$initialization_vector);
                $output = base64_encode($output);
            }

            if($action == 'decrypt'){
                $output = base64_decode($string);
                $output = openssl_decrypt($output, $encrypt_method,$key,0, $initialization_vector);
            }
        }
        return $output;
    }
}
