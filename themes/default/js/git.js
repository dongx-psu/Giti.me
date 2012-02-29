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
	t='<li id="key_'+ key_data.id +'" class="clearfix">' + (key_data.title);
	t=t + '<a class="minibutton danger" href="#" data-method="delete"><span>Delete</span></a>';
	t=t + '<a class="minibutton" href="#" data-method="edit"><span>Edit</span></a></li>';
	$(".setting-box-list").append(t);
	addListener(key_data.id);
}

function erase_table(s_id){
	$("li#key_"+s_id).remove();
}

function add_key(title,key){
	console.log(URLencode($.base64.encode(key)));
	$.ajax({
		//post add method
		url: "ssh_key.php",
		type: "POST",
		data: 'method=create&title=' + URLencode($.base64.encode(title)) + '&key='+ URLencode($.base64.encode(key)),
		async: true,
		success: function(data){
			if (data.success) {
//				alert("Key is added successfully!")
				make_table(data.data);
			} else {
				alert('Error : '+ data.message);
			}
		},
		error: function(){
			alert('Connection error!');
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
	t='';
	t=t+'<dl class="form"><dt><label>Title</label></dt><dd><input name="edit_title" id="edit_title" type="text" size="30"/></dd></dl>';
	t=t+'<dl class="form"><dt><label>Key</label></dt><dd><textarea name="edit_key" id="edit_key" rows="20" cols="40"></textarea></dd></dl>';
	t=t+'<div class="form-actions"><button id="edit-confirm" class="minibutton" type="submit"><span>Confirm</span></button>or<a id="close-popup" href="#">Cancel</a></div>';
	$("#edit_box").html(t);
	$("#edit_box").bPopup();
	$("#close-popup").click(function(){
		$("#edit_box").bPopup().close();
	})
	$.ajax({
		url: "ssh_key.php",
		type: "GET",
		async: true,
		data: 'id='+s_id,
		success: function(data){
			if (data.success) {
				$("input#edit_title").val(decode(data.data.title,'ENT_QUOTES'));
				$("textarea#edit_key").val(decode(data.data.ssh_key,'ENT_QUOTES'));				
			} else {
				alert("Cannot get the keys!");
			}
		},
		error: function(){
			alert('Connection error!');
		},
		dataType: "json"
	});
}

function edit_key(s_id){
	call_edit_popup(s_id);
	$("#edit-confirm").click(function(){
		title=$("input#edit_title").val();
		key=$("textarea#edit_key").val();
		$.ajax({
			url: "ssh_key.php",
			type: "POST",
			data: 'method=update&id=' + s_id + "&title=" + URLencode($.base64.encode(title)) + '&key='+ URLencode($.base64.encode(key)),
			async: true,
			success: function(data) {
				if (data.success) {
					t=data.data.title+'<a class="minibutton danger" href="#" data-method="delete"><span>Delete</span></a>';
					t=t + '<a class="minibutton" href="#" data-method="edit"><span>Edit</span></a>';						
					$("li#key_"+s_id).html(t);
					addListener(s_id);
					alert("Updated!");					
				} else {
					alert("Error : " + data.message);
				}
			},
			error: function(){
				alert('Connection error!');
			},
			dataType: "json"			
		}); //end of ajax
		$("#edit_box").bPopup().close();
	});
}

function del_key(s_id){
	var r=confirm("Are you sure to delete?");
	if (r==true) {
		$.ajax({
			url: "ssh_key.php",
			type: "POST",
			data: 'method=delete&id=' + s_id,
			async: true,
			success: function(data) {
				if (data.success) {
					alert('Delete successfully!')
					erase_table(s_id);
				} else {
					alert('Error : '+data.message);
				}
			},
			error: function(){
				alert('Connection error!');
			},
			dataType: "json"			
		}); //end of ajax
	}
}



$(document).ready(function(){
	//$.ajax({
		//get key pages here
	//});
	$.ajax({
		url: "ssh_key.php",
		type: "GET",
		async: true,
		success: function(data){
			if (data.success) {
				$("a.section-head").html(data.display_name);
				for (i=0; i < (data.data.length); ++i){
					make_table(data.data[i]);
				}
			} else {
				alert("Cannot get the keys!");
			}
		},
		error: function(){
			alert('Connection error!');
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
		$("#add-form").toggle(100);
		$("#add_key_action").show();
		$("input#ssh_title").val('');
		$("textarea#ssh_key").val('');
	});
	//listen add_key_post
	$("button[type=submit]").click(function(){
		add_key($("input#ssh_title").val(),$("textarea#ssh_key").val());
		$("#add-form").toggle(100);
		$("#add_key_action").show();
		$("input#ssh_title").val('');
		$("textarea#ssh_key").val('');

	});
	
});

