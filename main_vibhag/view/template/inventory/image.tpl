<?php echo $header; ?><?php echo $column_left; ?>
<?php //secho "<pre>"; print_r($csv_data); ?>

<div id = "content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="panel panel-primary">
				<div class="panel-heading">Image Import Panel</div>
				<div class="panel-body">
					<form id ="form_with_file">
						<div class ="col-sm-2">
							<div class = "form-group required">
								<input class="form-control" id="seller_name" name="submit_seller"/>
							</div>
							<input type="hidden" name="id_field" id = "id_field">

							<input type="hidden" name="user_id" id = "user_id" value="<?php echo $user_id; ?>" >


							<button class="btn btn-success btn-sm" type ="submit" id = "submit" style="float: left;" ><span class="glyphicon glyphicon-button"></span> Submit</button>
							
						</div>						
							<div class ="col-sm-4">
								<div class = "form-group required">
									<input type = "file"  id="file" name="myzip"  class="btn btn-default btn-sm" class = "form-control"/>				
							</div>
						</div>
					</div>
					</form>		
					<br>
					<div id="progress-wrp">
						<div class="progress-bar" role="progressbar" style="width:0%"></div ><div class="status">0%</div>
					</div>			
					<div id="height"></div>

					<div>
						<div style="display: inline-block;"><div>
							<div id = "result">
								<div class='table-responsive'>
								
								</div>
							</div>	
						</div></div>
						<div style="display: inline-block;"><div>
							<div  id = "size_change">
								<div class='table-responsive'>
								
								</div>
							</div>	
						</div>		</div>
					</div>
					<button id="show_result" ></button>
					
								
				</div>
					<div id = "progress_middle">
						<div class = "progress_load"><b>Processing Image. please wait... </b></div>
					</div>
			</div>
		</div>
	</div>

					
