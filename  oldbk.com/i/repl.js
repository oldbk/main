Cufon.replace("h2");
Cufon.replace("h3");
Cufon.replace("h4");
Cufon.replace("h5");
Cufon.replace("h6");
Cufon.replace("h7");
Cufon.replace("h8");

$(function() {
   $('a.lightbox').lightBox();
  });
function clearText(field)
{
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

$(document).ready(function() {
    $('#box2').cycle({
		    fx:     'fade',
		    speed:   500,
		    timeout: 4000,
		    next:   '#next1',
		    prev:   '#prev1',
		    pause:   1
	});
});

