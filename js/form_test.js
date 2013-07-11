jQuery.fn.exists = function() {
	return ($(this).length > 0);
};
var fields = ["In_First_Name", "In_Last_Name", "In_Company_Name",
		"In_Email", "In_Phone_1", "In_Phone_2", "In_Phone_3"];
var field_error = false;

$(function() {
	var firstInput = document.getElementById("In_First_Name");
	if (firstInput && firstInput.focus != "undefined") {
		firstInput.focus();
	}

	$(".live-chat").colorbox({width:"60%", height:"60%", iframe:true});	
	//$.query.get("X1");
	//$.query.get("X2");
	//$.query.get("X3");
	$.query.load(location.href);
	$.each($.query.get(), function(key, value) {
		var k = key.toUpperCase();
		if( k == "X1" || k == "X2" || k == "X3"){
			$("#" + k).val(value);
		}
	});
	//var post_url = "application/post.php?from=Lee";
	var post_url = "application/post.php";

	$.each(fields, function(i, k) {
		$('#' + k).bind('change', function() {
			if ($.trim($(this).val()) == "") {
				if (!$("p.errors").exists()) {
					$('div.top')
							.after('<p class="errors">Please correct the errors below:</p>');
				}
				$(this).parent().parent().addClass("error");
			}
			else{
				$(this).parent().parent().removeClass("error");
			}
		});
	});
	
	$('#In_Phone_1').bind('keyup', function(){
		var v = $(this).val();

		if( v.length <= 3 ){
			if(!v.match(/^[0-9]{0,3}$/)){
				$(this).parent().parent().addClass("error");
				return;
			}
		}

		if (v.length == 3 ){
			$("#In_Phone_2").focus();
			$(this).parent().parent().removeClass("error");
			return;
		}
	});
	
	$("#In_Phone_2").bind("keyup", function(){
		var v = $(this).val();

		if( v.length <= 3 ){
			if(!v.match(/^[0-9]{0,3}$/)){
				$(this).parent().parent().addClass("error");
				return;
			}
		}

		if (v.length == 3 ){
			$("#In_Phone_3").focus();
			$(this).parent().parent().removeClass("error");
			return;
		}
	});

	$("#In_Phone_3").bind("keyup", function(){
		var v = $(this).val();

		if( v.length <= 4 ){
			if(!v.match(/^[0-9]{0,4}$/)){
				$(this).parent().parent().addClass("error");
				return;
			}
		}
		
		if(v.length == 4 ){
			$("#In_Phone_4").focus();
			$(this).parent().parent().removeClass("error");
			return;
		}
	});
	$("#In_Phone_4").bind("keyup", function(){
		var v = $(this).val();
		
		if( v.length <= 6){
			if(!v.match(/^[0-9]{0,6}$/)){
				$(this).parent().parent().addClass("error");
				return;
			}
			else{
				$(this).parent().parent().removeClass("error");
				return;
			}
		}
	});


	$("form").submit(function() {
				return false;
			});

	$(":submit").bind("click", function() {
				var frm_opt = {
					url : post_url,
					type : 'post',
					dataType : 'json',
					beforeSubmit : check_error_status,
					success : showResponse
				};
				$('#mainFrm').ajaxForm(frm_opt);
			});
});

function check_error_status() {
	var fr = true;
	$("input_area").mask("Checking the data your submit....");
	$.each(fields, function(i, f) {
		var e = $("#" + f);
		if ($.trim(e.val()) == "") {
			e.parent().parent().addClass("error");
			fr = false;
		}
		else{
			if( ((f == "In_Phone_1" || f == "In_Phone_2") && !e.val().match(/^[0-9]{3}$/)) ||
					(f == "In_Phone_3" && !e.val().match(/^[0-9]{4}$/)) ||
					( !$("#In_Phone_4").val().match(/^[0-9]{0,6}$/)) ){
					e.parent().parent().addClass("error");
					fr = false;
				}
				else{
					e.parent().parent().removeClass("error");
				}
		}
	});
	if(!fr){
		$("#input_area").unmask();
		if (!$("p.errors").exists()) {
			$('div.top').after('<p class="errors">Please correct the errors below:</p>');
		}
	}
	else{
		$("#input_area").mask("Submitting the data you input....");
		$("p.errors").remove();
	}
	return fr;
}

// post-submit callback
function showResponse(responseText, statusText, xhr, $form) {
	var d = responseText;
	if (d.error) {
		$("#input_area").unmask();
		if (!$("p.errors").exists()) {
			$('div.top').after('<p class="errors">Please correct the errors below:</p>');
		}
		$("#" + d.key).parent().parent().addClass("error");
	}
	else {
		//<form action="http://go.pardot.com/l/7982/2011-07-19/22H5" method="post">
		//$("p.errors").remove();
		//window.location.href = d.message;		
	}
}
