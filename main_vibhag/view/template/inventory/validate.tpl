
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
		.btn {
			margin-right : 15px;
			display: inline-block;
			margin-bottom: 0;
			font-weight: normal;
			text-align: center;
			vertical-align: middle;
			touch-action: manipulation;
			cursor: pointer;
			background-image: none;
			border: 1px solid transparent;
			white-space: nowrap;
			padding: 8px 13px;
			font-size: 12px;
			line-height: 1.42857143;
			border-radius: 3px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
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
		
		.body {
			#EAE3E3
		}
		
		.panel-cheading {
			padding: 8px 12px;
			border-bottom: 1px solid transparent;
			border-top-left-radius: 3px;
			border-top-right-radius: 3px;
			text-align : center;
		}
		.form1 {
			
			left-margin : 0px;
		}
		#height {
			height : 2vh;
		}
		
		.row1 {
			margin-left: 6px; !important
			margin-right: 5px; !important
			display : flex;
		}
		#wrap {
			width:100px; 
			word-wrap:break-word;
		}
		.bt{
			margin-right: 10px; !important
			margin-left: 10px; !important
			margin : 10px; !important
		}
		.greenBg {
			color: green;
		}
		.redBg {
			color : red;
		}
		.warning {
			background-color : yellow;
		}
		.table > thead > tr > td.warning, .table > tbody > tr > td.warning, .table > tfoot > tr > td.warning, .table > thead > tr > th.warning, .table > tbody > tr > th.warning, .table > tfoot > tr > th.warning, .table > thead > tr.warning > td, .table > tbody > tr.warning > td, .table > tfoot > tr.warning > td, .table > thead > tr.warning > th, .table > tbody > tr.warning > th, .table > tfoot > tr.warning > th {
			color : red;
		}
		.image {
			display : none;
		}
		.image_pick {
			float:right;
		}
		.height_b {
			height : 2vh;
		}
		.cursor {
			cursor: pointer;	
		}
		
		.hide-product-id {
			display : none;
		}
		
</style>

<?php echo $header; ?><?php echo $column_left; ?>

<?php //secho "<pre>"; print_r($csv_data); ?>
<div id = "content">


<div class="page-header">
	
	<div class="container-fluid">
		<div class="panel panel-custom">
		  <div class="panel-cheading">Validate Inventory Panel</div>
			<div class="panel-body">
				<div class="row1  ">
					<div style='background-color:#E5E5ED;' >
					
					<div class ="col-sm-4">
						<div class = "form-group required">
							<label for "imagefolder" class="control-label">Select Seller Id</label>
								<select class="form-control" id="seller_name" name="submit_seller">
									<option value="">Select Seller Id</option>
										<?php foreach ($filter_seller_id as $seller) { ?>
										<option value="<?php echo $seller['seller_id'] ?>"><?php echo $seller['nickname'] ?></option>
										<?php } ?>
								</select>							
						</div>
					</div>
						
					<div class = "col-sm-4">
						<div class = "form-group required">
							<label for "imagefolder" class="control-label">Select Category</label>
								<select class="form-control" id="category_name" name="submit_category">
									<option value="">Select Category</option>

										<?php foreach ($filter_category_id as $category) { ?>
										<option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
									<?php } ?>
								</select>
						</div>
					</div>	
					
					<div class = "col-sm-4">
						<div class = "form-group required">
							<label for "imagefolder" class="control-label">Select Status</label>
								<select class="form-control" id="status_name" name="status">
									<option value="0">Pending for Approval</option>
									<option value="1">Rejected</option>
									<option value="3">on Hold</option>	

								</select>
							</div>
						</div>		
					</div>
					
					<div class = "col-sm-6">
						<div class = "form-group required">
							<div>
								<button class="btn btn-success btn-sm" type = "submit" id = "submit" name="approved" value = "approved" style="float: left;" ><span class="glyphicon glyphicon-button"></span> Submit Data</button>
							</div>
						</div>
					</div>		
				
					
														
				</div>
			</div>
			
			<div class="image_pick" >
				<!-- commenting out pick images out! can be used later stage!, commented out checkboxes as well in sku_code-->
				<!--<button class="btn btn-primary btn-sm"  style="display:none;" id = "pick_image" value = "Pick Images" style="float: left;" ><span class="glyphicon glyphicon-button"></span> Pick Images</button>-->
				<br>
			</div>
			<div class = "col-sm-12" id = "display2">
				<div class='table-responsive'>	
					
				</div>
				
			</div>	
			
			
		
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
     </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="product_name"></h4>
        </div>
        <div class="modal-body">
			<br>
			<div id = "image_name">
			
			</div>
			<div id = "image">
				<img src="" class="imagepreview" >
			</div>
        <div class="w3-center">
			<div class="w3-section">
				<button class="w3-button btn-sm btn-warning" onclick="plusDivs(-1)">❮ Prev</button>
				<button class="w3-button btn-sm btn-primary" onclick="plusDivs(1)">Next ❯</button>
			</div>
		</div>

        </div>
      
      </div>
      
    </div>
  </div>
  
