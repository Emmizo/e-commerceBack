<!DOCTYPE html>
<html>
<head>
 <title>E-Share</title>
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
  <style type="text/css">
      .card-box{
        background:#ffb400;
        width:100%;
        box-shadow: 0 0 20px rgba(0, 0, 0,0.13);
        list-style-type: none;
        border:solid 2px #ffffff;
        color:#ffffff
      }
      .order-box{
        display: flex;
        flex-wrap: wrap;
      }
  </style>
</head>
<body>
 
<p>Your order received well!</p>
 <div class="container">
     <div class="row justify-content-center">
         <div class="col-md-8">
             <div class="card">
                 <div class="card-header">Welcome!</div>
                   <div class="card-box">
                    @if (session('resent'))
                         <div class="alert alert-success" role="alert">
                            {{ __('A fresh mail has been sent to your email address.') }}
                        </div>
                    @endif
                    <div class="order-box">
                        <div>
                            Comment: {!! $comments !!}
                        </div>
                        <hr/>
                        <div>
                            address: {!! $address !!}
                        </div>
                        <hr/>
                        <div>
                            Items: {!! $cartItems !!}
                        </div>
                        <hr/>
                   <div class="text-success">Total amount: {!! $total !!} FRW</div>
                   </div>
                </div>
                <hr/>
                 <p>Thank you to join us, </p>
            </div>
        </div>
    </div>
</div>
</body>
</html> 