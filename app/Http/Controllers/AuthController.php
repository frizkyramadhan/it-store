<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        $title = 'Login';
        return view('auth.login', compact('title'));
    }

    public function postLogin(Request $request)
    {
        // $validatedData = $request->validate([
        //     'email' => 'required',
        //     'password' => 'required|min:5',
        // ], [
        //     'email.required' => 'Email is required',
        //     'password.required' => 'Password is required'
        // ]);
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'status' => 'active'])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        } else {
            return back()->with('errors', 'Login failed.');
        }
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }

    public function register()
    {
        $title = 'Register';
        return view('auth.register', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->role = 'user';
        $user->status = 'inactive';
        $user->save();

        return redirect()->route('login')->with('success', 'Registration success! Please contact administrator to activate your account.');
    }

    // buat fungsi untuk parsley.js check email unique dari table users
    public function checkNewEmail(Request $request)
    {
        if ($request->has('email')) {
            $email = trim($request->input('email'));

            $total_row = DB::table('users')
                ->where('email', $email)
                ->count();

            if ($total_row === 0) {
                $output = [
                    'success' => true,
                ];

                return response()->json($output);
            }
        }
    }
}
