var userheader = {{json_encode(View::make('usercontext.app.header')->render())}};
document.getElementById('userheader').innerHTML = userheader;
