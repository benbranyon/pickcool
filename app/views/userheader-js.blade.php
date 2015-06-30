var userheader = {{json_encode(View::make('userheader')->render())}};
document.getElementById('userheader').innerHTML = userheader;
