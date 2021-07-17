<!DOCTYPE html>
<html>
<head>
 <title>E-Share</title>
</head>
<body>
 
 <h6>You received this email because you join our e-Share</h6>

 <div class="container">
     <div class="row justify-content-center">
         <div class="col-md-8">
             <div class="card">
                 <div class="card-header">Welcome!</div>
                   <div class="card-body">
                    @if (session('resent'))
                         <div class="alert alert-success" role="alert">
                            {{ __('A fresh mail has been sent to your email address.') }}
                        </div>
                    @endif
                    {!! $fullname !!}

                    <p>Your username:{!! $username !!}</p>
                    <p>Your password:{!! $password !!}</p>
                </div>
                <hr/>
                 <p>Thank you to join us, </p>
            </div>
        </div>
    </div>
</div>
</body>
</html> 