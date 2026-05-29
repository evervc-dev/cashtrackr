<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Autentica al usuario para validar el email a través de la ruta generada (ya necesita tener una sesión)
        Auth::login($user);
    }
}
