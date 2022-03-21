<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AuxFunctions;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * User Register
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function registerUser(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();

        $validator = Validator::make(
            json_decode($data, true),
            [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
                'roles' => ['required', Rule::in('Particular', 'Profesional', 'Administrador')],
            ],
            [
                'name.required' => "Campo requerido",
                'name.unique' => "Ya existe un usuario registrado con ese nombre",
                'email.required' => "Campo requerido",
                'email.email' => "El formato del correo electrónico no es válido",
                'email.unique' => "Ya existe un usuario registrado con ese correo",
                'password.required' => "Campo requerido",
                'password.regex' => "El formato de la contraseña no es válido (Ej: H0la^)",
            ]
        );

        try {
            if ($validator->fails()) {
                $response['status'] = 0;
                $response['msg'] = "Ha ocurrido un error: " . $validator->errors();

                return response()->json($response, 400);
            } else {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'roles' => $request->input('roles')
                ]);

                $user->save();

                $response['status'] = 1;
                $response['msg'] = "Usuario creado correctamente";

                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 500);
        }
    }

    /**
     * User Login
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function login(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $credentials = $request->only('name', 'password');

        $user = User::where('name', $request['name'])->first();

        try {
            if ($user) {
                if (!Auth::attempt($credentials)) {
                    $response['status'] = 0;
                    $response['msg'] = "Nombre o Contraseña incorrectos";

                    return response()->json($response, 401);
                } else {
                    $user->tokens()->delete();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    $response['status'] = 1;
                    $response['data']['token'] = $token;
                    $response['msg'] = "Sesión Iniciada Correctamente";

                    return response()->json($response, 200);
                }
            } else {
                $response['status'] = 0;
                $response['msg'] = "Usuario No Registrado";

                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }

    /**
     * Recovery User Password With Email
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function retrieve_password(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $email = $request->email;

        try {
            if ($request->has('email')) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=[]{};:,./?\|`~';
                    $regex = '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/';

                    do {
                        $password = AuxFunctions::randomPassword($characters, 6);
                    } while (!preg_match($regex, $password));

                    $user->password = Hash::make($password);
                    $user->save();

                    $response['msg'] = "Su nueva contraseña es: " . $password;
                    $response['status'] = 1;

                    return response()->json($response, 200);
                } else {
                    $response['msg'] = "Este Usuario No Está Registrado";
                    $response['status'] = 0;

                    return response()->json($response, 404);
                }
            } else {
                $response['msg'] = "Introduzca el email";
                $response['status'] = 0;

                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }

    /**
     * Redirect To Google OAuth
     * 
     * @param nil
     * @return response()->json($response, http_status_code)
     */
    public function loginRedirect()
    {
        $response = ["status" => 1, "msg" => ""];

        try {
            $googleAuthScopes = [
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
            ];

            $response['msg'] = Socialite::driver('google')->scopes($googleAuthScopes)->redirect()->getTargetUrl();
            $response['status'] = 1;

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }

    /**
     * Get oAuth Data
     * 
     * @param nil
     * @return response()->json($response, http_status_code)
     */
    public function loginCall()
    {
        $response = ["status" => 1, "msg" => ""];

        try {
            $auth_user = Socialite::driver('google')->stateless()->user();

            $dbUser = User::where('email', $auth_user->email)->first();

            if (!$dbUser) {
                $user = new User();
                $user->google_id = $auth_user->id;
                $user->name = $auth_user->name;
                $user->email = $auth_user->email;
                $user->password = Hash::make('password');
                $user->save();

                $token = $user->createToken('auth_token')->plainTextToken;

                $response['msg'] = "Usuario Guardado Correctamente";
                $response['token'] = $token;
                $response['status'] = 1;

                return response()->json($response, 200);
            } else {
                $response['msg'] = "Ha Ocurrido un Error";
                $response['status'] = 0;

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 500);
        }
    }
}
