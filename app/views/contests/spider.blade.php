<html>
<head>
<title>{{$title}}</title>
<meta property="fb:app_id" content="1497159643900204"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="{{{$title}}}"/>
<meta property="og:site_name" content="pick.cool"/>
<meta property="og:url" content="{{{$canonical_url}}}"/>
<meta property="og:description" content="{{{$description}}}"/>
<meta property="og:image" content="{{{$image_url}}}?cachebuster={{uniqid()}}"/>
</head>
<body>
  <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=1497159643900204&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

  <h1><a href="{{{$canonical_url}}}">{{{$title}}}</a></h1>
  <img src="{{{$image_url}}}" width="100"/>
  <p>{{{$description}}}</p>
  <div class="fb-share-button" data-href="{{{$canonical_url}}}" data-layout="button_count"></div>

</body>
</html>