<script>	
	var inventory_data = [];
	var counter;
	//$(document).ready(function(){
		
	//});
	
	var count = 0;
	var len = [];
	var image_count = [];
	var image_count_pick_images = [];
	var count_pick_image = 0;
	var permissible_tax_class = []; //storing aray having tax_classids and tax_ratess
	var keys = []; //storing tax_class_ids
	var input_type; //storing input_type
	var newarr = [];
	var selected_status;
	var trigger = <?php echo $trigger; ?>;
	if(trigger == 1) {
		$(document).ready(function(){

			$('#submit').trigger('click');
		});
	}
	

	$('#submit').on('click',function(){
		var url = 'index.php?route=inventory/validate&token=<?php echo $token; ?>';
		
		//inventory_data = <?php print json_decode($inventory_data) ? $inventory_data :'' ;?>;
		//console.log(inventory_data);
		
		var filter_seller = $('#seller_name').val();

        if (filter_seller) {
            url += '&filter_seller=' + encodeURIComponent(filter_seller);
        }

        var filter_category =  $('#category_name').val();

        if (filter_category) {
            url += '&filter_category=' + encodeURIComponent(filter_category);
        }
        
        var filter_status = $('#status_name').val();
        
        if (filter_status) {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }
        
		//alert(url);

		count++;
		if(count >= 2) {
		$("#display2").empty();
		}
		//if($('#category_name').val() == "" ||  $('#seller_name').val() == "" || $('#status_name').val() == "") {
		//	alert("please select some value");
		//} else {
		
			$('#pick_image').show();
			var data = $('#submit_image').val();
			var id_seller =$('#seller_name').val();
			var seller_id = $("#seller_name :selected").text();
			var seller_code = seller_id.split('-')[0];
			var category = $('#category_name').val();
			var category_name = $("#category_name :selected").text();
			category_name = category_name.replace(/[>]+/g, "");
			var status = $('#status_name').val();
			
			selected_status = status;
			$.ajax({
				url:  'index.php?route=inventory/validate/getForm&token=<?php echo $this->session->data['token']; ?>' + url,
				type: 'GET',
				datatype : 'json',		
				success: function (data) {	
					var inventory_data = JSON.parse(data);
					var trHTML = "";
					if(inventory_data.final_data.length == 0) {
						trHTML += "<br>";
						trHTML += "<table  bgcolor='#FF0000' class='table table-bordered'>";
						trHTML += '<tr><td width="100%">NO ENTRIES TO SHOW!</td></tr></table>';
					} else {
					/*	var tax_import_data = inventory_data.tax_data;
						var tax_rate_data = [];
						var tax_data = [];
						var tax_data_with_input = [];
						
						if ( tax_import_data[1] != undefined ) {
							for(var i in inventory_data.tax_data[1]) {
								tax_rate_data.push(inventory_data.tax_data[1][i]);
							}
							
							if( inventory_data.tax_data[1] != undefined || inventory_data.tax_data[1] != null ) {
								permissible_tax_class = inventory_data.tax_data[1];
								for(var key in permissible_tax_class) {
									keys[key] = Object.keys(permissible_tax_class[key]);
								}
							}
							
						} 
						
						for(var i in tax_import_data) {
							tax_data.push(tax_import_data[i]);
						}
						*/
						
						var data = inventory_data.final_data;	
						var final_data = [];
						for(var i in data) {
							final_data.push(data[i]);
						}	
						
						for(var i =0; i < final_data.length; i++){
							
							var import_data = final_data[i][0];
							
							
							var array_not_filters = ['AvailableSets','Color Description','Color Set Sizes','Weight of a Piece (in gm)','Transfer Price','Tax','Set Type','SKU Code','Pieces in Size Set','Pieces in Free Size Set','Free Size quantity','Color Set quantity','Any additional Comments','Sizes in Size Set', 'id', 'Quantity', 'Pieces in Set'];
							
							trHTML += "<table  bgcolor='#FF0000' id = 'table_" + i+ "' class='table table-bordered'><th><a href='#'>Import CSV ID</a></th><th class='text-left'><a href='#'>Category Name</a></th><th class='text-left'><a href='#'>File Link</a></th><th class='text-left'><a href='#'>Dated Added</a></th><th class='text-left'><a href='#'>Change Status of Inventory</a></th><th class='text-left'><a href='#'>Comments</a></th>";
							trHTML += '<tr id = "row_' + i + '"><td width="5%"><a class = "cursor" id = "a_'+i+'"' +'onclick="javascript:myfunction(this);">' +
										import_data.import_csv_id +'</a></td><td width="10%">' + 
										import_data.category_name +'</td><td nowrap style="white-space: nowrap;  overflow: hidden; overflow-x:auto" width="10%">' + 
										'<a href="index.php?route=inventory/validate/downloadFile&token=<?php echo $this->session->data['token']; ?>&file_path='+import_data.file_path+'">Click here to download seller sheet</a>' +'</td><td width="5%">' + 
										import_data.date_added +'</td>'
										//'<button class = "bt btn btn-danger btn-sm" id = "get_image_'+i+'"' +'>Get Image</button></td>' 
										+ '<td id = "button_' + i + '" width = "10%"><button class="bt btn btn-warning btn-sm" id = "hold_'+i+'"' +'>Put on Hold</button></td>' + 
										'<td id = "td_with_comment_' +i +'" width="5%">' + '<div  id = "Comment_' +i +'"style="display:none">' +
										'<textarea id = "comment_' + i +'"placehoder = "please enter the reason" name="comment" required></textarea>' + '<button id = "comment' + i +'" class = "btn btn-success btn-sm">Add</button>' +
										'</div>' +
										'</td></tr>';
							var count = i+1;
							trHTML += '<table class="table table-bordered"  id="displaytable_' +i+'"' +'style="display:none"> ' +
										'<tr><th width="8%">' + 
										' SKU Code' +'</th><th class = "hide-product-id">Product Id</th><th width="25%">' + 
										'Name' +'</th><th width="15%">' + 
										'Set Description'+'</th><th width="10%">' + 
										'Description' +'</th><th width="5%">' + 
										'Price' +'</th><th width="5%">' +
										'Weight' + '</th><th width="5%">' +
										'Seller VAT (%)' + '</th><th width="8%">' +
										'Tax Class' + '</th><th width="5%">' +
										'Sale VAT (%)' + '</th><th width="8%">' +
										'Filters' + '</th><th width="15%">' +
										'Image Status' +'</th><th width="15%">SKU Action</th></tr>';
							
							for(var j =0 ;j < final_data[i].length; j++) {
								
								var max_weight = import_data['max_weight'];
								var min_weight = import_data['min_weight'];
								
								var data2 = final_data[i][j];
								var new_data = [];
								var import_data2 = [];
								var import_data = [];
								import_data = JSON.parse(final_data[i][j]['import_data']);
								
							//	var weight_of_piece = import_data['Weight of a Piece (in gm)']/100;
								
								/*if(weight_of_piece > max_weight || weight_of_piece < min_weight) {
									$('#display_' + i + 'weight_' + j).css('color' , 'green');
								} else {
									$('#display_' + i + 'weight_' + j).css('color' , 'red');
								} */
								
								var import_csv_id = final_data[i][j].import_csv_id;
								
								var json_parse = jQuery.parseJSON(data2.import_data);
								$.each(json_parse, function (k,v) {
									new_data[k] = v;
									
								});
								
								
								import_data2 = new_data;
								var filters_array = [];
								var filter_values = [];
								
								//extracting out the filters
								for (var key in new_data) {
									
									if(array_not_filters.indexOf(key) == -1 && new_data[key] != "") {
										
										if(final_data[i][0]['naming_filters'].indexOf(key) != -1) {
											filter_values.push(new_data[key]);
										}
										filters_array.push(new_data[key]);
									}
								}
								
							
								var set_description = '';
								
								//set description for set type
								if(new_data['Set Type'] == 1) {
									//color set
									set_description = "1 Set = Total " + new_data['Color Description'].split(",").length + " pieces; 1 each of color " + new_data['Color Description'];
									set_description += "; size-options : " + new_data['Color Set Sizes'];

								} else if(new_data['Set Type'] == 2) {
									//free size set
									
									set_description = "1 Set = Total " + new_data['Pieces in Free Size Set'] + " pieces of free size; Available sets : " + new_data['Free Size quantity'];
									
								} else if(new_data['Set Type'] == 0) {
									//size set
									var pieces = new_data['Pieces in Size Set'].split(",");
									var sizes = new_data['Sizes in Size Set'].split(",");
									for(var x = 0; x < pieces.length; x++) {
										if(pieces[x] == 0) {
											delete sizes[x];
										}
									} 
									pieces = pieces.length;
									var quantity  = sizes.length;
									set_description = "1 Set = Total " + pieces + " pieces; 1 each of sizes " + sizes.join();
									
								} else if(new_data['Set Type'] == 3) {
									//not specific set
									set_description = "1 Set = Total " + new_data['Pieces in Set'] + " pieces of not specific set; Available sets : " + new_data['Quantity'];

								}
							
								
								
								trHTML += 	 '<tr id = tr_' + i + '_' + j +'><td width="10%"  id = "' +
										i + '_' + j + '"name="' + import_data["SKU Code"] + '">' + 
										import_data["SKU Code"] +'</td><td class = "hide-product-id">'+ final_data[i][j].import_product_id +'</td><td width="15%">' + 
										(filter_values.join(" ")).concat(" " + final_data[i][j]['category_name']) +'</td><td width="10%">' + 
										set_description +'</td><td width="10%">' + 
										new_data["Any additional Comments"] +'</td><td>' + 
										new_data["Transfer Price"] +'</td><td id = "display_' +
										i + 'weight_' + j + '" >' + 
										(new_data["Weight of a Piece (in gm)"])/1000 +'kg </td>' + 
										'<td >' + import_data['Tax'] + '</td>' + '<td >'+ 
										data2['tax_class_id'] +'</td>'+'<td width="10%" >' + 
										data2['seller_tax'] + '</td>'+
										'<td width = 10%>' +filters_array.join() +
										'</td><td width="10%" id = "display_' + i + 'image_' + j + '" >' + 
										'NA' +'</td><td width="10%" id = "sku_action_' + i + '_' + j + '" >' + 
										'<button class = "bt btn btn-danger btn-sm" id = "sku_reject_'+i+'_'+ j +'"' +
										'>Reject</button> <div class = "height_b"></div>' +
										'<button class ="btn btn-success btn-sm"  id = "sku_approve_'+i+'_' + j+'"' +
										'>Approve</button><div class = "height_b"></div>' +
										'<button class ="btn btn-primary btn-sm"  id = "sku_image_'+i+'_' + j+'"' +
										'>Pick Image</button></td><td class = "hide-product-id" id = "input_' + i + '_' + j + '" >' + input_type + '</td></tr>';
								
								/*} else if(input_type == 0) {

									trHTML += 	 '<tr id = tr_' + i + '_' + j +'><td width="10%" id = "' +
											i + '_' + j + '"name="' + import_data["SKU Code"] + '">' + 
											import_data["SKU Code"] +'</td><td class = "hide-product-id">'+final_data[i][j].import_product_id+'</td><td width="15%">' + 
											(filter_values.join(" ")).concat(" " + final_data[i][j].category_name) +'</td><td width="10%">' + 
											set_description +'</td><td width="10%">' + 
											new_data["Any additional Comments"] +'</td><td>' + 
											new_data["Transfer Price"] +'</td><td id = "display_' +
											i + 'weight_' + j + '" >' + 
											(new_data["Weight of a Piece (in gm)"])/1000 +'kg </td>' + 
											'<td >' + tax_data[0][import_csv_id][j]['Tax'] + '</td>' + '<td  id="td_sel_'+
											i+ '_' + j + '" > <select  id="select_'+
											i+ '_' + j + '" onclick="javascript:getDropDownValues(this);">'+'<option value="' + tax_data[0][import_csv_id][j]['tax_class_id'] + '">' + 
											tax_data[0][import_csv_id][j]['tax_rate'] + '</option>' +
											'</select></td>'+'<td width="10%" >' + 
											tax_data[0][import_csv_id][j]['sale_tax'] + '</td>'+
											'<td width = 10%>' +filters_array.join() +
											'</td><td width="10%" id = "display_' + i + 'image_' + j + '" >' + 
											'NA' +'</td><td width="10%" id = "sku_action_' + i + '_' + j + '" >' + 
											'<button class = "bt btn btn-danger btn-sm" id = "sku_reject_'+i+'_'+ j +'"' +
											'>Reject</button> <div class = "height_b"></div>' +
											'<button class ="btn btn-success btn-sm"  id = "sku_approve_'+i+'_' + j+'"' +
											'>Approve</button><div class = "height_b"></div>' +
											'<button class ="btn btn-primary btn-sm"  id = "sku_image_'+i+'_' + j+'"' +
											'>Pick Image</button></td><td class = "hide-product-id" id = "input_' + i + '_' + j + '" >' + input_type + '</td></tr>';
								}*/
						}
						trHTML+='</table>';
						len.push((final_data[i]).length);
						}
						trHTML+="</table>";
						
					}
				
					$("#display2").append($(trHTML));   
                   },
		});
		
	
	});
	
	var count_function = 0;
	var count_drop_down = 0;
	var ids_array = [];

	function getDropDownValues(elem) {
		var id = $(elem).attr("id");
		
		var selected_val = $("#" + id).val();
		var arr =[];
		var import_csv_id = $('#a_' + id.split("_")[1]).html();
		//disallow further values to append in select box if values have been appended once
		if(ids_array.includes(id)) {
			
			return;
		} 

		//freeze the data since seller tax = 0;
		if(selected_val == 0) {
			alert("you cannot change tax class id as input seller tax is 0");
			return;	
		}
		
		//pushing ids in array to check which id is present in array
		ids_array.push(id);
		
		var select = document.getElementById(id);
		
		//converting object to array
		for( var i in permissible_tax_class[import_csv_id] ) {
			if (permissible_tax_class[import_csv_id].hasOwnProperty(i)){
			   arr.push(permissible_tax_class[import_csv_id][i]);
			}
		}		
		//appending values in select element
		for (var i = 0; i < arr.length; i++) {
			var opt = arr[i];
			var el = document.createElement("option");
			el.textContent = opt;
			el.value = parseInt(keys[import_csv_id][i]);
			select.appendChild(el);
		}
	}
	
	var approve_count = 0;
	var approve_a = 0;
	var image_click = 0;


	function myfunction(elem) {
		
		var id = $(elem).attr("id");
		var value_import_id = $('#'+id).text();
		var num = id.split('_');
		var id_seller =$('#seller_name').val();
		var seller_id = $("#seller_name :selected").text();
		var seller_code = seller_id.split('-')[0];
		var category = $('#category_name').val();
		var category_name = $("#category_name :selected").text();
		category_name = category_name.replace(/[>]+/g, "");
		var inserted_ids = [];
		var rejected_ids = [];
		var image_count = [];
		var image_pick_count = [];
	
		var error = 0;
		var checked_box = "";
		var status = 0;
		//array to store numbers/skus to remove data during addproduct where images are not available
		var removed_sku_from_product = [];
		var sku_name = [];
		var sku_id = [];
		var removed_sku_send = [];

		for(var j = 0; j < len.length; j++) {
			sku_name.push([]);
			sku_id.push([]);
			rejected_ids.push([]);
			inserted_ids.push([]);
			removed_sku_send.push([]);
			removed_sku_from_product.push([]);
			image_pick_count.push([]);
		}
	
		for(var j = 0; j < len[num[1]]; j++) {
			
			var check_box = $('#' + num[1] + '_' + j).prop('checked');
			if(check_box == true || check_box == false) {
				var name = $('#'+num[1]+ '_' + j).attr('name');
				var id = $('#'+num[1]+ '_' + j).attr('id');
				sku_id[num[1]].push(id.split('_')[1]);
				sku_name[num[1]].push(name);
			}

		}
		var count = 0;
		var count2 = 0;
		
		$('#pick_image').unbind().on('click',function() {

			//getting image using checkboxes
			var selected = [];
			var selected_ids = [];
			for(var j = 0; j < len[num[1]]; j++) {
				var check_box = $('#' + num[1] + '_' + j).prop('checked');
				if(check_box == true) {
					var name = $('#'+num[1]+ '_' + j).attr('name');
					var id = $('#'+num[1]+ '_' + j).attr('id');
					selected_ids.push(id);
					selected.push(name);
					image_pick_count[num[1]][parseInt(id.split('_')[1])] = true;
				}
			}
		
			$.ajax({
				url:  'index.php?route=inventory/validate/pickImage&token=<?php echo $this->session->data['token']; ?>',
				type: 'GET',
				datatype : 'json',		
				data: {id_seller :id_seller, selected : selected},
				success: function (data) {	
					var images = [];
					var data = JSON.parse(data);
					images = data.images;
					console.log(images);
					for(var i = 0; i < selected_ids.length; i++) {
						var row_num = selected_ids[i].split("_")[1];
						if(images[selected[i]] == undefined) {
							$('#display_' + num[1] + 'image_' + i ).html("Image Not Available");
							$('#tr_'+num[1]+'_'+row_num).css('background-color', 'rgb(189, 130, 130)');
							$('#tr_'+num[1]+'_'+row_num).css('color', 'black');
							removed_sku_from_product[num[1]].push(parseInt(row_num));

						} else {
							$('#display_' + num[1] + 'image_' + row_num ).html("<a id = 'image_0_" + num[1]+row_num+"' onclick='javascript:show_image(this,\"" + images[selected[i]][0] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + images[selected[i]][0][0] + "'></a>");
						}
					}
				},
			});	

			
		});
		
		
		$('#get_image_' + num[1]).unbind().on('click',function() {
			image_count[num[1]] = 1;
			var sku_arr = [];
			sku_arr = sku_name[num[1]];

			$.ajax({
				url:  'index.php?route=inventory/validate/checkImage&token=<?php echo $this->session->data['token']; ?>',
				type: 'GET',
				datatype : 'json',		
				data: {value_import_id: value_import_id, id_seller :id_seller, sku_arr : sku_arr},
				success: function (data) {	
					var data = JSON.parse(data);
					var warning_flag = 0;
					data = data.images;
					
					for(var i = 0; i < sku_arr.length; i++) {
						
						//checking for warning in weight
						if($('#display_' + num[1] + 'weight_' + i ).text() < .2 || $('#display_' + num[1] + 'weight_' + i ).text() > 2) {
							$('#display_' + num[1] + 'weight_' + i ).addClass('warning');
						}
							
						if(data[sku_arr[i]] == undefined) {
							$('#display_' + num[1] + 'image_' + i ).html("Image Not Available");

							$('#tr_'+num[1]+'_'+i).css('background-color', 'rgb(189, 130, 130)');
							$('#tr_'+num[1]+'_'+i).css('color', 'black');
							removed_sku_from_product[num[1]].push(i);
							
						} else {
							
							$('#display_' + num[1] + 'image_' + i ).html("<a id = 'image_0_" + num[1]+i+"' onclick='javascript:show_image(this,\"" + data[sku_arr[i]][0] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + data[sku_arr[i]][0][0] + "'></a>");
							
						}	
					}
				}
			});

		});
			
		for(var j = 0; j < len[num[1]]; j++) {
			var sku_image_clicked = 0;
			$('#sku_image_' + num[1] + '_' + j).unbind().on('click', function() {
				sku_image_clicked++;
				var sku_code = [];
				var id = $(this).prop("id");
				var index = id.split('_')[3];
				var sku_present = sku_id[num[1]].slice(0, sku_id[num[1]].length); //making a copy of the sku_ids
				var id = $(this).prop('id');
				var index_num = id.split('_')[3];
				var number = parseInt(index_num)+1;
				sku_code[0] = document.getElementById("displaytable_"+num[1]).rows[number].cells[0].innerHTML;
				import_product_id = document.getElementById("displaytable_"+num[1]).rows[number].cells[1].innerHTML;

				
				$.ajax({
					url:  'index.php?route=inventory/validate/pickImage&token=<?php echo $this->session->data['token']; ?>',
					type: 'GET',
					datatype : 'json',		
					data: {import_product_id :import_product_id, selected : sku_code},
					success: function (data) {	
						var images = [];
						var data = JSON.parse(data);
						images = data.images;
						
						if(images.length == 0 || images == undefined) {
							
							$('#display_' + num[1] + 'image_' + index_num ).html("Image Not Available");
							$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
							$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							
						} else {
							
							$('#display_' + num[1] + 'image_' + index_num ).html("<a id = 'image_0_" + num[1]+index_num+"' onclick='javascript:show_image(this,\"" + images[sku_code[0]][0] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + images[sku_code[0]][0][0] + "'></a>");
						}
					},
				});	

			});	
			
			$('#sku_reject_' + num[1] + '_' + j).unbind().on('click', function() {
				var id = $(this).prop("id");
				var index = id.split('_')[3];
				
				if(parseInt(sku_image_clicked) >= 1) {	

					var sku_present = sku_id[num[1]].slice(0, sku_id[num[1]].length); //making a copy of the sku_ids
					var id = $(this).prop('id');

					var index_num = id.split('_')[3];
					var number = parseInt(index_num)+1;
					var product_id = document.getElementById("displaytable_"+num[1]).rows[number].cells[1].innerHTML;
					rejected_ids[num[1]].push(parseInt(index_num));
					
					$.ajax({
						url:  'index.php?route=inventory/validate/rejectProduct&token=<?php echo $this->session->data['token']; ?>',
						type : 'GET',
						datatype : 'json',
						data : {product_id:product_id},
						success: function (data) {	
							var data = JSON.parse(data);
							if(data.flag == 1) {
								alert(data.msg);
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Rejected</p>');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							} 
						},
					});
				} else {
					alert("you cannot reject a product without selecting image for it");
				}
			});	
			
			$('#sku_approve_' + num[1] + '_' + j).unbind().on('click', function() {
				var id = $(this).prop("id");
				var index = id.split('_')[3];
				if(parseInt(sku_image_clicked) >= 1) {	
					var removed_sku = [];
					var checking_tax_flag = 0;
					var sku_present = sku_id[num[1]].slice(0, sku_id[num[1]].length); //making a copy of the sku_ids
					var id = $(this).prop('id');
					var index_num = id.split('_')[3];
					var row_id = '#tr'+'_'+num[1]+'_'+index_num;
					var number = parseInt(index_num)+1;
					var product_id = document.getElementById("displaytable_"+num[1]).rows[number].cells[1].innerHTML;
					var checking_tax_rate = document.getElementById("displaytable_"+num[1]).rows[number].cells[8].innerHTML;
					var input_type = document.getElementById("displaytable_"+num[1]).rows[number].cells[13].innerHTML;
					var image_status = document.getElementById("displaytable_"+num[1]).rows[number].cells[11].innerHTML;


					var tax_rate = $(row_id).find(":selected").text(); // gets tax_rate values
					var tax_class_id = $(row_id).find(":selected").val(); // gets tax_class_id values
					inserted_ids[num[1]].push(parseInt(index_num));
					var index = sku_present.indexOf(index_num);
					if (index > -1) {
						sku_present.splice(index, 1);
					}	
					
					if(checking_tax_rate == "tax class id not set! Please set the tax rate id") {
						checking_tax_flag = 1;
					}
				
					var index = "image_0_".concat(num[1],index_num);	
					console.log(newarr[index]);

					if(newarr[index] == undefined || image_status == "Image Not Available" || newarr[index].length == 0) {
						alert("Please check images!");
						return;
					}
				
					if(image_status == "Image Not Available") {
						alert("Image not available");
					}
					if(tax_class_id == undefined) {
						tax_class_id = document.getElementById("displaytable_"+num[1]).rows[number].cells[8].innerHTML;
						
					}
					
					$('#sku_approve_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Loading</p>');
					removed_sku = sku_present.slice(0, sku_id[num[1]].length);
					$.ajax({
						url:  'index.php?route=inventory/validate/addProduct&token=<?php echo $this->session->data['token']; ?>',
						type : 'GET',
						datatype : 'json',
						data : {product_id:product_id, checking_tax_flag : checking_tax_flag, images_arr : newarr[index]},
						success: function (data) {	
							var data = JSON.parse(data);
							if(data.flag == 1) {
								alert(data.message);
								$('#button_'+num[1]).html('<p style="text-align: center;align:center">Seller Not Active</p>');
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Seller not active</p>');
								$('td', '#displaytable_'+num[1]).css({ 'background-color' : 'rgb(189, 130, 130)' });
								$('td', '#displaytable_'+num[1]).css({ 'color' : 'black' });
								$('table tr td:nth-child(12)').html('Seller Not Active');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
								$('#row_' + num[1]).children('td, th').css('background-color', 'rgb(189, 130, 130)');
								$('#row_' + num[1]).children('td, th').css('color', 'black')
							} else if(data.flag == 0) {
								alert(data.message);
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Approved</p>');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', '#79a079');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							} else if(data.flag == 2) {
								alert(data.message);
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Product Rejected! Duplicate SKU</p>');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							} else if(data.flag == 3) {
								alert(data.message);
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Product Rejected! Tax class ID not valid!</p>');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							} else if(data.flag == 4) {
								alert(data.message);
								$('#sku_action_' + num[1] + '_' + index_num).html('<p style="text-align: center;align:center">Product Rejected! No Images Available!</p>');
								$('#tr_'+num[1]+'_'+index_num).css('background-color', 'rgb(189, 130, 130)');
								$('#tr_'+num[1]+'_'+index_num).css('color', 'black');
							}
						},
					});
				} else {
					alert("you cannot approve a product without selecting image for it");
					}
				});
		}
	
		$('#reject_' + num[1]).unbind().on('click',function() {
			
			if(image_count[num[1]] == 1) {
				status = 1;
				$('#Comment_' +num[1]).show();
			} else {
				alert("you cannot reject product without selecting all the images");
			}
			
		});
			
		$('#hold_' + num[1]).unbind().on('click',function() {
			status = 3;
			$('#Comment_' +num[1]).show();
		});
		
		$('#approve_' + num[1]).unbind().on('click',function() {
			
			if(image_count[num[1]] == 1) {	
					$('#approve_' + num[1]).html('<p style="text-align: center;align:center">Loading</p>');
					var removed_sku_send_final = [];
					var clone_sku_id = [];
					var tax_class_id_arr = [];
					var clone_sku_id = (sku_id[num[1]]).slice(0);

					for(var i = 0; i < inserted_ids[num[1]].length; i++) {
						var index = sku_id[num[1]].indexOf((inserted_ids[num[1]][i]).toString());
						if (index > -1) {
							sku_id[num[1]].splice(index, 1);
						}
					}
					
					for(var i = 0; i < rejected_ids[num[1]].length; i++) {
						var index = sku_id[num[1]].indexOf((rejected_ids[num[1]][i]).toString());
						if (index > -1) {
							sku_id[num[1]].splice(index, 1);
						}
					}
					
					for(var i = 0; i < (inserted_ids[num[1]]).length; i++) {
						var index = removed_sku_from_product[num[1]].indexOf(inserted_ids[num[1]][i]);
						if (index > -1) {
							removed_sku_from_product[num[1]].splice(index, 1);
						}
					}
					
					for(var i = 0; i < (removed_sku_from_product[num[1]]).length; i++) {
						var index = sku_id[num[1]].indexOf((removed_sku_from_product[num[1]][i]).toString());
						if (index > -1) {
							sku_id[num[1]].splice(index, 1);
						}
					}
				
					for(var i = 0; i < sku_id[num[1]].length; i++) {
						$('#sku_action_' + num[1] + '_' + parseInt(sku_id[num[1]][i])).html('<p style="text-align: center;align:center">Approved</p>');
						$('#tr_'+num[1]+'_'+ parseInt(sku_id[num[1]][i])).css('background-color', '#79a079');
						$('#tr_'+num[1]+'_'+ parseInt(sku_id[num[1]][i])).css('color', 'black');
					}
					
					for(var i = 0; i < removed_sku_from_product[num[1]].length; i++) {
						$('#sku_action_' + num[1] + '_' + parseInt(removed_sku_from_product[num[1]][i])).html('<p style="text-align: center;align:center">Rejected</p>');
						$('#tr_'+num[1]+'_'+ parseInt(removed_sku_from_product[num[1]][i])).css('background-color', 'rgb(189, 130, 130)');
						$('#tr_'+num[1]+'_'+ parseInt(removed_sku_from_product[num[1]][i])).css('color', 'black');
					}
					
					removed_sku_send[num[1]] = removed_sku_from_product[num[1]].concat(inserted_ids[num[1]]);
					removed_sku_send[num[1]] = removed_sku_send[num[1]].concat(rejected_ids[num[1]]);
					removed_sku_send_final = removed_sku_send[num[1]];
				/*
					for(var i = 0; i<clone_sku_id.length; i++) {
						var row_id = '#tr'+'_'+num[1]+'_'+clone_sku_id[i];
						var tax_rate = $(row_id).find(":selected").text(); // gets tax_rate values
						var tax_class_id = $(row_id).find(":selected").val(); // gets tax_class_id values
						tax_class_id_arr.push(tax_class_id);
						
					}
					
					*/
			
					$.ajax({
						url:  'index.php?route=inventory/validate/addProduct&token=<?php echo $this->session->data['token']; ?>',
						type : 'GET',
						datatype : 'json',
						data : {value_import_id:value_import_id, id_seller:id_seller,removed_sku_send_final : removed_sku_send_final},
						success: function (data) {	
							var data = JSON.parse(data);
							if(data.flag == 1) {
								alert(data.message);
								$('#button_'+num[1]).html('<p style="text-align: center;align:center">Seller Not Active</p>');
								$('#row_' + num[1]).children('td, th').css('background-color', 'rgb(189, 130, 130)');
								$('#row_' + num[1]).children('td, th').css('color', 'black');
								$('td', '#displaytable_'+num[1]).css({ 'background-color' : 'rgb(189, 130, 130)' });
								$('td', '#displaytable_'+num[1]).css({ 'color' : 'black' });
								$('table tr td:nth-child(13)').html('Seller Not Active');
							} else if(data.flag == 0) {
								alert(data.message);
								$('#button_'+num[1]).html('<p style="text-align: center;align:center">Approved</p>');
								$('#row_' + num[1]).children('td, th').css('background-color', '#79a079');
								$('#row_' + num[1]).children('td, th').css('color', 'black');
							}
						},
					});
					$('#displaytable_' + num[1]).hide();	
				} else {
					alert("you cannot add product without selecting all the images");
					
				}
			

			
			});
			approve_count--;

		$('#comment'+num[1]).unbind().on('click', function() {
			var comment = $('#comment_' + num[1]).val();
			$('#Comment_' +num[1]).hide();
			$('#td_with_comment_' +num[1]).html(comment);

			$.ajax({
				url:  'index.php?route=inventory/validate/addComment&token=<?php echo $this->session->data['token']; ?>',
				type: 'GET',
				datatype : 'json',		
				data: {value_import_id: value_import_id, comment : comment, status : status, selected_status : selected_status},
				success: function (data) {	
					if(status == 1) {
						alert("The status of the ID has been changed to Rejected");
					} else if(status == 3) {
						alert("The status of the ID has been changed to on Hold");
					}
				}
			});
			
			$('#displaytable_' + num[1]).hide();
			
			if(status == 3) {
				$("#button_" + num[1]).html('<p style="text-align: center;align:center">On Hold</p>');
				$('#row_' + num[1]).children('td, th').css('background-color', 'rgb(222, 150, 95');
				$('#row_' + num[1]).children('td, th').css('color', 'black');
			
				
				$('#displaytable_' + num[1]).find('tr').each(function (i, el) {
					$tds = $(this).find('td');
					action = $tds.eq(12).text();
					if(action.match('Rejected') || action.match('Approved')) {

					} else {
						$tds.eq(12).html('Inventory set on hold');
						$tds.css({ 'background-color' : 'rgb(222, 150, 95' });
						$tds.css({ 'color' : 'black' });
					}
				});
			
			} else if(status == 1) {
				$("#button_" + num[1]).html('<p style="text-align: center;align:center">Rejected</p>');
				$('#row_' + num[1]).children('td, th').css('background-color', 'rgb(189, 130, 130)');
				$('#row_' + num[1]).children('td, th').css('color', 'black');
			}
		});	


			
		if(count_function%2 == 0) {
			$('#displaytable_' + num[1]).show();
			count_function++;
		} else {
			$('#displaytable_' + num[1]).hide();
			count_function++;
		}
	}
	var slideIndex;
	var delete_id ;
	var marked_array = [];

	function show_image(elem,data) {
		newarr = [];
		
		var id = $(elem).attr("id");
		if(marked_array.indexOf(id) == -1) {
			
			marked_array.push(id);			
			image_click++;
			$('#image').empty();
			arr = data.split(",");
			delete_id = id.split("_")[2];
				
			newarr[id] = arr.slice();
			
			

			$('#image').append('<button id = "delete_image' + delete_id + '" class="w3-button btn-sm btn-danger" ><i class="fa fa-folder">Reject Image</i></button>');
			for(var i = 0; i < arr.length; i++) {
				$('#image').append('<img id = "image_' + delete_id + '_' + i +'"class="a" src="'+ arr[i] + '" style = "width:100%">');
			//	$('#image_name').empty();

			}
			slideIndex = 1;
			showDivs(slideIndex);	
			$('#myModal').show();
			
		} else {
			
			$('#image').empty();
			delete_id = id.split("_")[2];
			arr = data.split(",");
			delete_id = id.split("_")[2];
			newarr[id] = arr.slice();
			//showing imags that have been approved/rejected by the user. local data
			$('#image').append('<button id = "delete_image' + delete_id + '" class="w3-button btn-sm btn-danger" >Reject Image</button>');
			for(var i = 0; i < newarr[id].length; i++) {
				$('#image').append('<img id = "image_' + delete_id + '_' + i +'"class="a" src="'+ newarr[id][i] + '" style = "width:100%">');
			}
			slideIndex = 1;
			showDivs(slideIndex);	
			$('#myModal').show();
			
		}
	}
		
	function plusDivs(n) {
	  showDivs(slideIndex += n);
	}
	
	function showDivs(n) {
	 
	  var i;	  
	  var x = document.getElementsByClassName("a");
	  if ( n > x.length) {
		slideIndex = 1;
		}    
	  if (n < 1) {
		  slideIndex = x.length;
		}
	  for (i = 0; i < x.length; i++) {
		 x[i].style.display = "none";  
	  }
	  if( x[slideIndex-1] != undefined) {
		 $('#image_name').empty();
		 x[slideIndex-1].style.display = "block";  
		 $('#image_name').append('<h4 style="text-align:center">Image Name : '+ $('#'+ x[slideIndex-1].id).attr('src').replace(/^.*[\\\/]/, '') +'</h4>');

	  }
	  
	  $('#delete_image'+delete_id).unbind().on('click',function() { 
		  
		  if(n<1) {
			n = x.length;
		  }
		  if(n>x.length) {
			n = 1;
		  }
		  var str1 = "image_";
		  var str2 = delete_id;
		  var str3 = "_";
		  var str4 = n-1;
		  
		  var image_id = str1.concat(str2,str3,str4); 
		  var delete_image = document.getElementById(image_id);
		  var row_id = delete_id.substr(delete_id.length - 1); //storing row number
		  var table_id = delete_id[0]; //storing table number
		
		  var td_image = "display_".concat(table_id,"image_",row_id);

		  var st1 = "image_0_";
		  var st3 = "_";
		  var st4 = n-1;
		  var image_id = st1.concat(str2);
		  var id = image_id;
		  newarr[id].splice(n-1, 1);
		  delete_image.remove();
		  $('#image').empty();
		  $('#image').append('<button id = "delete_image' + delete_id + '" class="w3-button btn-sm btn-danger" ><i class="fa fa-folder">Reject Image</i></button>');

		  for(var i = 0; i < newarr[id].length; i++) {
			$('#image').append('<img id = "image_' + delete_id + '_' + i +'"class="a" src="'+ newarr[id][i] + '" style = "width:100%">');
		  }
		  if(newarr[id].length == 0) {
			  alert("no images to show");
			  $('#myModal').modal('toggle');
			  $('#'+td_image).html("<a id = 'image_0_" + table_id + row_id + "' onclick='javascript:show_image(this,\"" + newarr[id] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + newarr[id][0] + "'></a>");

		  }
		  //$('#display_1image_0').html("<a id = 'image_0_" + num[1]+i+"' onclick='javascript:show_image(this,\"" + data[sku_arr[i]][0] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + new_arr[id][0] + "'></a>");

		  slideIndex = n;
		  showDivs(slideIndex); 
		  $('#'+td_image).html("<a id = 'image_0_" + table_id + row_id + "' onclick='javascript:show_image(this,\"" + newarr[id] + "\");'><img  height='100' width='60'  data-toggle='modal' data-target='#myModal' src='" + newarr[id][0] + "'></a>");
 
	  });
	}
	
	
	
</script>
	
<style>


.color {
	color : black;
}
.a {display:none}
.success {
	color : green;
}
.error {
	color : red;
}
</style>
</html>

<?php echo $footer; ?>
