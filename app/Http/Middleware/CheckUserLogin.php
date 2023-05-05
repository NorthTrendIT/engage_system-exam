<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use App\Models\Role;

class CheckUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(userrole() != 1){ //Not Super Admin
            if(Auth::user()->first_login == 1){
                \Session::flash('profile_error_message', "");
                $role = Role::find(Auth::user()->role_id);
                if(strtolower($role->name) == 'sales personnel'){
                    return redirect()->route('profile.change-password.index');
                }else{
                    if(Auth::user()->password_text == "engage"){
                        return redirect()->route('profile.change-password.index');
                    }
                    return redirect()->route('profile.index');
                }
            }

            if(strpos(Auth::user()->email, '@mailinator.com') !== false){
                return redirect()->route('profile.index');
            }
        }

        return $next($request);
    }
}
