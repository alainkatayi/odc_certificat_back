<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthentificationController extends Controller
{
        //function pour la création d'un utilisateur
    public function register(Request $request){
        //validation des donnees
        $validator= Validator::make($request -> all(),[
            'name' => 'required | string |max:50',
            'email' => 'required | string| email | max:255 | unique:users',
            'password' => 'required | string | min:6 | confirmed',
        ]);
        try{
            
            //s'il y a erreur, on revoi un message d'erreur
            if ($validator -> fails()){
            return response() -> json([
                    'error' => [
                        'name' => implode($validator -> errors() -> get('name')),
                        'email' => implode($validator -> errors() -> get('email')),
                        'password' => implode($validator -> errors() -> get('password'))
                    ]
                ], 403);
            }
        //dans le cas contraire, on crée le user
            $user = User::create([
                'name' => $request -> name,
                'email' => $request -> email,
                'password' => Hash::make($request -> password),
            ]);

            //on genère aussi token
            $token = $user->createToken('token')->plainTextToken;
            $user['token'] = $token;

            //on envoie le token et le user
            return response() -> json([
                'message' => 'Inscription réussi',
                'user' => $user
            ], 200);

        }
        //casd'erreur
        catch(\Exception $exception){
            return response()-> json(['error' => $exception -> getMessage() ]);
        }
    }

        //function pour la connexion d'un utilisateur
    public function login(Request $request){

        $validationData = Validator::make($request ->all(),[
            'email' => 'required | string |email',
            'password' => 'required | string'
        ]);

        if ($validationData -> fails()){
            return response()-> json($validationData -> errors(), 403);
        }
        $credenstials = ['email' => $request -> email, 'password' => $request -> password];
        
        try{
            if(!auth()->attempt($credenstials)){
                return response() -> json([
                    'error' => "Email ou Mot de passe  incorrect  "
                ], 400);
            }

            $user = User::where('email', $request -> email)->first();
            $token = $user -> createToken('token') -> plainTextToken;
            $user['token'] = $token;

            return response() -> json($user);
        }
        catch(\Exception $exception){
            return response()-> json([
                'error' => [
                    $exception -> getMessage()
                ]
                ], 500);
        }
    }
}
