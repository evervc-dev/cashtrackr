<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index() 
    {
        return view('auth.register');
    }

    public function store(SignupRequest $request) 
    {
        $data = $request->validated();

        // Registro del nuevo usuario en la db
        $user = User::create($data);

        // Evento de registro del usuario para la verificación de email
        event(new Registered($user));
    }
}
