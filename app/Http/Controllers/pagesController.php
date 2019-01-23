<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use Cookie;
use App\Stark;

class pagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index()
    {

        $output = session('output');
        return view('index')->withOutput($output);
    }

    function runRuby()
    {
        $usrPas = Auth::user()->igUsername . " " . Auth::user()->igPassword;
        $cmd = "ruby instagram.rb " . $usrPas;
        $output = system($cmd);

        Session::flash('output', $output);
        return redirect()->route('index');
    }

    function encrypt()
    {
        $password = "Mati1411";
        $method = "AES-128-CBC-HMAC-SHA256";
        $iv = random_bytes(16);
        $key = random_bytes(128);
        $password = openssl_encrypt($password, $method, $key, 0, $iv);

        $iv = random_bytes(16);
        $key = random_bytes(128);
        $encrypredIv = openssl_encrypt($iv, $method, $key, 0, $iv);
        $encryptedKey = openssl_encrypt($key, $method, $key, 0, $iv);

        $stark = new Stark;
        $stark->key = $key;
        $stark->iv = $iv;
        $stark->user_id = Auth::id();
        $stark->save();

        $cookieName = "stark";
        $cookieData = [$encrypredIv, $encryptedKey];
        setcookie($cookieName, $cookieData, time() + (86400 * 30), "/");

        return redirect()->route('test2');
    }

    function decrypt()
    {
        $password = Auth::user()->igPassword;
        $method = "AES-128-CBC-HMAC-SHA256";
        $binaryKey = Auth::user()->stark->key;
        $binaryIv = Auth::user()->stark->iv;

        $key = $this->toString($binaryKey);
        $iv = $this->toString($binaryIv);
        dd($key, $iv, $_COOKIE["hey"], $_COOKIE["yo"]);
        $encryptedKey = $_COOKIE["tony"];
        $encryptedIv = $_COOKIE["stark"];
        $unecncryptedKey = openssl_decrypt($encryptedKey, $method, $key, 0, $iv);
        $unecncryptedIv = openssl_decrypt($encryptedIv, $method, $key, 0, $iv);
        dd($unecncryptedIv, $unecncryptedKey);
        $password = openssl_decrypt($password, $method, $unecncryptedKey, 0, $unecncryptedIv);
        dd($password);
    }

    function toString($binary)
    {
        $string = "";
        for ($i = 0; $i < strlen($binary); $i += 8) {
            $string = $string . chr(bindec(substr($binary, $i, 8)));
        }
        return $string;
    }
}

