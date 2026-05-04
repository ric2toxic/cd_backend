<!DOCTYPE html>
<head>
	<!--//Author Divya Porwal
	 	// February 2017 -->

</head>
<style>
		.seller_inventory {
			display: inline-block;
			width: 100%;
			vertical-align: top;
			
		}
		.row {
		  display: flex; /* equal height of the children */
		}

		.col {
		    -webkit-flex: 1;  /* Safari 6.1+ */
			-ms-flex: 1;  /* IE 10 */    
			flex: .3;
			padding: 4em;

		}
		.bt {
			font-size: 12px; !important
		}
		.panel-custom>.panel-cheading {
				color: #fff; !important
				background-color: #337ab7; !important
				border-color: #337ab7;
				/* background-color: #5bb75b; */
				/* background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#62c462), to(#51a351)); */
				/* background-image: -webkit-linear-gradient(top, #62c462, #51a351); */
				background-image: linear-gradient(to bottom, #07335a, #597b98); !important
		}
		.btn-danger {
			color: #ffffff;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
			background-color: #da4f49;
			background-image: -moz-linear-gradient(top, #ee5f5b, #bd362f);
			background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));
			background-image: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
			background-image: -o-linear-gradient(top, #ee5f5b, #bd362f);
			background-image: linear-gradient(to bottom, #ee5f5b, #bd362f);
			background-repeat: repeat-x;
			border-color: #bd362f #bd362f #802420;
			border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}
		
		.glyphiconm {
			position: relative;
			top: 6px; !important
			display: inline-block;
			font-family: 'Glyphicons Halflings';
			font-style: normal;
			font-weight: 400;
			line-height: 1;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		.color1 {
			
			background-color : gray;
		}
		.panel-custom {
			 border-color: #e9eef3; !important
		}
		.font {
			font-size: 11px; !important
		}
		.card-5 {
		 
		 box-shadow: 0 19px 38px rgba(0,0,0,0.30), 0 15px 12px rgba(0,0,0,0.22);

		}
		
		.panel-cheading {
			padding: 8px 12px;
			border-bottom: 1px solid transparent;
			border-top-left-radius: 3px;
			border-top-right-radius: 3px;
		}
		.form1 {
			
			left-margin : 0px;
		}
		#height {
			height : 2vh;
		}
		select.form-control, input[type="text"].form-control, input[type="password"].form-control, input[type="datetime"].form-control, input[type="datetime-local"].form-control, input[type="date"].form-control, input[type="month"].form-control, input[type="time"].form-control, input[type="week"].form-control, input[type="number"].form-control, input[type="email"].form-control, input[type="url"].form-control, input[type="search"].form-control, input[type="tel"].form-control, input[type="color"].form-control {
			 font-size: 10px; !important
			 height: 30px; !important
		}
		select.form-control, input[type="text"].form-control, input[type="password"].form-control, input[type="datetime"].form-control, input[type="datetime-local"].form-control, input[type="date"].form-control, input[type="month"].form-control, input[type="time"].form-control, input[type="week"].form-control, input[type="number"].form-control, input[type="email"].form-control, input[type="url"].form-control, input[type="search"].form-control, input[type="tel"].form-control, input[type="color"].form-control {
			font-size: 10px; !important
			height: 30px; !important
		}
		
		.form-group .download-file-width {
			
			width :  150px;
			overflow-y : hidden;
			
		}
		
		.seller_inventory .add-category {
			
			width : 100%;
		}
		#add-category {
			display : none;
		}
		#add-btn {
			display : none;
		}
		
		
</style>

