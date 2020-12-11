<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Session;
use Carbon;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showFormLogin() {
        if (Auth::check()) { // check authentication and session
            //Login Success
            return redirect()->route('products.index');
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $rules = [
            'email'                 => 'required|email',
            'password'              => 'required|min:5'
        ];
 
        $messages = [
            'email.required'        => 'Email wajib diisi',
            'email.email'           => 'Email tidak valid',
            'password.required'     => 'Password wajib diisi',
            'password.min'          => 'Password minimal 5 karakter'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
 
        $data = [
            'email'     => $request->input('email'),
            'password'  => $request->input('password')
        ];

        Auth::attempt($data);
 
        if (Auth::check()) {
            //Login Success
            return redirect()->route('products.index');
 
        } else {
            //Login Failed
            Session::flash('error', 'Email atau password salah');
            return redirect()->route('login');
        }
    }

    public function showFormRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $rules = [
            'name'                  => 'required|min:3|max:35',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:5|confirmed'
        ];
 
        $messages = [
            'name.required'         => 'Nama Lengkap wajib diisi',
            'name.min'              => 'Nama lengkap minimal 3 karakter',
            'name.max'              => 'Nama lengkap maksimal 35 karakter',
            'email.required'        => 'Email wajib diisi',
            'email.email'           => 'Email tidak valid',
            'email.unique'          => 'Email sudah terdaftar',
            'password.required'     => 'Password wajib diisi',
            'password.min'          => 'Password minimal 5 karakter',
            'password.confirmed'    => 'Password tidak sama dengan konfirmasi password'
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
 
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
 
        $user = new User;
        $user->name = ucwords(strtolower($request->name));
        $user->email = strtolower($request->email);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = \Carbon\Carbon::now();
        $save_user = $user->save();
 
        if($save_user){
            Session::flash('success', 'Register berhasil! Silahkan login untuk mengakses data');
            return redirect()->route('login');
        } else {
            Session::flash('errors', ['' => 'Register gagal! Silahkan ulangi beberapa saat lagi']);
            return redirect()->route('register');
        }
    }

    public function logout() {
        Auth::logout(); // remove active session
        return redirect()->route('login');
    }
}
