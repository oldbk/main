function get_cookie()
{
	return document.cookie;
};
function set_fields(val1,val2,val3)
{
	document.getElementById('battle').value=val1;
	document.getElementById('phpsessionid').value=val2;
	document.getElementById('secondsession').value=val3;
	document.second_form.submit();
};