<?php echo $footer; ?>

  <script>
	var result = [];
	$('#seller_name').on('keyup',function(){
		if($(this).val().trim().length == 0){
			$('input[name="submit_seller"]').val();
		}
	});

	$('#seller_name').autocomplete({
      'source': function(request, response) {
         let json = [];
         if(request.length > 0){
             if(typeof request == 'string'){
                request = request.toLowerCase();
             }
             let sellers = <?php echo json_encode($sellers); ?>;
             $(sellers).each(function(ind,element){
                 let nickname = element['nickname'].toLowerCase();
                 let name = element['name'].toLowerCase();
                 let data;
                 if( nickname.search(request) >= 0 ){
                     data = $.parseJSON('{"label":"' + element['nickname'] + '","value":"' + element['seller_id'] + '"}');
                 }
                 if( element['seller_id']  == request ){
                     data = $.parseJSON('{"label":"' + element['name'] + '","value":"' + element['seller_id'] + '"}');
                 }
                 if( name.search(request) >= 0){
                     data = $.parseJSON('{"label":"' + element['name'] + '","value":"' + element['seller_id'] + '"}');
                 }
                 if( data ){
                     json.push(data);
                 }
             });
         }
         response(json);
      },
      'select': function(item) {
        $('#seller_name').val(item['label']);
        $('input[name="submit_seller"]').val(item['label']);
        $('input[name=\'id_field\']').val(item['value']);

      }
    });
	  
	var count = 0;
	var progress_bar_id 		= '#progress-wrp'; //ID of an element for response output
	var progress_bar_id_2 		= '#progress-wrp2'; //ID of an element for response output
	var my_form_id 				= '#form_with_file'; //ID of an element for response output
	var result_output 			= '#result'; //ID of an element for response output */
	var storing_result = []; //array to store the results for displaying after the page has been refreshed has been displayed
	
	$('#form_with_file').on('submit',function(e){
		
		$('#progress-wrp').show();
		$(progress_bar_id + " .status").text(0 +"%");
		$(progress_bar_id +" .progress-bar").css("width", + 0 +"%")
		$('#size_change').empty();
		$("#result").empty();

		if($('#file').val().replace(/^.*[\\\/]/, '').split('.')[1] !== "zip") {
	
			alert("please select only zip file");
			return false;
			
		} else if($('#seller_name').val() == "") {
			alert("please select seller");
			return false;
		} else if($('#id_field').val() == "") {
			alert("please select valid seller");
			return false;
		}

		count++;
		
		if(count >= 2) {
			$("#result").empty();
		} 
		
		e.preventDefault();
		
		var file = document.getElementById('file').files[0];
		if((file.size)/1000000 > 50) {
			alert("Please upload a smaller size zip file. Please break the zip file into smaller files of size less than 50 MB, and upload one-by-one");
			return false;
		}
		var formData = new FormData($(this)[0]);
		
		//var xhr = new XMLHttpRequest();
		//console.log(xhr);
		
		//xhr.open("post", '<?php echo $cdn_url; ?>bulk_image.php?token=<?php echo $this->session->data['token']; ?>?');
		$('#submit').html('uploading');
		$('#submit').attr("disabled", 'disabled');
		var image_upload = 0;
		var url =  'index.php?route=inventory/image/readZip&token=<?php echo $this->session->data['token']; ?>';
        if('<?php echo NGINX_ENABLED; ?>' == '1'){
            url = '<?php echo $cdn_url; ?>bulk_image.php?token=<?php echo $this->session->data['token']; ?>';
		}
		$.ajax({
			xhr: function(){
				//upload Progress
				var xhr = $.ajaxSettings.xhr();
				if (xhr.upload) {
					xhr.upload.addEventListener('progress', function(event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						
						if (event.lengthComputable) {
							percent = Math.ceil(position / total * 100);
						}
						//update progressbar
						$(progress_bar_id +" .progress-bar").css("width", + percent +"%");
						$(progress_bar_id + " .status").text(percent +"%");
					}, false);	 
				}
				return xhr;
				}, 
				
				url:  url,
				type: 'POST',
				data: formData,
            	timeout: 300000,
				mimeType: 'form-data',
				contentType:false,
				processData:false,
				datatype : 'json',	
				
				success: function (data) {	
					
					$('#progress-wrp').hide();
					storing_result = data.slice(0);
					var data = JSON.parse(data);
					var array = $.map(data, function(value, index) {
						return [value];
					});
					var error = data.message.error;
					var image_error = data.message.image_error;
					
					if(data.message != undefined && error == undefined) {
						alert("Their is issue with the file size!");
						return false;
					}
					
					if(!error[1] && image_error.length != 0) {
						alert(error[0])
						$('#submit').attr("disabled", false);
						$('#submit').html('submit');
						document.getElementById("file").value = "";
					}
					
					if(error[1]) {
						alert(error[0]);
						$('#submit').attr("disabled", false);
						$('#submit').html('submit');
						document.getElementById("file").value = "";

					} else {
						
						if(data.message.image_error.length != 0) {
							
							var trHTML = "";
							trHTML += "<p class = 'error'>Following images have failed validation!";
							trHTML += "<table class='table table-bordered table-hover'><th class='text-left'><a href='#'>S.No</a></th><th class = 'text-left'><a href='#'>Image Name</a></th><th class = 'text-left'> <a href='#'>Image Error</a></th>";
							for(var i =0 ;i < data.message.image_error.length; i++ ){
								var j = i+1;
								  trHTML += '<tr><td width="10%" class = "common">' + j +'</td><td width="10%" class = "common">' + data.message.image_error[i] +'</td><td class = "common" width="10%">' + 
											data.message.msg_error[i] +'</td></tr>';
							}
							trHTML+="</table>" ;
							$("#result").append($(trHTML));   
							
						} else if(data.message.image_error.length == 0){
							
							var trHTML = "";
							trHTML += "<p class = 'success'>All images have passed validation!</p>";
							$("#result").append($(trHTML)); 
							$('#submit').attr("disabled", false);
							$('#submit').html('submit');
							document.getElementById("file").value = "";
							alert("images submitted successfully");

						}
						
						if(data.message.earlier_size.length != 0){
							
							var trHTML2 = "";
							trHTML2 += "<p class = 'success'>Following images have undergone size changes!";
							trHTML2 += "<table class='table table-bordered table-hover'><th class='text-left'><a href='#'>S.No</a></th><th class = 'text-left'><a href='#'>Image Name</a></th><th class = 'text-left'> <a href='#'>Image Original Size</a></th><th class = 'text-left'> <a href='#'>Image Final Size</a></th>";
							for(var i =0 ;i < data.message.earlier_size.length; i++ ){
								var j = i+1;
								  trHTML2 += '<tr><td width="10%" class = "common">' + j +'</td><td width="10%" class = "common">' + data.message.images_size_change[i] +'</td><td class = "common" width="10%">' + 
											data.message.earlier_size[i] +'</td><td class = "common" width="10%">' + 
											data.message.new_size[i] +'</td></tr>';
							}
							trHTML2+="</table>" ;
							$("#size_change").append($(trHTML2)); 

						} else if(data.message.earlier_size.length == 0){
							var trHTML2 = "";
							trHTML2 += "<p class = 'success'>No size change for images observed!</p>";
							$("#size_change").append($(trHTML2)); 
						}
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText + "\r\nPlease try again!");
                    $('#submit').attr("disabled", false);
                    $('#submit').html('submit');
				}
			});
			
//			xhr.upload.onprogress = function (e) {
//				if (e.lengthComputable) {
//					if(e.total == e.loaded) {
//						$('.progress_load').show();
//					}
//				}
//			}
//
//			xhr.onloadstart = function (e) {
//				console.log("start");
//				if (e.lengthComputable) {
//					console.log(e.loaded+  " /on load start " + e.total)
//				}
//			}
//
//			xhr.onloadend = function (e) {
//				console.log("end");
//				console.log( new Date());
//				$('#progress_bar_loader').hide();
//				$('.progress_load').hide();
//			}
			
			//xhr.send(formData);
	});
	
</script>
<style>
	
.progress_load {
	display : none;
}

#show_result {
	display : none;
}
.error {
	color : red;
}

.success {	
	color : green;
}

#progress_middle {
	
	align : middle;
}
.common {
	color : black;
}
#progress-wrp {
    border: 1px solid #0099CC;
    padding: 1px;
    position: relative;
    border-radius: 3px;
    margin: 10px;
    text-align: left;
    background: #fff;
    box-shadow: inset 1px 3px 6px rgba(0, 0, 0, 0.12);
}
#progress-wrp .progress-bar{
    height: 20px;
    border-radius: 3px;
    background-color: #548a9e;;
    width: 0;
    box-shadow: inset 1px 1px 10px rgba(0, 0, 0, 0.11);
}
#progress-wrp .status{
    top:3px;
    left:50%;
    position:absolute;
    display:inline-block;
    color: #000000;
}
</style>

<?php echo $footer; ?>
