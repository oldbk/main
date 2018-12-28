 var x=window.location.href;
  var y=window.location.search;
  var z=window.location.pathname;
  if(y=='?page=1')
  {
  	location.href='http://oldbk.com/';
  }
  if(z=='/index.php')
  {
  	location.href='http://oldbk.com/'+y;
  }