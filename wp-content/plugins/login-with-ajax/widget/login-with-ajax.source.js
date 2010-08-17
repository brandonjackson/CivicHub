jQuery(document).ready( function($) {
 	//Oh well... I guess we have to use jQuery ... if you are a javascript developer, consider MooTools if you have a choice, it's great!
	$('#LoginWithAjax_Form').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		//Sort out url
		var url = $('#LoginWithAjax_Form').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Form *[name]');
		postData['login-with-ajax'] = 'login'; //So that there is a JS fallback mechanism
		//Make Ajax Call
		$.post(url, postData, function(data){
			lwaAjax( data, 'LoginWithAjax_Status', '#login-with-ajax' );
			if(data.result === true){
				//Login Successful - Extra stuff to do
				if( data.widget != null ){
					$.get( data.widget, function(widget_result) {
						$('#LoginWithAjax').replaceWith(widget_result);
						$('#LoginWithAjax_Title').replaceWith($('#LoginWithAjax_Title_Substitute').text());
					});
				}else{
					if(data.redirect == null){
						window.location.reload();
					}else{
						window.location = data.redirect;
					}
				}
			}
		}, "json");
	});	
	
 	$('#LoginWithAjax_Remember').submit(function(event){
		//Stop event, add loading pic...
 		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax');
		//Sort out url
		var url = $('#LoginWithAjax_Remember').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Remember *[name]');
		//Make Ajax Call
		$.post(url, postData, function(data){
			lwaAjax( data, 'LoginWithAjax_Status', '#login-with-ajax' );
		}, "json");
	}); 	

	$('#LoginWithAjax_Register form').submit(function(event){
		//Stop event, add loading pic...
		event.preventDefault();
		$('<div class="LoginWithAjax_Loading" id="LoginWithAjax_Loading"></div>').prependTo('#LoginWithAjax_Register');
		//Sort out url
		var url = $('#LoginWithAjax_Register form').attr('action');
		//Get POST data
		var postData = getPostData('#LoginWithAjax_Register form *[name]');
		$.post(url, postData, function(data){
			//variable status not here anymore
			lwaAjax( data, 'LoginWithAjax_Register_Status', '#LoginWithAjax_Register' );
		}, "json");
		/*$.ajax({
		  type: 'POST',
		  url: url,
		  data: postData,
		  success: function(data){
				//variable status not here anymore
				lwaAjax( data, 'LoginWithAjax_Register_Status', '#LoginWithAjax_Register' );
		  },
		  dataType: "json"
		});*/
	});	
 	
	//Visual Effects for hidden items
	//Remember
	$('#LoginWithAjax_Remember').hide();
	$('#LoginWithAjax_Links_Remember').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').show('slow');
	});
	$('#LoginWithAjax_Links_Remember_Cancel').click(function(event){
		event.preventDefault();
		$('#LoginWithAjax_Remember').hide('slow');
	});
	
	//Handle a AJAX call for Login, RememberMe or Registration
	function lwaAjax( data, statusElement, prependTo ){
		$('#LoginWithAjax_Loading').remove();
		if( data.result === true || data.result === false ){
			if(data.result === true){
				//Login Successful
				if( $('#'+statusElement).length > 0 ){
					$('#'+statusElement).attr('class','confirm').html(data.message);
				}else{
					$('<span id="'+statusElement+'" class="confirm">'+data.message+'</span>').prependTo( prependTo );
				}
			}else{
				//Login Failed
				//If there already is an error element, replace text contents, otherwise create a new one and insert it
				if( $('#'+statusElement).length > 0 ){
					$('#'+statusElement).attr('class','invalid').html(data.error);
				}else{
					$('<span id="'+statusElement+'" class="invalid">'+data.error+'</span>').prependTo( prependTo );
				}
				//We assume a link in the status message is for a forgotten password
				$('#'+statusElement).click(function(event){
					event.preventDefault();
					$('#LoginWithAjax_Remember').show('slow');
				});
			}
		}else{	
			//If there already is an error element, replace text contents, otherwise create a new one and insert it
			if( $('#'+statusElement).length > 0 ){
				$('#'+statusElement).attr('class','invalid').html('An error has occured. Please try again.');
			}else{
				$('<span id="'+statusElement+'" class="invalid">An error has occured. Please try again.</span>').prependTo( prependTo );
			}
		}
	}
	
	//Get all POSTable data from form.
	function getPostData(selector){
		var postData = {};
		$.each($(selector), function(index,el){
			el = $(el);
			postData[el.attr('name')] = el.attr('value');
		});
		return postData
	}
});