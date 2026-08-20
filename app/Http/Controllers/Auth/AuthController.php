<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use App\Models\LoginRegister;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Models\PasswordResetToken;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationDispatcher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\ContractContent;


class AuthController extends Controller
{

    public function login(){
        $login = LoginRegister::where('key','login')->first();
        return view('auth.adminLogin',compact('login'));
    }

    public function loginProcc(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect('/admin-dashboard')->with('sucess','Iniciar sesión exitosamente');
        }else{
            return redirect()->back()->with('error','error de inicio de sesion !!');
        }
    }

    public function adminLogout(){
        Auth::logout();

        return redirect('/')->with('success',"Has cerrado sesión correctamente");
    }

    public function register(){

        $register = LoginRegister::where('key','register')->first();

        return view('auth.register',compact('register'));
    }

    // public function registerProcc(Request $request){
    //     try {
    //         $request->validate([
    //             'first_name' => 'required|string|max:255',
    //             'last_name' => 'required|string|max:255',
    //             'email' => 'required|email|unique:users,email',
    //             'password' => 'required|string|min:6',
    //         ], [
    //             'first_name.required' => 'El nombre es obligatorio.',
    //             'first_name.string' => 'El nombre debe ser una cadena de texto válida.',
    //             'first_name.max' => 'El nombre no debe exceder los 255 caracteres.',

    //             'last_name.required' => 'El apellido es obligatorio.',
    //             'last_name.string' => 'El apellido debe ser una cadena de texto válida.',
    //             'last_name.max' => 'El apellido no debe exceder los 255 caracteres.',

    //             'email.required' => 'La dirección de correo electrónico es obligatoria.',
    //             'email.email' => 'Por favor, introduce una dirección de correo electrónico válida.',
    //             'email.unique' => 'Este correo electrónico ya está registrado.',

    //             'password.required' => 'La contraseña es obligatoria.',
    //             'password.string' => 'La contraseña debe ser una cadena de texto válida.',
    //             'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    //         ]);


    //         $existingUser = User::where('email', $request->email)->first();

    //         if($existingUser){
    //             if (Hash::check($request->password, $existingUser->password)) {
    //                 Auth::login($existingUser);

    //                 if(auth()->user()->is_admin == 1){
    //                     return redirect('/admin-dashboard');
    //                 }else{
    //                     if($request->redirecturl){
    //                         return redirect()->route('user.checkout')->with('success', 'Tu documento esta listo.');
    //                     }
    //                     return redirect('/')->with('success', 'Iniciar sesión exitosamente');
    //                 }
    //             }else{
    //                 return redirect()->back()->with('error', 'La contraseña no coincide con la registrada para este correo.');
    //             }
    //         }

    //         $user = new User();
    //         $user->first_name = $request->first_name;
    //         $user->last_name = $request->last_name;
    //         $user->email = $request->email;
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         if(Auth::attempt($request->only('email', 'password'))){
    //             NotificationDispatcher::dispatch('Registration_successful', auth()->user());
    //             if(auth()->user()->is_admin == 1){
    //                 return redirect('/admin-dashboard')->with('success', 'Bienvenido al Panel de administración');
    //             }else{
    //                 if($request->redirecturl){
    //                     return redirect()->route('user.checkout')->with('success', 'Tu documento esta listo.');
    //                 }
    //                 return redirect('/')->with('success', 'Iniciar sesión exitosamente');
    //             }
    //         }
    //         return redirect()->back()->with('success', "Registro exitoso, pero no se pudo iniciar sesión. Por favor inténtalo de nuevo.");
    //     }catch (Exception $e){
    //         saveLog("Error:", "AuthController", $e->getMessage());
    //         return redirect()->back()->with('error', 'Algo salió mal. Por favor inténtalo de nuevo.');
    //     }
    // }

    public function registerProcc(Request $request)
    {       

        try {
            $isRedirect = $request->has('redirecturl');
            $redirectUrl = $request->input('redirecturl', '/legal-documents');

            if(!$isRedirect){

                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                ], [
                    'first_name.required' => 'El nombre es obligatorio.',
                    'first_name.string' => 'El nombre debe ser una cadena de texto válida.',
                    'first_name.max' => 'El nombre no debe exceder los 255 caracteres.',

                    'last_name.required' => 'El apellido es obligatorio.',
                    'last_name.string' => 'El apellido debe ser una cadena de texto válida.',
                    'last_name.max' => 'El apellido no debe exceder los 255 caracteres.',

                    'email.required' => 'La dirección de correo electrónico es obligatoria.',
                    'email.email' => 'Por favor, introduce una dirección de correo electrónico válida.',
                    'email.unique' => 'Este correo electrónico ya está registrado.',

                    'password.required' => 'La contraseña es obligatoria.',
                    'password.string' => 'La contraseña debe ser una cadena de texto válida.',
                    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                ]);
            }

            $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:6',
                ], [
                    'first_name.required' => 'El nombre es obligatorio.',
                    'first_name.string' => 'El nombre debe ser una cadena de texto válida.',
                    'first_name.max' => 'El nombre no debe exceder los 255 caracteres.',

                    'last_name.required' => 'El apellido es obligatorio.',
                    'last_name.string' => 'El apellido debe ser una cadena de texto válida.',
                    'last_name.max' => 'El apellido no debe exceder los 255 caracteres.',

                    'email.required' => 'La dirección de correo electrónico es obligatoria.',
                    'email.email' => 'Por favor, introduce una dirección de correo electrónico válida.',
                    'email.unique' => 'Este correo electrónico ya está registrado.',

                    'password.required' => 'La contraseña es obligatoria.',
                    'password.string' => 'La contraseña debe ser una cadena de texto válida.',
                    'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
                ]);

            

            $existingUser = User::where('email', $request->email)->first();

            if($existingUser){
                if(Hash::check($request->password, $existingUser->password)){
                    Auth::login($existingUser);

                    if(auth()->user()->is_admin == 1){
                        return redirect('/admin-dashboard');
                    }else{
                        if($isRedirect){
                            // return redirect()->route('user.checkout')->with('success', 'Tu documento está listo.');
                            return redirect($redirectUrl)->with('success', 'Iniciar sesión exitosamente');
                        }
                        return redirect('/')->with('success', 'Iniciar sesión exitosamente');
                    }
                }else{
                    return redirect()->back()->with('error', 'La contraseña no coincide con la registrada para este correo.');
                }
            }

            $user = new User();
            $user->first_name = $request->first_name ?? '';
            $user->last_name = $request->last_name ?? '';
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            if(Auth::attempt($request->only('email', 'password'))){
                                    if(Session::has('contract_session_token')){

                        ContractContent::where(
                            'session_token',
                            Session::get('contract_session_token')
                        )
                        ->whereNull('user_id')
                        ->update([
                            'user_id' => auth()->id()
                        ]);

                    }
                NotificationDispatcher::dispatch('Registration_successful', auth()->user());
                if(auth()->user()->is_admin == 1){
                    return redirect('/admin-dashboard')->with('success', 'Bienvenido al Panel de administración');
                }else{
                    return redirect($redirectUrl)->with('success', 'Iniciar sesión exitosamente');
                }
            }

            return redirect()->back()->with('success', "Registro exitoso, pero no se pudo iniciar sesión. Por favor inténtalo de nuevo.");
        } catch (\Exception $e) {
            saveLog("Error:", "AuthController", $e->getMessage());
            return redirect()->back()->with('error', 'Algo salió mal. Por favor inténtalo de nuevo.');
        }
    }


    public function loginUser(){
        $login = LoginRegister::where('key','login')->first();
        return view('auth.login',compact('login'));
    }

    public function loginProcess(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'El correo es obligatorio.',         
            'email.email' => 'Correo no válido.',                     
            'email.exists' => 'Correo no registrado.',              
            'password.required' => 'La contraseña es obligatoria.',  
            'password.string' => 'Contraseña no válida.',            
            'password.min' => 'Mínimo 6 caracteres.',                
        ]);

        $redirectUrl = $request->input('redirect_url');

        if(Auth::attempt($request->only('email', 'password'))){
            if (
                    auth()->check() &&
                    Session::has('contract_session_token')
                ) {

                    ContractContent::where(
                        'session_token',
                        Session::get('contract_session_token')
                    )
                    ->whereNull('user_id')
                    ->update([
                        'user_id' => auth()->id()
                    ]);
                }
            if(Auth::user()->is_admin == 1){
                if(!empty($redirectUrl) && $redirectUrl !== '/legal-documents' ){
                    return redirect($redirectUrl)->with('success', 'Iniciar sesión exitosamente');
                }
                return redirect('/admin-dashboard');
            }elseif(Auth::user()->is_admin == 2){
                return redirect('/admin-dashboard');
            }elseif(Auth::user()->is_admin == 3){
                return redirect('/admin-dashboard');
            }else if(Auth::user()->is_admin == 0){
                if(!empty($redirectUrl)){
                    return redirect($redirectUrl)->with('success', 'Iniciar sesión exitosamente');
                }
                return redirect('/')->with('success', 'Iniciar sesión exitosamente');
            }


        }else{
            return back()->withErrors(['password' => 'Contraseña no válida.'])->withInput();
        }
       
        return redirect()->back()->with('error', 'Las credenciales proporcionadas no coinciden con nuestros registros.');
    }

    public function logout(){
        Auth::logout();
        return redirect('/')->with('success',"Has cerrado sesión correctamente");
    }

    public function forgetPassword(){
        $login = LoginRegister::where('key','login')->first();
        return view('auth.forget_password',compact('login'));
    }

    public function sendResetLink(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No se encontró ninguna cuenta con esa dirección de correo electrónico.');
        }

        $token = Str::random(64); 

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $resetLink = url('/password/reset/' . $token . '?email=' . urlencode($email));
        Mail::to($email)->send(new ResetPasswordMail($resetLink));
        return back()->with('success', 'Reset link has been sent to your email.');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.password_reset', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }


    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        $resetEntry = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetEntry) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No user found with that email.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token after success
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login.user')->with('success', 'Password has been reset successfully.');
    }






    // login with google  function define

    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();

    }


    public function handleGoogleCallback(){

        try {
            $googleUser  = Socialite::driver('google')->stateless()->user();
            $user = User::where('google_id', $googleUser->getId())->first();

            if($user){
                if($user->email !== $googleUser->getEmail()) {
                    return redirect('/')->with('error', 'El correo electrónico no coincide. Por favor contacte al soporte.');
                }

                Auth::login($user);
                return $this->redirectUser($user);
            }

            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if($existingUser){
                return redirect('/')->with('error', 'Este correo electrónico ya está registrado. Por favor inicia sesión.');
            }

            $fullName = $googleUser->name;
            $nameParts = explode(' ', $fullName, 2);

            $newUser = new User();
            $newUser->first_name = $googleUser->user['given_name'] ?? $nameParts[0];
            $newUser->last_name = $googleUser->user['family_name'] ?? (isset($nameParts[1]) ? $nameParts[1] : '');
            $newUser->email = $googleUser->email;
            $newUser->google_id = $googleUser->getId();
            $newUser->password = Hash::make(Str::random(16));
            $newUser->email_verified_at = now();

            if($newUser->save()){
                Auth::login($newUser);
                NotificationDispatcher::dispatch('Registration_successful', $newUser);
                return $this->redirectUser($newUser);
            }else{
                return redirect('/')->with('error', 'No se pudo crear el usuario. Por favor inténtalo de nuevo.');
            }


        }catch (\Throwable $th) {
            Log::error('Google Callback Error: ' . $th->getMessage());
            return redirect('/')->with('error', 'Algo salió mal. Por favor inténtalo de nuevo.');
        }
    }

    private function redirectUser($user){
        if($user->is_admin){
            return redirect('/admin-dashboard')->with('success', 'Bienvenido al Panel de administración.');
        }
        return redirect('/')->with('success', 'Bienvenido a la página de inicio.');
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->scopes(['email'])->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::where('facebook_id', $facebookUser->getId())->first();

            if ($user) {
                if ($user->email && $facebookUser->getEmail() && $user->email !== $facebookUser->getEmail()) {
                    return redirect('/')->with('error', 'El correo electrónico no coincide.');
                }

                Auth::login($user);
                return $this->redirectUser($user);
            }

            // Check if email exists (only if Facebook provides one)
            if ($facebookUser->getEmail()) {
                $existingUser = User::where('email', $facebookUser->getEmail())->first();
                if ($existingUser) {
                    return redirect('/')->with('error', 'Este correo electrónico ya está registrado.');
                }
            }

            $fullName = $facebookUser->getName() ?? '';
            $nameParts = explode(' ', $fullName, 2);

            $newUser = new User();
            $newUser->first_name = $nameParts[0] ?? 'Facebook';
            $newUser->last_name = $nameParts[1] ?? 'User';
            $newUser->email = $facebookUser->getEmail();
            $newUser->facebook_id = $facebookUser->getId();
            $newUser->password = Hash::make(Str::random(16));

            
            // Only set email_verified_at if email exists
            if ($facebookUser->getEmail()) {
                $newUser->email_verified_at = now();
            }

            if ($newUser->save()) {
                Auth::login($newUser);
                NotificationDispatcher::dispatch('Registration_successful', $newUser);
                return $this->redirectUser($newUser);
            }

            return redirect('/')->with('error', 'No se pudo crear el usuario.');

        } catch (\Throwable $th) {
            Log::error('Facebook Callback Error: ' . $th->getMessage());
            return redirect('/')->with('error', 'Algo salió mal. Por favor inténtalo de nuevo.');
        }
    }


}
