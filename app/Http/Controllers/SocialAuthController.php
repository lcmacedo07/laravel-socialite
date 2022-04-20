<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function entrarFacebook()
    {
      return Socialite::driver('facebook')->redirect();
    }
    public function retornoFacebook()
    {
      $userSocial = Socialite::driver('facebook')->user();
      $email = $userSocial->getEmail();

      if(Auth::check()){
        $user = Auth::user();
        $user->facebook = $email;
        $user->save();
        return redirect()->intended('/home');
      }

      $user = User::where('facebook',$email)->first();

      if(isset($user->name)){
        Auth::login($user);
        return redirect()->intended('/home');
      }

      if(User::where('email',$email)->count()){
        $user = User::where('email',$email)->first();
        $user->facebook = $email;
        $user->save();
        Auth::login($user);
        return redirect()->intended('/home');
      }

      $user = new User;
      $user->name = $userSocial->getName();
      $user->email = $userSocial->getEmail();
      $user->facebook = $userSocial->getEmail();
      $user->password = bcrypt($userSocial->token);
      $user->save();
      Auth::login($user);
      return redirect()->intended('/home');
    }
}
