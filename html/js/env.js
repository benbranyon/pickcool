var API_ENDPOINT="/api/v1";
var DEBUG=true;

if(DEBUG)
{
  console.log("***DEBUGGING MODE ENABLED***");
} else {
  var console = {
    'log': function() {},
  };
}