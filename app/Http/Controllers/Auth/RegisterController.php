<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Stark;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'igUsername' => ['required', 'string'],
            'igPassword' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $password = $data['igPassword'];
        $method = "AES-128-CBC-HMAC-SHA256";
        $iv = random_bytes(16);
        $key = random_bytes(128);
        $password = openssl_encrypt($password, $method, $key, 0, $iv);

        $cookieIv = random_bytes(16);
        $cookieKey = random_bytes(128);
        $encryptedKey = openssl_encrypt($key, $method, $cookieKey, 0, $cookieIv);
        $encryptedIv = openssl_encrypt($iv, $method, $cookieKey, 0, $cookieIv);


        setcookie("tony", $encryptedKey, time() + (86400 * 30), "/");
        setcookie("stark", $encryptedIv, time() + (86400 * 30), "/");
        setcookie("hey", $key, time() + (86400), "/");
        setcookie("yo", $iv, time() + (86400), "/");

        $binaryIv = $this->toBinary($cookieIv);
        $binaryKey = $this->toBinary($cookieKey);
        dd($this->toString($binaryIv), $cookieIv, $this->toString($binaryKey), $cookieKey);
        $stark = new Stark;
        $stark->key = $binaryKey;
        $stark->iv = $binaryIv;
        $stark->user_id = $this->lastUserId();
        $stark->save();

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'igUsername' => $data['igUsername'],
            'igPassword' => $password,
        ]);


    }

    function lastUserId()
    {
        $user = User::orderBy('id', 'desc')->limit(1)->get();
        if (count($user) != 0) {
            return $user[0]->id + 1;
        } else {
            return 1;
        }
    }

    function toBinary($string)
    {
        $binary = "";

        for ($i = 0; $i < strlen($string); $i++) {
            $character = decbin(mb_ord(substr($string, $i, 1), 'UTF-8'));
            $binary = $binary . $character;
            for ($j = strlen($character); $j < 8; $j++) {
                $binary = "0" . $binary;
            }
        }

        return $binary;
    }

    function toString($binary)
    {
        $string = "";
        for ($i = 0; $i < strlen($binary); $i += 7) {
            $string = $string . mb_chr(bindec(substr($binary, $i, 7)), 'UTF-8');
        }
        return $string;
    }

}
