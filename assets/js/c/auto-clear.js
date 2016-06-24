/*
Onclick clean input field 
*/
function clickclear(thisfield, defaulttext) 
{
	if (thisfield.value == defaulttext) 
	{
		thisfield.value = "";
	}
}
function clickrecall(thisfield, defaulttext) 
{    
	if (thisfield.name == "activation_code") 
	{
		if (thisfield.value == "") 
		{
			  thisfield.value = defaulttext;
		}
	}
	else
	{
		if (thisfield.value == "") 
		{
			  thisfield.value = document.getElementById('hdn_serach_value').value;
		}
	}

}
