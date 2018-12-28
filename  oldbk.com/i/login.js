	   function check_login()
		{
			alert('TEST');
		}
	   function switch_msg()
	   {
	    if((document.getElementById("msg_01").style.display=="none")  && (document.getElementById("msg_02").style.display=="none"))
	    {
	     document.getElementById("msg_01").style.display="block";
	     document.getElementById("msg_02").style.display="none";
	     document.getElementById("msg_03").style.display="none";
	    }
	    else if((document.getElementById("msg_01").style.display=="block")  && (document.getElementById("msg_02").style.display=="none"))
	    {
	     document.getElementById("msg_01").style.display="none";
	     document.getElementById("msg_02").style.display="block";
	     document.getElementById("msg_03").style.display="none";
	    }
	    else
	    {
	     document.getElementById("msg_01").style.display="none";
	     document.getElementById("msg_02").style.display="none";
	     document.getElementById("msg_03").style.display="block";
	    }
	   }
