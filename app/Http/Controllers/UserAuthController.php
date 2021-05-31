<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;//驗證器
use Hash;//雜湊
use Mail;//寄信
use Auth;
use Socialite;
use App\User;
use App\fb_user;
use Illuminate\Support\Facades\Redirect;

// use App\Shop\Entity\User;//使用者 Eloquent ORM Model




class UserAuthController extends Controller
{
    public function userlogin(Request $req){
        if(Auth::check()){
            return Redirect::to('/userLoginSuccess'); 
        }else{
            if(empty($req->user)){
                return view('login');
            }else{
                return view('login',array('user'=>'yes'));
            }
        }
        
        
    }

    public function userRegister(Request $req){
        if(Auth::check()){
            return Redirect::to('/userLoginSuccess'); 
        }else{
            $userEmail=$req->user['Email'];
        $userPwd=Hash::make($req->user['Pwd']);
        $userInputStatus=User::create([
            'email'=>$userEmail,
            'password'=>$userPwd
        ]);
        if(!isset($userInputStatus->id) || empty($userInputStatus->id)){
            dd('Register Fail!');
        }else{
            return Redirect::to('/');
        }
        }
        
    }

    public function userInputLogin(Request $req){
        if(Auth::check()){
            return Redirect::to('/userLoginSuccess'); 
        }else{
            $userEmail=$req->user['Email'];
        $userPwd=$req->user['Pwd'];
        if(Auth::attempt(['email' => $userEmail, 'password' => $userPwd])){
            $data=array(
                'status'=>200,
                'message'=>'good'
            );
            return response(json_encode($data),200);
        }else{
            return Redirect::to('/'); 
        }
        }
        
        
    }


    public function fbLogin(Request $req){
        if($req->filled('id')){//確認是否有此值有進來
            $fbName=$req->name;
            $fbID=$req->id;
            $fbMail=$req->email;

            $fbLoginOrNot=User::where('facebook_id',$fbID)->first();
            if($fbLoginOrNot==null){
                $userFBStatus=fb_user::create([
                    'email'=>$fbMail,
                    'name'=>$fbName,
                    'fbID'=>$fbID,
                ]);
                $data=array(
                    'data'=>'good',
                    'status'=>200
                );
                if(!isset($userFBStatus->id) || empty($userFBStatus->id)){
                    return response(json_encode($data),500);
                }else{
                    $userPwd=Hash::make($fbMail);//use email for the password
                    $userSaveStatus=User::create([
                        'email'=>$fbMail,
                        'facebook_id'=>$fbID,
                        'password'=>$userPwd
                    ]);
                    if(!isset($userSaveStatus->id) || empty($userSaveStatus->id)){
                        return response(json_encode($data),500);
                    }else{
                        Auth::attempt(['email' => $fbMail, 'password' => $fbMail]);
                        return response(json_encode($data),200);
                    }
                }
            }else{//已經有使用過FB登入
                if(Auth::attempt(['email' => $fbMail, 'password' => $fbMail])){
                    $data=array(
                        'status'=>200,
                        'message'=>'good'
                    );
                    return response(json_encode($data),200);
                }else{
                    return Redirect::to('/'); 
                }
            }


        }else{
            return Redirect::to('/'); 
        }
        
    }

    public function userloginSuccess(){
        $loginByFb=Auth::user()->facebook_id;

        return view('success',compact('loginByFb'));
    }

    public function userLogout(){
        Auth::logout();
        return Redirect::to('/'); 
    }


    
    //Facebook登入
    public function facebookSignInProcess()
    {
        $redirect_url = env('FB_REDIRECT');
        
        return Socialite::driver('facebook')
            ->scopes(['user_friends'])
            ->redirectUrl($redirect_url)
            ->redirect();
    }
        
    //Facebook登入重新導向授權資料處理
    public function facebookSignInCallbackProcess()
    {
        if(request()->error=="access_denied")
        {
            throw new Exception('授權失敗，存取錯誤');
        }
        //依照網域產出重新導向連結 (來驗證是否為發出時同一callback)
        $redirect_url = env('FB_REDIRECT');
        //取得第三方使用者資料
        /*
        $user = Socialite::driver('facebook')->user();
        var_dump($user);
        //*/
        $FacebookUser = Socialite::driver('facebook')
            ->fields([
                'name',
                'email',
            ])
            ->redirectUrl($redirect_url)->user();
       
        $facebook_email = $FacebookUser->email;
        if(is_null($facebook_email))
        {
            throw new Exception('未授權取得使用者 Email');
        }
        //取得 Facebook 資料
        $facebook_id = $FacebookUser->id;
        $facebook_name = $FacebookUser->name;
        echo "facebook_id=".$facebook_id.", facebook_name=".$facebook_name;
        //*/
    }



}
