(function() {
  function hookLoad(handler) {
    if (window.addEventListener) {
      window.addEventListener("load", handler, false);
    }
    else if (window.attachEvent) {
      window.attachEvent("onload", handler);
    }
  }
  
  hookLoad(function() {
    var userheader = {{json_encode(View::make('userheader')->render())}};
    document.getElementById('userheader').innerHTML = userheader;
  });

})();