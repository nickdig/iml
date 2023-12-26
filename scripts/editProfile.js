$(document).ready(function(){
	$("#msg-div").html();
	$("#edit-profile").on('submit',function(e){
		
		e.preventDefault(); //avoid to execute the actual submit of the form
		
		var data = new FormData(this);
		
		$.ajax({
			type:"POST",
			url:"scripts/UpdateAccount.php",
			dataType:'json',
			contentType:false,
			processData:false,
			cache:false,
			data: data,
			success: function(d){
				if(d.status == "FAIL"){
					$("#msg-div").html('<div class="alert alert-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>  '+d.msg +' </div>');
					}
					else{
						$("#UserImage").attr('src',d.picture);
						$("#UsersFirstName").html('<b>Name :</b><br>'+d.user_first_name+' '+ d.user_last_name+'</p>');
						$("#msg-div").html('<div class="alert alert-success" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Profile Updated.  </div>');
					}
			},
			error : function(d){
				    $("#msg-div").html('<div class="alert alert-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Please Try again an error has occurred. </div>');
			}
		});	
	
	
	});
});