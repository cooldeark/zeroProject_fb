<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="http://cdn.bootcss.com/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href=" https://netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css " rel="stylesheet" type="text/css" />
        <link href="{{asset('css/loginPage.css')}}" rel="stylesheet">
    </head>
    <body>
    <style>
        #password {
            -webkit-text-security: disc;
        }


    </style>

    <div class="wrapper fadeInDown">
        <div id="formContent">
        <form class="mt-5" method="POST" id="userRegisterFormID" name="userRegisterForm" action="{{url('/userRegister')}}" autocomplete="off" onclick="return false;">
            {{csrf_field()}}
            <h4 class="fadeIn second" style="">信箱</h4>
            <input type="text" id="email" class="fadeIn second" name="user[Email]" placeholder="Email">
            <h4 class="fadeIn third">密碼</h4>
            <input type="text" id="password" class="fadeIn third" name="user[Pwd]" placeholder="password" >
            
            @if(!empty($user))
                @else
                <button  class="fadeIn fourth btn btn-success" id="loginBtn">Login</button>
                @endif
            
          </form>
          @if($errors->first('error'))
            <div class="mt-3 fadeIn fourth" style="color:red;">{{$errors->first('error')}}</div>
            @endif
          
          <div id="formFooter">
              @if(!empty($user))
              <button  class="fadeIn fourth btn btn-primary" id="userSaveAccount">Save & Post</button>
                @else
                    <button class="underlineHover fadeIn fourth btn btn-primary" style="cursor: pointer;" id="fbLoginBtn">FBLogin</button>
                    <button  class="fadeIn fourth btn btn-primary" id="registerBtn">Register</button>
                @endif
          </div>
      
        </div>
      </div>


<script>
/*
FB api library start
*/
window.fbAsyncInit = function() {
    FB.init({
    appId      : '774938796549705',
    cookie     : true,
    xfbml      : true,
    version    : 'v7.0'
    });
    
    FB.AppEvents.logPageView();   
    
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
//fb api end



function checkLoginState() {
    FB.getLoginStatus(function(response) {
          statusChangeCallback(response);
      });
}
 
function statusChangeCallback(response){
    if(response.status=='unknown'){
        console.log('no');
    }else if(response.status=='connected'){
        console.log('good');
    }
    
}



$('#fbLoginBtn').click(function(){
    FB.login(function(response){

        if(response.status=='connected'){
            
            //FB.api=>取得user身上的資訊    
        FB.api('/me', { locale: 'tr_TR', fields: 'name, email,birthday, hometown,education,gender,website,work' },
          function(response) {

                //取得fb user 有幾個好友
                // FB.api(
                //     '/'+response.id+'/friends',
                //     'GET',
                //     {},
                //     function(friendResponse) {
                //         console.log(friendResponse.summary['total_count']);
                        
                //     }
                // );

                //取得fb user 的最近post
                // FB.api(
                //     '/'+response.id+'/feed',
                //     function (feedResponse) {
                //         if (feedResponse && !feedResponse.error) {
                //             console.log(feedResponse);
                //         }
                //     }
                // );

                $.ajax({
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    url:'/fbLogin',
                    data:response,
                    dataType:'json',
                    type:'POST',
                    success:function(suMessage){
                        console.log(suMessage);
                        if(suMessage['status']==200){
                            window.location.href="{{url('/userLoginSuccess')}}";
                        }else{
                            alert('Login Fail');
                        }
                    }
                }).fail(function(eMe){
                    alert('請聯繫管理員');
                    
                });
          }
        );
        }else{
            alert('no good');
        }
    },{scope: 'public_profile,email'});
    // checkLoginState();

});

$('#registerBtn').on('click',function(){
    window.location.href="{{url('/userRegister/user')}}";
});

$('#userSaveAccount').on('click',function(){
    let userEmail=($('#email').val()).replace(/ /g, ''),userPwd=($('#password').val()).replace(/ /g, ''),checkInput=true;

    if(userEmail=='' || userEmail==undefined || userEmail==null || userPwd=='' || userPwd==undefined || userPwd==null){
        checkInput=false;
    }

    if(checkInput==false){
        alert('Email or Password not verify!');
    }else{
        document.userRegisterForm.submit();
    }
});

$('#loginBtn').on('click',function(){
    let userEmail=($('#email').val()).replace(/ /g, ''),userPwd=($('#password').val()).replace(/ /g, ''),checkInput=true;

if(userEmail=='' || userEmail==undefined || userEmail==null || userPwd=='' || userPwd==undefined || userPwd==null){
    checkInput=false;
}

if(checkInput==false){
    alert('Email or Password not verify!');
}else{
    let userData=$('#userRegisterFormID').serializeArray();
    $.ajax({
        url:'/userLogin',
        data:userData,
        dataType:'json',
        type:'POST',
        success:function(suMessage){
            console.log(suMessage);
            if(suMessage['status']==200){
                window.location.href="{{url('/userLoginSuccess')}}";
            }else{
                alert('Login Fail');
            }
        }
    }).fail(function(eMe){
        alert('請聯繫管理員');
        
    });
}
});

</script>
    </body>
</html>
