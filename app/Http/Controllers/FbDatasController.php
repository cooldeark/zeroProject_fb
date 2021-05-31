<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\FbDataCreateRequest;
use App\Http\Requests\FbDataUpdateRequest;
use App\Repositories\FbDataRepository;
use App\Validators\FbDataValidator;
use Prettus\Repository\Contracts\RepositoryInterface;//add by me

//add be me
use Validator;//驗證器
use Hash;//雜湊
use Mail;//寄信
use Auth;
use Socialite;
use App\User;
use App\fb_user;
use Illuminate\Support\Facades\Redirect;

/**
 * Class FbDatasController.
 *
 * @package namespace App\Http\Controllers;
 */
class FbDatasController extends Controller
{
    /**
     * @var FbDataRepository
     */
    protected $repository;

    /**
     * @var FbDataValidator
     */
    protected $validator;

    /**
     * FbDatasController constructor.
     *
     * @param FbDataRepository $repository
     * @param FbDataValidator $validator
     */
    public function __construct(FbDataRepository $repository, FbDataValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getFbData()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $fbDatas = $this->repository->all();
        
        return $fbDatas;

        // if (request()->wantsJson()) {

        //     return response()->json([
        //         'data' => $fbDatas,
        //     ]);
        // }

        // return view('fbDatas.index', compact('fbDatas'));
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
                

                $userFBStatus = $this->repository->create(array(
                    'email'=>$fbMail,
                    'name'=>$fbName,
                    'fbID'=>$fbID,
                ));

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


    public function userlogin(Request $req){
        // dd($this->getFbData());
        
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  FbDataCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(FbDataCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $fbDatum = $this->repository->create($request->all());
            
            // $response = [
            //     'message' => 'FbData created.',
            //     'data'    => $fbDatum->toArray(),
            // ];

            // if ($request->wantsJson()) {

            //     return response()->json($response);
            // }

            // return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fbDatum = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $fbDatum,
            ]);
        }

        return view('fbDatas.show', compact('fbDatum'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $fbDatum = $this->repository->find($id);

        return view('fbDatas.edit', compact('fbDatum'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FbDataUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(FbDataUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $fbDatum = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'FbData updated.',
                'data'    => $fbDatum->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'FbData deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'FbData deleted.');
    }
}
