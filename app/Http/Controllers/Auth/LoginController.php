<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() 
    {
        return view('auth.login');
    }

    public function store(SignInRequest $request) 
    {
        // Valida los datos
        $data = $request->validated();

        // Verifica las credenciales (true => mantener la sesión)
        if (!Auth::attempt($data, true)) {
            return back()->with('error', 'Credenciales incorrectas');
        }

        return redirect()->route('dashboard');
    }
}
