function notify_email()
{
	var post_url = $F('email_notify_url');
	var email = $F('email_notify_text');
	var product_id = $F('email_notify_product');
	$('email_notify_text').removeClassName('validation-failed');
	document.getElementById("email_notify_text").placeholder = "Enter The Email Id";
	
	if(email == "")
	{
		$('email_notify_text').addClassName('validation-failed');
		//$('email_notify_text').value = "Enter the Email-Id"
		$('email_notify_text').focus();
		return false;
	}
	
	if(!validateEmail(email))
	{
		$('email_notify_text').addClassName('validation-failed');
		$('email_notify_text').value = "";
		document.getElementById("email_notify_text").placeholder = "Enter Valid Email Id";
		$('email_notify_text').focus();
		return false;
	}
	
	//** loading icon showing
	$('email_notify_loader').setStyle({display: 'block'});
	new Ajax.Request(post_url, {
		method:'post',
		parameters: {email:email, product_id:product_id, send_email:0},
		onSuccess: function(transport) {
			var response = transport.responseText || "no response text";
			obj = JSON.parse(response);
			if(obj.insert_id && obj.success == 200)
			{
				$('email_notify_text').value = "";
				$('email_notify_success').setStyle({display: 'block'});
				setTimeout(function(){$('email_notify_success').setStyle({display: 'none'});}, 2500);
			}
			else 
			{
				$('email_notify_text').addClassName('validation-failed');
				$('email_notify_text').focus();
			}
			$('email_notify_loader').setStyle({display: 'none'});
		},
		onFailure: function() { alert('Something went wrong...'); }
	});

}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}