<?php echo $header_seller; ?>
<br>
<div class="container">
	<div class="panel panel-custom">
      <div class="panel-cheading">Inventory Upload Panel for Sellers</div>
		<div class="panel-body">
			<div class="row "  style='background-color:#E5E5ED;'>
				<div class="col"  style='background-color:#E5E5ED;' >
					
				<label> <h5><b><span class="glyphicon glyphicon-check"></span> Click on the below button to download Sample file</b></h5></label>
				
				<div class="seller_inventory ">
					<div class = "form-group required">
						<label for "imagefolder" class="control-label">Select Category for Sample DownLoad Sheet</label>
							<select class="form-control download-file-width select" id="category_selected" name="category_selected">
								<option value="">Select Category</option>
									<?php foreach ($category as $subcategory) { ?>	
								<option value="<?php echo $subcategory['category_id']; ?>"><?php echo $subcategory['name']; ?></option>
								<?php } ?>
								<optgroup label="Add new Category">
								<option value="add"><b>Add category</b></option>
							</select>
							<br>
							<div class="row">
								<div class="col-md-12">
									<input name="add-category" id="add-category" placeholder="add new category" class="form-control add-category">
								</div>
								<button type="button" id = "add-btn" class="btn btn-success btn-sm"> Add</button>
							</div>
							</div>
						<form action="index.php?route=seller/seller_upload/download" method="post" id = 'download_form' class = 'form1'>
							<input class="btn btn-danger btn-sm" type = "submit" name="submit" value = "Sample File " style="float: left;" >
								<span class="glyphiconm glyphicon-download-alt"></span>
							</input>
							<input type="hidden" name="category_id_download" id="category_id_download"/>
						</form>
				</div>
				<div id = "height"></div>
				<div>
				</div>
				</div>	
				<div class="col" style='background-color:#eee;;'>
				<h5><b> <span class="glyphicon glyphicon-check"></span> Only xlsx and xls file extension is supported</h5></b></label>
					<div class = "form-group required">
						<label for "imagefolder" class="control-label">Select Category</label>
							<select class="form-control download-file-width" id="category_name" name="submit_category">
								<option value="">Select Category</option>
									<?php foreach ($category as $subcategory) { ?>	
								<option value="<?php echo $subcategory['category_id']; ?>"><?php echo $subcategory['name']; ?></option>
								<?php } ?>

							</select>
						</div>
					<div class="seller_inventory">
						<form enctype="multipart/form-data" action="index.php?route=seller/seller_upload/upload" id = "sheet_form" method="post" >
						<input name = "file"  type="file" id="file" required > 
						<br>
						<input  class="btn btn-success btn-sm" type="submit" value="Upload File ">
						<span class="glyphiconm glyphicon-upload"></span>
						</input>
						<input type="hidden" name="category_id" id="category_id"/>
						</form>
					</div>
				</div>	
				<div class="col" >
					<div class="seller_inventory">
						<label><h5><b>Please go through the video for reference</h5></b></label>
					</div>
				</div>
				</div>	
				</div>
			<div class="panel-footer"></div>
    </div>
</div>


<?php echo $footer_seller; ?>
<script>	
	
	$(document).ready( function (){
    $("#sheet_form").submit( function(submitEvent) {
		//adding valid value in cateogyr checker
		var category = $("#category_name :selected").text();
		if(category == "Select Category") {
			alert("please select category_id");
			document.getElementById("file").value = "";
			submitEvent.preventDefault();
		} else {
			var filename = $("#file").val();
			// Use a regular expression to trim everything before final dot
			var extension = filename.replace(/^.*\./, '');
			// there is no dot anywhere in filename,
			if (extension == filename) {
				extension = '';
			} else {
				// if there is an extension, convert to lower case
				extension = extension.toLowerCase();
			}
			switch (extension) {
				case 'xlsx':
				case 'xls':
				break;
				default:
					alert("only xlsx and xls extensions allowed");
					document.getElementById("file").value = "";
					submitEvent.preventDefault();
			}
		}
  });
  
  $('#category_name').on('change',function() {
	 var category = $("#category_name :selected").text();
	 var category_id = $('#category_name').val();
	 $('#category_id').val(btoa(JSON.stringify(category_id)));
	 var a = $('#category_id').val();
  });
  
  $('#category_selected').on('click',function() {
	  
	 var category = $("#category_selected :selected").text();
	 var category_id = $('#category_selected').val();
	 
	 if(category_id == "add") {
		 $('#add-category').show();
		 $('#add-btn').show();
		 
	 } else {
		$('#category_id_download').val(btoa(JSON.stringify(category_id)));
		var a = $('#category_id_download').val();
	 }
  });
  
  var i = 0;
  $('#add-btn').on('click',function() {
	  
	  $('#category_selected').append('<option value="'+  'category_' + i + '">' + $('#add-category').val() + '(under review!)</option>');
	  var val = 'category_' + i;
	  $("option[value=" + val + "]").css('color', 'red');

	  jQuery.ajax({
		  url: "index.php?route=seller/seller_upload/getNewCategoryAndSendMail",
		  type: 'POST',
		  datatype : 'json',		
		  data: {category_name: $('#add-category').val()},
		  success: function () {
			alert(" We have received your request for new category. We shall update you shortly.");
		 }
		});
	  i++;
	  $('#add-btn').hide();
	  $('#add-category').hide();
	  
  });
  
  $('#download_form').submit(function(submitEvent) {
	   var category_id = $('#category_selected').val();
	   if(category_id == "") {
		   alert("please select category to download sample sheet");
		   submitEvent.preventDefault();
	   }
  });
  
});
</script>
</html>
