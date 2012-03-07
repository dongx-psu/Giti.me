/*
 * Author: yinger650 QQ: 6019784 Email:yinger650@gmail.com
 * Last update: 2012/2/29
 */

function addListener(s_id){
	$("li#key_"+s_id+" a[data-method=delete]").click(function(){
		del_key(s_id);
	});
	$("li#key_"+s_id+" a[data-method=edit]").click(function(){
		edit_key(s_id);
	});
}

function URLencode(sStr) 
{
    return escape(sStr).replace(/\+/g, '%2B').replace(/\"/g,'%22').replace(/\'/g, '%27').replace(/\//g,'%2F');
}

function make_table(key_data){
	if ($("a[data-method=edit]").length == 0) $("#table-hint").remove();
	t='<li id="key_'+ key_data.id +'" class="clearfix">' + (key_data.title);
	t=t + '<a class="minibutton danger" href="#" data-method="delete"><span>Delete</span></a>';
	t=t + '<a class="minibutton" href="#" data-method="edit"><span>Edit</span></a></li>';
	$(".setting-box-list").append(t);
	addListener(key_data.id);
}

function make_empty_table(){
	t='<li id="table-hint" class="clearfix">Please add a new SSH_Key.</li>';
	$(".setting-box-list").append(t);
}

function erase_table(s_id){
	$("li#key_"+s_id).remove();
	if ($("a[data-method=edit]").length == 0) make_empty_table();
}

function add_key(title,key){
	$(".pjax-loading").fadeIn(300);
	$.ajax({
		//post add method
		url: "ssh_key.php",
		type: "POST",
		data: 'method=create&title=' + URLencode(base64_encode(title)) + '&key='+ URLencode(base64_encode(key)),
		async: true,
		success: function(data){
			if (data.success) {
				$(".pjax-loading").fadeOut(300);
				throw_warning("Key is added successfully!")
				make_table(data.data);
				$("input#ssh_title").val('');
				$("textarea#ssh_key").val('');
				$("#add-form").toggle(100);
				$("#add_key_action").show();
			} else {
				$(".pjax-loading").fadeOut(300);
				throw_error(data.message);
			}
		},
		error: function(){
			throw_error('Connection error!');
		},
		dataType: "json"
	});
}

var decode = function (string, quote_style) {
   string = string.toString();
   string = string.replace(/&amp;/g,'&');
   string = string.replace(/&lt;/g,'<');
   string = string.replace(/&gt;/g,'>');
   if (quote_style == 'ENT_QUOTES') {
       string = string.replace(/&quot;/g,'"');
       string = string.replace(/&#039;/g,"'");
   } else if (quote_style != 'ENT_NOQUOTES') {
       string = string.replace(/&quot;/g,'"');
   }
   return string;
}

function call_edit_popup(s_id){
	var t;
	t='<span class="close button-top-right">close</span>';
	t=t+'';
	t=t+'<dl class="form"><dt><label>Title</label><div id="edit-title-error"style="color:#990000;display:none;">Input invalid.</div></dt><dd><input name="edit_title" id="edit_title" type="text" size="30"/></dd></dl>';
	t=t+'<dl class="form"><dt><label>Key</label><div id="edit-key-error"style="color:#990000;display:none;">Input invalid.</div></dt><dd><textarea name="edit_key" id="edit_key" rows="20" cols="40"></textarea></dd></dl>';
	t=t+'<div class="form-actions"><button id="edit-confirm" class="minibutton" type="submit"><span>Confirm</span></button><span>or</span><a id="close-popup" href="#">Cancel</a></div>';
	$("#edit_box").html(t);
	$("#edit_box").bPopup();
	$("#close-popup").click(function(){
		$("#edit_box").bPopup().close();	
	})
	$(".close.button-top-right").click(function(){
		$("#edit_box").bPopup().close();
	})
	$.ajax({
		url: "ssh_key.php",
		type: "GET",
		async: true,
		data: 'id='+s_id,
		success: function(data){
			if (data.success) {
				$("#edit_box").css("opacity","1");
				$("input#edit_title").val(decode(data.data.title,'ENT_QUOTES'));
				$("textarea#edit_key").val(decode(data.data.ssh_key,'ENT_QUOTES'));				
			} else {
				$("#edit_box").css("opacity","1");
				throw_error("Cannot get the keys!");
			}
		},
		error: function(){
			throw_error('Connection error!');
		},
		dataType: "json"
	});
}

function edit_key(s_id){
	call_edit_popup(s_id);
	$("#edit-confirm").click(function(){
		$("#edit-title-error").fadeOut(300);
		$("#edit-key-error").fadeOut(300);
		title=$("input#edit_title").val();
		key=$("textarea#edit_key").val();
		var flag=true;
		var blank = /^[\s\t\r\n]*$/;
		if (blank.test(title)) {$("#edit-title-error").fadeIn(300);flag=false;}
		if (blank.test(key)) {$("#edit-key-error").fadeIn(300);flag=false;}
		if (flag) {
			$(".pjax-loading").fadeIn(300);
			$.ajax({
				url: "ssh_key.php",
				type: "POST",
				data: 'method=update&id=' + s_id + "&title=" + URLencode(base64_encode(title)) + '&key='+ URLencode(base64_encode(key)),
				async: true,
				success: function(data) {
					$(".pjax-loading").fadeOut(300);
					if (data.success) {
						t=data.data.title+'<a class="minibutton danger" href="#" data-method="delete"><span>Delete</span></a>';
						t=t + '<a class="minibutton" href="#" data-method="edit"><span>Edit</span></a>';						
						$("li#key_"+s_id).html(t);
						addListener(s_id);
						throw_warning("Updated!");					
					} else {
						$(".pjax-loading").fadeOut(300);
						throw_error(data.message);
					}
				},
				error: function(){
					//$(".pjax-loading").fadeOut(300);			
					throw_error('Connection error!');
				},
				dataType: "json"			
			}); //end of ajax
			$("#edit_box").bPopup().close();
		}
	});
}

function del_key(s_id){
	var r=confirm("Are you sure to delete?");
	if (r==true) {
		$(".pjax-loading").fadeIn(300);
		$.ajax({
			url: "ssh_key.php",
			type: "POST",
			data: 'method=delete&id=' + s_id,
			async: true,
			success: function(data) {
				$(".pjax-loading").fadeOut(300);
				if (data.success) {
					throw_warning('Delete successfully!')
					erase_table(s_id);
				} else {
					$(".pjax-loading").fadeOut(300);
					throw_error(data.message);
				}
			},
			error: function(){
				throw_error('Connection error!');
			},
			dataType: "json"			
		}); //end of ajax
	}
}

function check_form(title,key){
	var blank = /^[\s\t\r\n]*$/;
	if (blank.test(title) || blank.test(key)) {
		throw_error("Key is invalid. Ensure you've filled the form correctly.");
		return false;
		} else return true;
}

var vanish,tmp_vanish,error_vanish,error_tmp_vanish;

function throw_error(message) {
	clearTimeout(error_vanish);
	clearTimeout(error_tmp_vanish);
	$("#error-text").hide();
	$("#warning-text").hide();
	$("#error-text").html("");
	t = message+'<span class="close">close</span>';
	$("#error-text").html(t);
	$("#error-text").fadeTo(300,1);
	$(".close").click(function() {$("#error-text").fadeOut(100)});
}

function throw_warning(message) {
	clearTimeout(vanish);
	clearTimeout(tmp_vanish);
	$("#warning-text").hide();
	$("#error-text").hide();
	$("#warning-text").html("");
	t = message+'<span class="close">close</span>';
	$("#warning-text").html(t);
	$("#warning-text").fadeTo(300,1);
	vanish = setTimeout(function(){$("#warning-text").fadeTo(1000,0);tmp_vanish=setTimeout('$("#warning-text").hide(200)',1300)},2000);
	$("#warning-text").mouseover(function(){
		clearTimeout(vanish-vanish);
		clearTimeout(tmp);
		$("#warning-text").fadeTo(300,1);		
	})
	$(".close").click(function() {$("#warning-text").fadeOut(300)});
}
$(document).ready(function(){
	$.ajax({
		url: "ssh_key.php",
		type: "GET",
		async: true,
		success: function(data){
			if (data.success) {
				$("a.section-head").html(data.display_name);
				if ((data.data) == null) make_empty_table() 
				else for (i=0; i < (data.data.length); ++i) make_table(data.data[i]);
			} else {
				throw_error("Cannot get the keys!");
			}
		},
		error: function(){
			throw_error('Connection error!');
		},
		dataType: "json"
	});
	//listen add_key_button
	$("#add_key_action").click( function(){
	$("#add-form").toggle(100);
	$(this).hide();
	});
	//listen clear_button
	$("#cancel_add_key").click( function(){
		$("#add-form").toggle();
		$("#add_key_action").show();
		$("input#ssh_title").val('');
		$("textarea#ssh_key").val('');
	});
	//listen add_key_post
	$("button[type=submit]").click(function(){
		if (check_form($("input#ssh_title").val(),$("textarea#ssh_key").val())) {
			add_key($("input#ssh_title").val(),$("textarea#ssh_key").val());
		}
	});
});

