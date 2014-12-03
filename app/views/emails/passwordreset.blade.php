<h1>Reset your password</h1>

<p>Hello {{$user['username']}},</p>

<p>We have received a request to reset your password. If you did not initiate this request, please ignore this message.</p>
<a href='{{ URL::to('users/reset_password/'.$token) }}'>
    {{ URL::to('users/reset_password/'.$token)  }}
</a>

<p>Thank you very much!</p>
<p>-The {{$_ENV['APP_NAME']}} Team</p>
