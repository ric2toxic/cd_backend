
<!DOCTYPE html>
<html>
<head>
   
	<!-- tpl file to display jqgrid table in bulk inventory upload -->
	<!-- Author - Divya Porwal-->
	
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/redmond/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="http://www.ok-soft-gmbh.com/jqGrid/jquery.jqGrid-3.8.2/css/ui.jqgrid.css" /
		<link rel="stylesheet" href="catalog/view/javascript/jquery-ui.min.css">
		<link rel="stylesheet" href="catalog/view/javascript/ui.jqgrid.css"/>
		<link rel="stylesheet" href="catalog/view/javascript/qunit-1.22.0.css">
		<link rel="stylesheet" href="catalog/view/javascript/jquery.flexbox.css">
		<!-- Latest compiled and minified CSS -->
		<link rel = "stylesheet" href = "catalog/view/javascript/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/display_jqgrid.css">

		<style>
			html, body { font-size: ; }
			.ui-jqgrid .ui-jqgrid-bdiv .myAltRowClass {
				background-color: #DCFFFF;
				background-image: none;
			}
			jQuery.jgrid.defaults.width = 500;
			jQuery.jgrid.defaults.responsive = true;
			jQuery.jgrid.defaults.styleUI = 'Bootstrap';
		</style>
		<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
		<script src="catalog/view/javascript/jquery-ui.min.js"></script>
		<script src="catalog/view/javascript/jquery.flexbox.js"></script>
		<script src="catalog/view/javascript/jquery.jqGrid.min.js"></script>
		<script src="catalog/view/javascript/grid.locale-en.js"></script>
		<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js"></script>
		<script src="catalog/view/javascript/jquery-ui.min.js"></script>
		<script src="catalog/view/javascript/jquery.flexbox.js"></script>
		<script src="catalog/view/javascript/jquery.jqGrid.min.js"></script>
		<script src="catalog/view/javascript/grid.locale-en.js"></script>
		<script src="catalog/view/javascript/grid.base.js"></script>
		<script src="catalog/view/javascript/grid.common.js"></script>
		<script src="catalog/view/javascript/grid.formedit.js"></script>
		<script src="catalog/view/javascript/grid.inlinedit.js"></script>
		<script src="catalog/view/javascript/grid.custom.js"></script>
		<script src="catalog/view/javascript/jquery.fmatter.js"></script>
		<script src="catalog/view/javascript/jquery.searchFilter.js"></script>
		<script src="catalog/view/javascript/jquery.jqgrid.src.js"></script>
		<script src="catalog/view/javascript/qunit-1.22.0.js"></script>


	</head>
	<style>
	.ui-jqgrid .ui-jqgrid-bdiv tr.ui-row-ltr > td {
		text-align: center; !important
		border-left-width: 0;
		border-left-style: none;
		border-right-width: 1px;
		border-right-style: solid;
		
	}
	.ui-jqgrid tr.jqgrow > td, .ui-jqgrid tr.jqgroup > td, .ui-jqgrid tr.jqfoot > td {
		font-weight: 0 ;!important
	}
	.classname {
		
	}
	
	.ui-jqgrid-bdiv {
		height: 500px; !important
	}
	.ui-autocomplete { z-index:2147483647; }
	.hide_size_option {
		display : none;
	}
	.find_br {
		height : 6vh;
	}
	
	br + br { display: none; }
	
	#hidden_previous {
		display : none;
	}
	
	.ui-widget-header {
		border: 1px solid #4297d7;
		background: #5c9ccc url(images/ui-bg_gloss-wave_55_5c9ccc_500x100.png) 50% 50% repeat-x;
		font-weight: bold;
		height: 29px;
	}
	
	</style>
	
	<body>
		 
	<div id="outerDiv" style="margin:5px;">
		<div style="width:100% height : 100%;overflow:auto;">
			<table id="grid"></table>
			<div id="pager"></div>
		</div>
		<br>
			
		<button id ="submittable" class="btn btn-sm btn-success"  ><span class="glyphicon glyphicon-floppy-saved"></span> Save Data</button>
		<div class = "sidebyside" id = 'firstdiv'>
			<div class="col-sm-6">
				<div class="col-sm-6">
					<table id = "filters"></table>
				</div>
				<div>
					<table id="filter_groups"></table>
				</div>
				<div>
					<div class="col-sm-6" id="firstdiv_1">
					<table id="grid2"></table>
					<div id="pager2"></div>
					<br>
					<button id = "submit_button" class ="hid  btn btn-sm btn-success"><span class="glyphicon glyphicon-floppy-saved"></span> Submit Filter</button>
				</div>
			
			</div>
			</div>
			
			<div class = "col-sm-6">
				<table id="filters_nonmandatory"></table>
				<table id="filters_groups_nonmandatory"></table>
			</div>
		</div>
		<!--<div class = "sidebyside" id = 'firstdiv_1'>
			<div class="column">
				<table id="grid2"></table>
				<div id="pager2"></div>
				<br>
				<button id = "submit_button" class ="hid  btn btn-sm btn-success"><span class="glyphicon glyphicon-floppy-saved"></span> Submit Filter</button>
			</div>
			<div class="column">
				<table id="gridinstruct1"></table>
				<div id="instructp1"></div>
			</div>
		</div> -->
		<div class="sidebyside" id = 'non_mandatory_div'>
			<div class="column">
				<table id="gridnonman"></table>
				<div id="pagernonman"></div>
				<br>
				<button id = "submit_button_nonman" class ="hid  btn btn-sm btn-success"><span class="glyphicon glyphicon-floppy-saved"></span> Submit Filter</button>
			</div>
			<div class="column">
				<table id="gridinstruct1"></table>
			<!--	<div id="instructp1"></div>-->
			</div>
		</div>

		<div class = "sidebyside" id = 'seconddiv'>
			<div class="column">
				<table id = "grid4"></table>
				<table id="pager4"></table>
			</div>
			<div class="column">
			<table id="grid3"></table>
				<div id="pager3"></div>
				<br>
				<button id = "button3" class ="hid  btn btn-sm btn-success">Submit Data</button>
			</div>
			<div class="column">
				<table id="gridinstruct2"></table>
				<div id="instructp2"></div>
			</div>
		</div>
			
		<div class = "sidebyside" id = 'fourthdiv'>
			<div class="column2">
				<table id="colorgrid"></table>
				<div id="pagercolor"></div>
			<!--	<br>
				<button id="color" class ="hid  btn btn-sm btn-success">Save Color</button>-->
			</div>
			<div class="column2">
				<table id="colorins"></table>
				<div id="colorinsp"></div>
			</div>	
		</div>

		<div class = "sidebyside" id = 'thirddiv'>
			<div class="column2">
				<table id = "sizegrid"></table>
				<table id="pagersize"></table>
			<!--	<br>
				<button id="size" class ="hid  btn btn-sm btn-success">Save Size</button>-->
			</div>
			<div class="column2">
				<table id="sizeins"></table>
				<div id="sizeinsp"></div>
			</div>
		</div>	
		<div class = "sidebyside" id = 'fifthdiv'>
			<div class="column">
				<table id = "freegrid"></table>
				<table id="pagerfree"></table>
				<br>
				<button id="free" class ="hid  btn btn-sm btn-success">Save Free Size</button>
			</div>
			<div class="column">
				<table id="freeins"></table>
				<div id="freeinsp"></div>
			</div>
		</div>
		<div class = "sidebyside" id = 'sixthdiv'>
			<div class="column">
				<table id = "notspecificgrid"></table>
				<table id="pagernotspecific"></table>
				<br>
				<button id="notspecific" class ="hid  btn btn-sm btn-success">Save Data</button>
			</div>
			
		</div>
	</div>	
	</body>
	
	<div class="modal fade" id="size_modal" role="dialog">
    <div class="modal-dialog modal-md">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="size_set_title"></h3>
        </div>
        <div class="modal-body">
			<div class = "sidebyside">
				<div id='size_set_body' class="col-sm-4"></div>
				<input id='hidden_previous' type="hidden">
				<div class="col-sm-1"></div>
				<div id='sizes_in_set' class="col-sm-3"></div>
				<div class="col-sm-1"></div>
				<div id='quantity' class="col-sm-3"></div>
			</div>
        </div>
        <div id = 'error-msg'>
        </div>
      <div class='modal-footer'>
		<button class="w3-button btn-sm btn-warning" id = "modal-close-size">Close</button>

		<button class="w3-button btn-sm btn-success" id = "submit_size_set" >Submit</button>

      </div>
      
      </div>
      
    </div>
  </div>
  
  	<div class="modal fade" id="color_modal" role="dialog">
    <div class="modal-dialog modal-md">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="color_set_title"></h3>
        </div>
        <div class="modal-body">
			<div class = "row">
				<!--<div class="col-md-6 coloroptions">
					<div>
						Color Options Available
					</div>
					<div class = "row">-->
						<div id='no_of_colors' class="col-sm-2"></div>
						<input id='hidden_previous2' type="hidden">

						<div id='colors_in_set' class="col-sm-2"></div>
				<!--	</div>
				</div>
				
				<div class="col-md-6 sizeoptions">
					<div>
						Size Options Available
					</div>
					<div class="row">-->
						<div id='size_options' class="col-sm-3"></div>
						<div id='sizes' class="col-sm-2"></div>
						<div id='quantity_sizes' class="col-sm-2"></div>
					<!--</div>
				</div>-->
			</div>
        </div>
        <div id = 'error-msg-color'>
        </div>
      <div class='modal-footer'>
		<button class="w3-button btn-sm btn-warning" id = "modal-close-color">Close</button>

		<button class="w3-button btn-sm btn-success" id = "submit_color_set" >Submit</button>

      </div>
      
      </div>
      
    </div>
  </div>

	<script>
		//<![CDATA[
		/*global $ */
		/*jslint browser: true */
			$(function () {
			"use strict";
			var keys = [[]];
			var final_arr = [];
			var final_arr_non_mandatory = [];
			var grid_data_to_show = [];
			var filtergroup_nonmandatory = [];
			var filter_id = [];
			var final_groups = [];
			var final_groups_nonmandatory = [];
			var filtergroup = [];
			var non_mandatory_filter_groups = [];
			var filter_group_length;	
			var non_mandatory_filter_groups_length;	
			var non_mandatory_filters_array = [];	
			var filtergroups_nonmandatory = [];	
			var lastsel2;
			var set_count;
			var check = 0 ;
			var warnings = 0;
			var which_set = 0;
			var removed_arr = []; //storing deleted rows
			var set_type_arr = [];
			var product_ids = [];
			var product_ids_old = [];
			var mandatory_filters = 0;
			var allGridParams = [];
			var allGridData = [];
			var empty_nonmandatory = 0;
			var added_filters_non_mandatory = [];
			
			var submit_no_error = 0;
			var data = <?php print json_encode($final_data); ?>;
			var flag = <?php print json_encode($flag); ?>;
			var row_count = <?php print json_encode($row_count); ?>;
			var col_count = <?php print json_encode($col_count); ?>;
			var headers = <?php print json_encode($headers['headers']); ?>;
			var rules = <?php print json_encode($rules); ?>;
			var import_csv_id = <?php print json_encode($import_csv_id); ?>;
			var original_data = <?php print json_encode($original_data); ?>;
			var indexes_of_error = <?php print json_encode($indexes_of_error); ?>;
			var category_id = <?php print json_encode($category_id); ?>;
			var product_ids = <?php print json_encode($product_ids);?>;
			product_ids_old  = product_ids.slice(0);
			var array =  ['name', 'label'];
			var lastSel = -1;
			var mydata = [];
			var selRow=1;
			var listOfColumnModels = [];
			var listOfColumnNames = [];
			var filter_groups = [];
			var filters_name2 = [];
			var filters_id = [];
			var button_count = 0;
			var button_count_non_mandatory = 0;
			var non_mandatory_filters_name2 = [];
			var non_mandatory_filters_id = [];
			
			
			var deleteRow = function(arr, row) {
				var arr2 = [];
				for (var i = 0; i < arr.length; i = i+2) {
					arr2.push(arr[i]); //deleteing every alternate row 
				}
				return arr2;
			}	
			var states = ['Size Set','Color Set', 'Free Size', 'Not Specific'];
			var availableSizes = ['24','26','28','30','32','34','36','38','40','42','44','46','48'];
			var availableColors =  ['yellow', 'green', 'red', 'blue'];


			//creating grid for size set
			var createSizeGrid = function (size) {
				var grid = $('#sizegrid');
				var flag_error = 0;
				var data = [];
				for ( var i = 0; i < size.length;  i++ ) {
					data[i] = []; 
				}
				for(var i =0 ;i < size.length; i++) {
					data[i]['SKU Code'] = size[i];
					data[i]['Sizes in Set'] = '';
					data[i]['Pieces in Set'] = '';
				}
				
				$('#sizegrid').jqGrid({
					datatype: "local",
					data: data,
					//pager: true,
					cellLayout:30,
					editurl: 'clientArray',
					align : "center",
					/*ondblClickRow: function(id) {
					  if (id) {
						$('#sizegrid').jqGrid('restoreRow', lastsel2);
						$('#sizegrid').jqGrid('editRow', id, true);
						lastsel2 = id;
						}
						},	*/		
					loadonce: true,	
					rownumbers : true,
					sortname: 'id',
					sortorder: "desc",
					sortable: true,
					cellLayout:30,
				//	forceFit : true,
					cellEdit: true,
					align : 'center',
				//	rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					cellsubmit: 'clientArray',
				//	pgbuttons: false, 
				//	pgtext: null,
					colModel: [
					{name:'SKU Code',index:'SKU Code',align : "center",},
					{name : 'Select Size', index: 'Select Size', formatter: function(cellvalue, options, rowObject) {
						return '<button class="btn-sm btn-default" title="Add">Add</button>'; 
						},
						formatoptions: {
						keys: true,
						editbutton: false,
						deletebutton: false,
						delOptions: {
							url: 'index.php?route=seller/seller_upload/getDelete',
							mtype: "POST", 
							keys: true
							}
						  },
					   sortable : false,
					},
					/*{name:'Sizes in Set',index:'Size', editable: true, align : "center",edittype:'text', formatter:numFormat, unformat:numUnformat,
					editrules: { 
						required: true
					}},	
					{name:'Pieces in Set',index:'Pieces in Set', editable: true, align : "center", edittype:'text',
						formatter:numFormat2, unformat:numUnformat2, editrules: { required: true}},
					{name:'Minimum Pieces in Order',index:'Minimum Pieces in Order',
						editable: true, edittype:'text',
						editrules: { required: true}, formatter:numFormat, unformat:numUnformat,
					},*/
					],
					caption : 'Size Set Description',
					gridComplete: function () {
						var ids = [];
						ids = jQuery("#sizegrid").jqGrid('getDataIDs');
						
						var items = [];
						var count_btn = 0;
						for(var variable=0;variable<ids.length;variable++) {
							var rowId = ids[variable];	
							var rowData = jQuery('#grid').jqGrid ('getRowData', rowId);
							var a = jQuery('#grid').jqGrid('getCell',rowId, 'SKU Code'); 
							var b = jQuery('#grid').jqGrid('getCell',rowId, 'Pieces in Set'); 
							var c = jQuery('#grid').jqGrid('getCell',rowId, 'Transfer Price'); 
							var d = jQuery('#grid').jqGrid('getCell',rowId, 'AvailableSets'); 
							var e = jQuery('#grid').jqGrid('getCell',rowId, 'Weight of a Piece (in gm)'); 
							var f = jQuery('#grid').jqGrid('getCell',rowId, 'Size Set / Color Set'); 
							var g = jQuery('#grid').jqGrid('getCell',rowId, 'Size'); 
							var h = jQuery('#grid').jqGrid('getCell',rowId, 'Set Description'); 
							var i = jQuery('#grid').jqGrid('getCell',rowId, 'Any additional Comments'); 
							var j = jQuery('#grid').jqGrid('getCell',rowId, 'Tax');

							jQuery("#sizegrid").find('Select Size').addClass('size_button'+count_btn);
							jQuery('#sizegrid').jqGrid('setCell', rowId,  'Select Size', '', 'size_button'+count_btn);
							$('.size_button'+count_btn).attr("id","size_id_"+count_btn);
							count_btn++;
						}
					}
						
				}).jqGrid('navGrid', '#pagersize');	
				
                //checking for which size set button has been clicked for adding sizes in grid
			
				var size_ids = 	jQuery("#sizegrid").jqGrid('getDataIDs');
				var check_size_count = 0;
				var number_sizes;
				for(var i = 0; i < size_ids.length; i++) {
					$('#size_id_'+i).on('click',function(){
						
						var id = $(this).prop("id");
						$('#size_modal').modal('show'); 
						var trHTML = '';
						var modalHTML = '';
						var flag = 0;
						$('#submit_size_set').prop("disabled",true);

						var trId = $(this).closest('tr').prop('id');
						var skuid = $("#" + trId + ' td:nth-child(2)').text();
						$('#size_set_body').empty();
						$('#size_set_title').empty();
						$('#sizes_in_set').empty();
						$('#quantity').empty();
						
						modalHTML = '<h6><b>Size Set Description for SKU <i>' + skuid + '</i></b></h6>';
						trHTML += '<div><div class="col-sm-10"><label for="input_' + id + '">Pieces in Set </label>';
						trHTML += '<input class="form-control" type="text"id = "input_'+ id + '"></div></div>';
						
						$('#size_set_title').append($(modalHTML));
						$('#size_set_body').append($(trHTML));
						
						var data_in_text;

						$('#hidden_previous').val(0);
						
						$('#input_'+id ).on('keyup',function(e) {
							
							if(e.which === 13 || e.which === 10 || e.which === 8 || e.which === 9 || e.which === 16){
								return false;
							}
							
							var data_text = $('#input_'+id ).val();
							number_sizes = data_text;

							var data_text = $('#input_'+id ).val();
							data_in_text = data_text;
							var prev = $('#hidden_previous').val();
							$('#hidden_previous').val(data_in_text);
							//$(this).data('val');
							var added = 0;
							var removed = 0;
							
							if(!isNaN(data_text) && parseInt(data_text) < 10) {
								var sizesHTML = '';
								var quantityHTML = '';
								
								
								// taking care only last boxes get added and removed 
								
								if(prev == undefined || prev == "") {
									prev = 0;
									added = data_in_text;
									removed = 0;

								}
								if(data_in_text < prev) {
									removed = prev - data_in_text ;
									
								} else if(data_in_text > prev) {
									
									added = data_in_text - prev;
								}
								
								$('#error-msg-color').empty();
								$('#input_'+id ).css('border-color','black');

								if($('.quantity_size_set').length == 0 && $('.size_class').length == 0) {
									sizesHTML += '<div class="col-sm-8"><label class="label-control size_class">Sizes</label></div>';
									quantityHTML += '<div class="col-sm-8"><label class="label-control quantity_size_set">Quantity</label></div>';								
								}
								
								if(added != undefined || added!= 0) {
									
									
									for(var k = data_in_text-1; k >= prev; k--) {
										
										sizesHTML += '<input class="form-control error select_size" id = "sizes_' + id + '_' + k + '" type="text" ><br>';
										quantityHTML += '<input class="form-control error select_quantity" id = "quantity_' + id + '_' + k + '" type="text"><br>';

									}	
								}
								
								if(removed != undefined || removed!= 0) {
									for(var k = 0; k < removed; k++) {
										
										$(".select_size:last").remove();
										$(".select_quantity:last").remove();
										
									}						
								}

								sizesHTML += '</div>';
								quantityHTML += '</div>';
								
						
							
								$('#sizes_in_set').append($(sizesHTML));
								$('#quantity').append($(quantityHTML));
								$('#submit_size_set').prop("disabled",false);
								$('#submit_size_set').unbind().on('click',function() {
									
									var size_array = [];
									var quantity_array = [];
									if( $('#input_'+id ).val() == undefined) {
										alert("please enter size description");
									}else {
										
										if(checkDuplicates_size() === false) {
											alert("duplicates not allowed");
											$('.select_size' ).css('border-color','red');
										} else if($('.select_quantity.error').length != 0 ){
											alert("please enter correct values!");
											$('.select_quantity' ).css('border-color','red');
										} else if($('.select_size.error').length != 0 ) {
											alert("please enter correct values!");
											$('.select_size' ).css('border-color','red');
											
										} else {

											$('#submit_size_set').prop("disabled",false);
											check_size_count++;
											for(var k = 0; k < parseInt(data_text); k++) {
												size_array.push( $('#sizes_' + id + '_' + k).val());
												quantity_array.push($('#quantity_' + id + '_' + k).val());
											}	
											$('#'+id + ' > .btn-sm').html('Done');
											$('#'+id + ' > .btn-sm').attr('disabled',true);
											make_new_grid(id,skuid,data_text,size_array,quantity_array,check_size_count);
											alert("Size Description submitted");
											$('#size_modal').modal('toggle');
										}	
									}
								});

							} else {   
								$('#input_'+id ).css('border-color','red');
								$('#error-msg').empty();

								$('#error-msg').append('<p style="color:red;">Please enter valid number in text box</p>');
							} 
						});
					});	
							
				}
				
				var checkDuplicates_size = function() {
					
					var sizes = [];
					
					$( ".select_size" ).each(function() {
						
						sizes.push($( this ).val());
					});
					
					return ((new Set(sizes)).size === sizes.length);
				}
				
				$("#sizes_in_set").delegate('.select_size','keydown',function() {
					
					var value = $(this).val();
					
					
					$(this).autocomplete({
					  source: availableSizes,
					  minLength: 0,
					  select: function( event, ui ) {
						  $(this).val(ui.item.value);
						  //triggering key up on select itself!
						  $(this).keyup();
						 }
					});
					
					$(this).on('keyup',function() {
						if($.inArray( $(this).val(), availableSizes ) == -1) {
							$(this).css('border-color','red');
							$(this).addClass('error');
						} else {
							$(this).removeClass('error');
							$(this).css('border-color','green');
						}
					});
				});
				
				
				$("#quantity").delegate('.select_quantity','keydown',function() {
					var id = this.id;
					var value = $('#'+id).val();
					
					$(this).on('keyup',function() {
						if(( isNaN($(this).val())) || $(this).val() == "") {
							$(this).css('border-color','red');
							$(this).addClass('error');
						} else {
							$(this).removeClass('error');
							$(this).css('border-color','green');
						}
					});
				});
				
				$('#modal-close-size').on('click',function() {
					
					$('#size_modal').modal('toggle');
					
				});
				
				var make_new_grid = function(id,skuid,data_text,size_array,quantity_array,check_size_count) {
					
					set_count = 'S';
					submit_no_error++;

					var inventory_data = jQuery('#grid').jqGrid('getRowData');
					if(set_count === 'S' && submit_no_error == 1) {
						for(var i =0; i < inventory_data.length; i++) {
							inventory_data[i]['Sizes in Size Set'] = '';
							inventory_data[i]['Pieces in Size Set'] = '';
							inventory_data[i]['Color Description'] = '';
							inventory_data[i]['Color Set quantity'] = '';
							inventory_data[i]['Color Set Sizes'] = ''; 
							inventory_data[i]['Pieces in Free Size Set'] = '';	
							inventory_data[i]['Free Size quantity'] = '';	
							inventory_data[i]['Pieces in Set'] = '';	
							inventory_data[i]['Quantity'] = '';
						}
					}
					
					var rowdata = [];
					
					rowdata['Sizes in Size Set'] = size_array.toString();
					rowdata['Pieces in Size Set'] = quantity_array.toString();
					rowdata['Color Set quantity'] = 'NA';
					rowdata['Color Description'] = 'NA';
					rowdata['Color Set Sizes'] = 'NA';
					rowdata['Pieces in Free Size Set'] = 'NA';
					rowdata['Free Size quantity'] = 'NA';
					rowdata['Pieces in Set'] = 'NA';	
					rowdata['Quantity'] = 'NA';
					rowdata['SKU Code'] = skuid;
					
					for(var i =0 ;i<inventory_data.length; i++) {
						if(rowdata['SKU Code'] == inventory_data[i]['SKU Code']) {
							inventory_data[i]['Sizes in Size Set'] = rowdata['Sizes in Size Set'];
							inventory_data[i]['Pieces in Size Set'] = rowdata['Pieces in Size Set'];
							inventory_data[i]['Color Set quantity'] = 'NA';
							inventory_data[i]['Color Set Sizes'] = 'NA';
							inventory_data[i]['Color Description'] = 'NA';
							inventory_data[i]['Pieces in Free Size Set'] = 'NA';
							inventory_data[i]['Free Size quantity'] = 'NA';	
							inventory_data[i]['Pieces in Set'] = 'NA';	
							inventory_data[i]['Quantity'] = 'NA';

						}
					} 
					
					var cm = jQuery("#grid").jqGrid("getGridParam", "colModel");		
					
					cm.shift();
				
					if(set_count === 'S' && submit_no_error == 1) {
						var i = 0;
						var sku_code = cm.shift();
						var set_type = cm.shift();

						cm.unshift({
							name : 'Color Set quantity',
							index : 'Color Set quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Color Set Sizes',
							index : 'Color Set Sizes',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Color Description',
							index : 'Color Description',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Sizes in Size Set',
							index : 'Sizes in Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Pieces in Size Set',
							index : 'Pieces in Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						
						cm.unshift({
							name : 'Pieces in Free Size Set',
							index : 'Pieces in Free Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
						
						cm.unshift({
							name : 'Free Size quantity',
							index : 'Free Size quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
						cm.unshift({
							name : 'Pieces in Set',
							index : 'Pieces in Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
							
						cm.unshift({
							name : 'Quantity',
							index : 'Quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
						cm.unshift(set_type);
						cm.unshift(sku_code);
					}
					
					jQuery("#grid").jqGrid('GridUnload');
					
					creatNewGrid(cm,inventory_data);
					

					if(check_size_count == size.length) {
						which_set++;
						
						if(jQuery.isEmptyObject(free) == false) {
							createfreeset(free, which_set);
						} else {
							createnotspecificset(notspecific, which_set);
						}
						
						jQuery('#sizegrid').jqGrid('GridUnload');
						//hiding submit button to avoid any changes to the submitted data
						jQuery('#size').hide();
						$('#thirddiv').remove();
						//sending data via ajax and db insertion
						jQuery.ajax({
							url: "index.php?route=seller/seller_upload/getSize",
							type: 'POST',
							datatype : 'json',		
							data: {Size_Set: inventory_data, product_ids:product_ids, set_type_arr:set_type_arr},
							success: function (data) {
								data = JSON.parse(data);
								if(data.msg == 1) {
									alert("Size Set submitted successfully");
								} else {
									alert("Data inserted successfully! WholesaleBox team will validate this data");
								}

							}
						});

					}

					};	
				};
				
			//ends size set grid
				
			//creating grid for color set
			var createColorGrid = function(color) {
				var grid = $('#colorgrid');
				var flag_error = 0;
				var data = [];
				for ( var i = 0; i < color.length;  i++ ) {
					data[i] = []; 
				}
				for(var i =0 ;i < color.length; i++) {
				
					data[i]['SKU Code'] = color[i];
					data[i]['Number of Pieces'] = '';
					data[i]['Number of Sets'] = '';
					data[i]['Color Description'] = '';
					data[i]['Sizes'] = '';
				}
				var lastsel2;
				
				jQuery('#colorgrid').jqGrid({
					datatype: "local",
					data: data,
				//	pager: true,
					cellLayout:20,
					editurl: 'clientArray',	
					loadonce: true,	
					rownumbers : true,
					cellLayout:20,
				//	forceFit : true,
					rowNum: 9999,
				//	gridview: true,
					align : 'center',
					cellEdit: true,
				//	rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					cellsubmit: 'clientArray',
					align : "center",
				//	pgbuttons: false, 
				//	pgtext: null,
					/*ondblClickRow: function(id) {
					  if (id) {
						$('#colorgrid').jqGrid('restoreRow', lastsel2);
						$('#colorgrid').jqGrid('editRow', id, true);
						lastsel2 = id;
						}
					},*/
					colModel: [
					{name:'SKU Code',index:'SKU Code',align : "center"},
					{name : 'Select Color', index: 'Select Color', formatter: function(cellvalue, options, rowObject) {
						return '<button class="btn-sm btn-default" title="Add">Add</button>'; 
						},
						formatoptions: {
						keys: true,
						editbutton: false,
						deletebutton: false,
						delOptions: {
							url: 'index.php?route=seller/seller_upload/getDelete',
							mtype: "POST", 
							keys: true
							}
						  },
					   sortable : false,
						},
					 ],
					gridComplete: function () {
						var ids = [];
						ids = jQuery("#colorgrid").jqGrid('getDataIDs');
						
						var items = [];
						var count_btn = 0;
						for(var variable=0;variable<ids.length;variable++) {
							var rowId = ids[variable];	
							var rowData = jQuery('#grid').jqGrid ('getRowData', rowId);
							var a = jQuery('#grid').jqGrid('getCell',rowId, 'SKU Code'); 
							var b = jQuery('#grid').jqGrid('getCell',rowId, 'Pieces in Set'); 
							var c = jQuery('#grid').jqGrid('getCell',rowId, 'Transfer Price'); 
							var d = jQuery('#grid').jqGrid('getCell',rowId, 'AvailableSets'); 
							var e = jQuery('#grid').jqGrid('getCell',rowId, 'Weight of a Piece (in gm)'); 
							var f = jQuery('#grid').jqGrid('getCell',rowId, 'Size Set / Color Set'); 
							var g = jQuery('#grid').jqGrid('getCell',rowId, 'Size'); 
							var h = jQuery('#grid').jqGrid('getCell',rowId, 'Set Description'); 
							var i = jQuery('#grid').jqGrid('getCell',rowId, 'Any additional Comments'); 
							var j = jQuery('#grid').jqGrid('getCell',rowId, 'Tax');

							
							jQuery("#colorgrid").find('Select Color').addClass('color_button'+count_btn);
							jQuery('#colorgrid').jqGrid('setCell', rowId,  'Select Color', '', 'color_button'+count_btn);
							$('.color_button'+count_btn).attr("id","color_id_"+count_btn);
							count_btn++;
						}
					},
					
			
				 rownumbers : true,
				 caption : 'Color Set Description',
				}).jqGrid('navGrid', '#pagercolor');

				var color_ids = jQuery("#colorgrid").jqGrid('getDataIDs');
				var check_color_count = 0;
				var show = 0;
				for(var i = 0; i < color_ids.length; i++) {
					
					$('#color_id_'+i).on('click',function(){
						
						$('#no_of_colors').empty();
						$('#colors_in_set').empty();
						$('#size_options').empty();
						$('#sizes').empty();
						$('#quantity_sizes').empty();
						
						var id = $(this).prop("id");
						
						$('#color_modal').modal('show'); 
						$('#submit_color_set').prop("disabled",true);
						var trHTML = '';
						var modalHTML = '';
						var flag = 0;
						
						var trId = $(this).closest('tr').prop('id');
						var skuid = $("#" + trId + ' td:nth-child(2)').text();
						
						$('#color_set_title').empty();
						$('#no_of_colors').empty();
						
						modalHTML = '<h6><b>Color Set Description for SKU <i>' + skuid + '</i></b></h6>';
						trHTML += '<div><div ><label for="input_' + id + '">No. of Colors</label>';
						trHTML += '<input class="form-control" type="text"id = "input_'+ id + '"></div></div>';
						
						$('#color_set_title').append($(modalHTML));
						$('#no_of_colors').append($(trHTML));
						
						var data_in_text;
						
						$('#hidden_previous2').val(0);
						$('#input_'+id ).on('keyup',function(e) {
							
							if(e.which === 13 || e.which === 10 || e.which === 8 || e.which === 9 || e.which === 16){
								return false;
							}
							
							var colorHTML = '';
							var data_text = $('#input_'+id ).val();
							data_in_text = data_text;
							var prev = $('#hidden_previous2').val();
							$('#hidden_previous2').val(data_in_text);
							var current = $(this).val();
							var added = 0;
							var removed = 0;
							
							if(!isNaN(data_text) && parseInt(data_text) < 10) {
								
								
								/* taking care only last boxes get added and removed */
								if(prev == undefined || prev == "") {
									prev = 0;
									added = data_in_text;
									removed = 0;
								}
								
								if(data_in_text < prev) {
									
									removed = prev - data_in_text ;
									
								} else if(data_in_text > prev) {
									
									added = data_in_text - prev;
									
								}
								
								$('#error-msg-color').empty();
								$('#input_'+id ).css('border-color','black');
								//$('#colors_in_set').empty();
								$('#size_options').empty();
								$('#sizes').empty();
								$('#quantity_sizes').empty();
								

								if($('.color_class').length == 0) {
									colorHTML += '<div><label class="label-control color_class">Colors</label></div>';
								}
								
								if(added != undefined || added!= 0) {
									
									for(var k = data_in_text-1; k >= prev; k--) {
										
										colorHTML += '<input class="form-control error colors_description" id = "colors_' + id + '_' + k + '" type="text" ><br>';									
									}
									if($('.hide_size_option').is(":visible")) {
										
										$('.hide_size_option').hide();
									}
									
								} 
								
								if(removed != undefined  || removed!= 0) {

									for(var k = 0; k < removed; k++) {
										
										$(".colors_description:last").remove();
										$('<br>').hide();
									}						
								}

								colorHTML += '</div>';
								
								$('#colors_in_set').append($(colorHTML));

								
								var sizeoptionHTML = '';
								
														
								sizeoptionHTML += '<div class="hide_size_option"><div><label for="size_option_' + id + '">Size Options</label>';
								sizeoptionHTML += '<input class="form-control hide_size_option" type="text" id = "size_option_'+ id + '"  ></div></div>';
								
								$('#size_options').append($(sizeoptionHTML));
								

								$('#size_option_'+id ).on('keyup',function() {
									
									var data_text = $('#size_option_'+id ).val();
									$('#sizes').empty();
									$('#quantity_sizes').empty();

									if(!isNaN(data_text) && parseInt(data_text) < 10) {
										$('#error-msg-color').empty();
										$('#size_option_'+id ).css('border-color','black');
										var different_sizesHTML = '';
										var quantity_sizesHTML = '';
										different_sizesHTML += '<div ><label class="label-control">Sizes</label></div>';
										quantity_sizesHTML += '<div ><label class="label-control">Quantity</label></div>';

										for(var k = 0; k < data_text; k++) {
											different_sizesHTML += '<div><input class="form-control error color_sizes" id = "sizes_' + id + '_' + k + '" type="text" ><br></div>';									
											quantity_sizesHTML  += '<div><input class="form-control error color_quantity" id = "quantitysizes_' + id + '_' + k + '" type="text"><br></div>';
											
										}
										
										$('#sizes').append($(different_sizesHTML));
										$('#quantity_sizes').append($(quantity_sizesHTML));
										$('#submit_color_set').prop("disabled",false);


									} else {
										$('#size_option_'+id ).css('border-color','red');
										$('#error-msg-color').empty();
										$('#error-msg-color').append('<p style="color:red;background-color : white">Please enter valid number in text box</p>');
									}
								});
								
								$('#submit_color_set').unbind().on('click',function() {

									var colors_array = [];
									var sizes_array = [];
									var quantity_array = [];
									var size_num = $('#size_option_'+id).val();

									if(checkDuplicates() !== 0) {
										alert("duplicates not allowed");
										if(checkDuplicates() === 1) {
											
											$('.colors_description' ).css('border-color','red');

										} else if(checkDuplicates() === 2) {
											
											$('.color_sizes' ).css('border-color','red');
										} else if(checkDuplicates() === 3) {
											
											$('.colors_description' ).css('border-color','red');
											$('.color_sizes' ).css('border-color','red');
											
										}

									} else if( $('#size_option_'+id ).val() == undefined) {
											
										alert("please enter size description");	
											
									} else if ($('.colors_description.error').length != 0) {
										
										$('.colors_description' ).css('border-color','red');
										alert("please enter correct values from options listed");	

									} else if($('.color_sizes.error').length != 0) {
										
										$('.color_sizes' ).css('border-color','red');
										alert("please enter correct values from options listed");	
										
									} else if($('.color_quantity.error').length != 0 ) {
										$('.color_quantity' ).css('border-color','red');
										alert("please enter correct values");	
									} else if(show == 0) {
										alert("please enter all the values");	
									} else {
											$('#'+id + ' > .btn-sm').attr('disabled',false);
											check_color_count++;
											for(var k = 0; k < parseInt(size_num); k++) {
												sizes_array.push( $('#sizes_' + id + '_' + k).val());
												quantity_array.push($('#quantitysizes_' + id + '_' + k).val());
											}
											$( ".colors_description" ).each(function() {
						
												colors_array.push($( this ).val());
												
											});
											/*for(var k = 0; k < parseInt(data_text); k++) {
												
												colors_array.push( $('#colors_' + id + '_' + k).val());
											}*/
										
											$('#'+id + ' > .btn-sm').html('Done');
											$('#'+id + ' > .btn-sm').attr('disabled',true);
											alert("color set for " + skuid + " submitted");
											make_color_grid(id,skuid,data_text,sizes_array,quantity_array,colors_array,check_color_count);
											$('#color_modal').modal('toggle');
									}
								});
							} else {   
								$('#input_'+id ).css('border-color','red');
								$('#error-msg-color').empty();
								$('#error-msg-color').append('<p style="color:red;background-color : white">Please enter valid number in text box</p>');
							} 
						});
					});	
							
				}
				
				var checkDuplicates = function() {
					var array_colors = [];
					var sizes = [];
					
					$( ".colors_description" ).each(function() {
						
						array_colors.push($( this ).val());
						
					});
					
					$( ".color_sizes" ).each(function() {
						
						sizes.push($( this ).val());
						
					});
					
					var color_var = (new Set(array_colors)).size === array_colors.length;
					var size_var = (new Set(sizes)).size === sizes.length;
					
					if((color_var && size_var) == true) {
						return 0;
					} else {
						if(color_var == false) {
							return 1;
						} else if(size_var == false) {
							return 2;
						} else if(size_var || color_var == false) {
							return 3;
						}
					}
					
				};
				
				var error_flag_in_color = 0;
				
		/*		$("#colors_in_set").delegate('.colors_description','click',function() {
					var id = this.id;
					var value = $('#'+id).val();
					$('#'+id).autocomplete({
					  source: availableColors
					});
					$('#'+id).change(function() {
						if($.inArray( $('#'+id).val(), availableColors ) == -1) {
							$('#'+id).css('border-color','red');
							$('#'+id).addClass('error');
							error_flag_in_color = 1;
							$('#size_options').prop("disabled",true);
						} else {
							$('#'+id).removeClass('error');
							$('#'+id).css('border-color','green');
						}
					
					});
					
					if($('.colors_description.error').length == 0) {

						alert("shhownow");
					}
				
				});
				*/
				
				$("#colors_in_set").delegate('.colors_description','keydown',function() {
					
					var value = $(this).val();
					var check_length = $(this).val().trim().length;
					$(this).autocomplete({
					  source: availableColors,
					  minLength: 0,
					  select: function( event, ui ) {
						  $(this).val(ui.item.value);
						  $(this).keyup();
						 }
					});
					
					
					$(this).on('keyup',function()  {
						if($.inArray( $(this).val(), availableColors ) == -1) {
							$(this).css('border-color','red');
							$(this).addClass('error');
							error_flag_in_color = 1;
						} else {
							$(this).removeClass('error');
							$(this).css('border-color','green');
						}
						//alert($('.hide_size_option').is(":visible"));
						
						if($('.colors_description.error').length == 0 && !$('.hide_size_option').is(":visible")) {
							$('.hide_size_option').show();
							show = 1;

						}
					});
				});
				
				
				
				$('#modal-close-color').on('click',function() {
					
					$('#color_modal').modal('toggle');
					
				});
				
				$("#sizes").delegate('.color_sizes','keydown',function() {
					
					$('#submit_color_set').prop("disabled",false);

					var id = this.id;
					var value = $('#'+id).val();
					
					$(this).autocomplete({
					  source: availableSizes,
					  minLength: 0,
					  select: function( event, ui ) {
						  $(this).val(ui.item.value);
						  $(this).keyup();
						 }
					});
					
					$(this).on('keyup',function()  {
						if($.inArray( $(this).val(), availableSizes ) == -1) {
							$(this).css('border-color','red');
							$(this).addClass('error');
							error_flag_in_color = 1;
						} else {
							$(this).removeClass('error');
							$(this).css('border-color','green');
						}
						
					});
				});
				
				$("#quantity_sizes").delegate('.color_quantity','keydown',function() {
					var id = this.id;
					var value = $('#'+id).val();
					
					$(this).on('keyup',function() {
						if(( isNaN($(this).val())) || $(this).val() == "") {
							$(this).css('border-color','red');
							$(this).addClass('error');
						} else {
							$(this).removeClass('error');
							$(this).css('border-color','green');
						}
						
					});
			
				});
				
			
				
				
				var make_color_grid = function(id,skuid,data_text,size_array,quantity_array,colors_array,check_color_count) {
					var rowdata = [];
					set_count = 'C';
					submit_no_error++;
					
					var set_array = [];
					for(var i = 0; i < size_array.length; i++) {
						set_array[i] = size_array[i].concat('-',quantity_array[i]);
						
					}
					
					var inventory_data = jQuery('#grid').jqGrid('getRowData');
					if(set_count === 'C' && submit_no_error == 1) {
						for(var i =0; i < inventory_data.length; i++) {
							inventory_data[i]['Sizes in Size Set'] = '';
							inventory_data[i]['Pieces in Size Set'] = '';
							inventory_data[i]['Color Set quantity'] = '';
							inventory_data[i]['Color Description'] = '';
							inventory_data[i]['Color Set Sizes'] = ''; 
							inventory_data[i]['Pieces in Free Size Set'] = '';	
							inventory_data[i]['Free Size quantity'] = '';
							inventory_data[i]['Pieces in Set'] = '';	
							inventory_data[i]['Quantity'] = '';
							
						}
					}
					
					var rowdata = [];
					
					rowdata['Sizes in Size Set'] = 'NA';
					rowdata['Pieces in Size Set'] = 'NA';
					rowdata['Color Set quantity'] = set_array.toString(); 
					rowdata['Color Description'] = colors_array.toString();
					rowdata['Color Set Sizes'] = 'NA';
					rowdata['Pieces in Free Size Set'] = 'NA';
					rowdata['Free Size quantity'] = 'NA';
					rowdata['Pieces in Set'] = 'NA';	
					rowdata['Quantity'] = 'NA';
					rowdata['SKU Code'] = skuid;
					
					for(var i =0 ;i<inventory_data.length; i++) {
						if(rowdata['SKU Code'] == inventory_data[i]['SKU Code']) {
		
							inventory_data[i]['Sizes in Size Set'] = 'NA';
							inventory_data[i]['Pieces in Size Set'] = 'NA';
							inventory_data[i]['Color Set quantity'] = quantity_array.toString();
							inventory_data[i]['Color Description'] = colors_array.toString();
							inventory_data[i]['Color Set Sizes'] = size_array.toString(); 
							inventory_data[i]['Pieces in Free Size Set'] = 'NA';	
							inventory_data[i]['Free Size quantity'] = 'NA';
							inventory_data[i]['Pieces in Set'] = 'NA';	
							inventory_data[i]['Quantity'] = 'NA';


						}
					} 
					
					var cm = jQuery("#grid").jqGrid("getGridParam", "colModel");		
					
					cm.shift();
				  
					if(set_count === 'C' && submit_no_error == 1) {
						var i = 0;
						var sku_code = cm.shift();
						var set_type = cm.shift();
						cm.unshift({
							name : 'Color Set quantity',
							index : 'Color Set quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Color Set Sizes',
							index : 'Color Set Sizes',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Color Description',
							index : 'Color Description',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Sizes in Size Set',
							index : 'Sizes in Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Pieces in Size Set',
							index : 'Pieces in Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						
						cm.unshift({
							name : 'Pieces in Free Size Set',
							index : 'Pieces in Free Size Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
						
						cm.unshift({
							name : 'Free Size quantity',
							index : 'Free Size quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});
						cm.unshift({
							name : 'Pieces in Set',
							index : 'Pieces in Set',
							editable: true,
							editrules: { required: true },
							align : "center",
						});	
							
						cm.unshift({
							name : 'Quantity',
							index : 'Quantity',
							editable: true,
							editrules: { required: true },
							align : "center",
						});		
						cm.unshift(set_type);
						cm.unshift(sku_code);
					}
					
					jQuery("#grid").jqGrid('GridUnload');
					
					creatNewGrid(cm,inventory_data);
					
					
					if(check_color_count == color.length) {
						which_set++;
						jQuery("#colorins").jqGrid('GridUnload');
						if(jQuery.isEmptyObject(size) == false) {
							jQuery("#colorins").jqGrid('GridUnload');
							createsizeset(size, which_set);
						} else if(jQuery.isEmptyObject(free) == false) {
							jQuery("#colorins").jqGrid('GridUnload');
							createfreeset(free, which_set);
						} else {
							createnotspecificset(notspecific, which_set);
						}
						jQuery('#colorgrid').jqGrid('GridUnload');
						//hiding submit button to avoid any changes to the submitted data
						jQuery('#color').hide();
						//$('#div').remove();
						//sending data via ajax and db insertion
						jQuery.ajax({
							url: "index.php?route=seller/seller_upload/getColor",
							type: 'POST',
							datatype : 'json',		
							data: {Color_Set: inventory_data, product_ids:product_ids,set_type_arr:set_type_arr},
							success: function (data) {
								data = JSON.parse(data);
								if(data.msg == 1) {
									alert("color set submitted successfully");
								} else {
									alert("Data inserted successfully! WholesaleBox team will validate this data");
								}

							}
						});
					}
	
			}
		}
			
			$('.colors_description').each(function(){
				if($(this).val().trim() == '' || $(this).hasClass('error')){
					error++;
				} else{
					error--;
					}
				});

			
			  
			
			function autocomplete_element2(value, options) {	
										
			var cm = jQuery("#colorgrid").jqGrid("getGridParam", "colModel");	
			var id = cm[0]['SKU Code'];
			// create input element
			var $ac = jQuery('<input type="text"  required="required" />');
			// setting value to the one passed from jqGrid
			var a = $ac.val(value);
			var colors = [];
			colors = ['yellow', 'green', 'red', 'blue'];
			$ac.autocomplete( {
			source: colors
			});
			return $ac.get(0); 
			}
								
			function autocomplete_value2(elem, op, v) {
						
				var a = jQuery(elem).val();
				//alert(a);
				if (op == 'set') {
					jQuery(elem).val(v);
				}
				return jQuery(elem).val();	
			}
			
			//creating grid for freesize
			var createFreeGrid = function(free) {
				var grid = jQuery('#freegrid');				
				var flag_error = 0;
				var data = [];
				for ( var i = 0; i < free.length;  i++ ) {
					data[i] = []; 
				}
				for(var i =0 ;i < free.length; i++) {
				
					data[i]['SKU Code'] = free[i];
					data[i]['Number of Pieces in Set'] = '';
					data[i]['Quantity'] = '';

				}
				
				var lastsel2;
				
				jQuery('#freegrid').jqGrid({
					datatype: "local",
					data: data,
				//	pager: true,
					editurl: 'clientArray',	
					loadonce: true,	
					rownumbers : true,
					cellLayout:30,
				//	forceFit : true,
					rowNum: 9999,
				//	gridview: true,
					align : 'center',
					cellEdit: true,
				//	rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					cellsubmit: 'clientArray',
					align : "center",
				//	pgbuttons: false, 
				//	pgtext: null,
					/*ondblClickRow: function(id) {
					  if (id) {
						$('#colorgrid').jqGrid('restoreRow', lastsel2);
						$('#colorgrid').jqGrid('editRow', id, true);
						lastsel2 = id;
						}
					},*/
					colModel: [
					{name:'SKU Code',index:'SKU Code',align : "center"},
					
					{name:'Pieces in Free Size Set',index:'Pieces in Free Size Set', editable: true, edittype:'text', align : "center",
						editrules: { required: true, number : true}
					},
					{name:'Free Size quantity',index:'Free Size quantity', editable: true, edittype:'text', align : "center",
						editrules: { required: true, number : true}
					},
				 ],
				 rownumbers : true,
				 caption : 'Free Size Set Description',
				}).jqGrid('navGrid', '#pagerfree');	
			};
			
			jQuery('#free').on('click',function() {
					var rowdata = [];
					set_count = 'F';

					//getting data from colorgrid
					rowdata = $('#freegrid').jqGrid('getRowData');
					
					var bool = checkEmptyCell(rowdata,0); //checking for empty cells
					
					//checking for errors as well as empty cells in size grid
					if(bool[0] == 1) {
						
						alert("Empty Fields not allowed");
						
					} else if(bool[0] != 1){
						submit_no_error++;
						//hiding submit button to avoid any changes to the submitted data
						jQuery('#free').hide();
						
						//unload the color grid
						jQuery("#freegrid").jqGrid('GridUnload');
						
						var inventory_data = $('#grid').jqGrid('getRowData');
						
						if(set_count== 'F' && submit_no_error == 1) {
							for(var i =0; i < inventory_data.length; i++) {
								inventory_data[i]['Sizes in Size Set'] = '';
								inventory_data[i]['Pieces in Size Set'] = '';
								inventory_data[i]['Color Set quantity'] = '';
								inventory_data[i]['Color Description'] = '';
								inventory_data[i]['Color Set Sizes'] = ''; 
								inventory_data[i]['Pieces in Free Size Set'] = '';	
								inventory_data[i]['Free Size quantity'] = '';	
								inventory_data[i]['Pieces in Set'] = '';	
								inventory_data[i]['Quantity'] = '';	

							}
						}
						
						for(var i = 0; i < rowdata.length; i++) {
							rowdata[i]['Sizes in Size Set'] = 'NA';
							rowdata[i]['Pieces in Size Set'] = 'NA';
							rowdata[i]['Color Set quantity'] = 'NA';
							rowdata[i]['Color Set Sizes'] = 'NA';
							rowdata[i]['Color Description'] = 'NA';
							rowdata[i]['Pieces in Set'] = 'NA';	
							rowdata[i]['Quantity'] = 'NA';	
						}
						
						var k = 0;
						var j = 0;
						
						for(var i =0 ;i<inventory_data.length; i++) {
							if(rowdata[j]['SKU Code'] == inventory_data[i]['SKU Code']) {
								inventory_data[i]['Sizes in Size Set'] = 'NA';
								inventory_data[i]['Color Set quantity'] = 'NA';
								inventory_data[i]['Color Set Sizes'] = 'NA';
								inventory_data[i]['Color Description'] = 'NA';
								inventory_data[i]['Pieces in Size Set'] = 'NA' 
								inventory_data[i]['Pieces in Set'] = 'NA';	
								inventory_data[i]['Quantity'] = 'NA';	
								inventory_data[i]['Pieces in Free Size Set'] = rowdata[j]['Pieces in Free Size Set']; 
								inventory_data[i]['Free Size quantity'] = rowdata[j]['Free Size quantity']; 

								j++;
								}
							if(j >= rowdata.length) {
								break;
							}
						}	
						var cm = jQuery("#grid").jqGrid("getGridParam", "colModel");		
						cm.shift();
					
						if(set_count== 'F' && submit_no_error == 1) {
							var i = 0;
							var sku_code = cm.shift();
							var set_type = cm.shift();
							cm.unshift({
								name : 'Color Set quantity',
								index : 'Color Set quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Color Set Sizes',
								index : 'Color Set Sizes',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Color Description',
								index : 'Color Description',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Sizes in Size Set',
								index : 'Sizes in Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Pieces in Size Set',
								index : 'Pieces in Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							
							cm.unshift({
								name : 'Pieces in Free Size Set',
								index : 'Pieces in Free Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							
							cm.unshift({
								name : 'Free Size quantity',
								index : 'Free Size quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							cm.unshift({
								name : 'Pieces in Set',
								index : 'Pieces in Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							
							cm.unshift({
								name : 'Quantity',
								index : 'Quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							cm.unshift(set_type);
							cm.unshift(sku_code);
							}
					
						jQuery("#grid").jqGrid('GridUnload');
						creatNewGrid(cm,inventory_data);
						which_set++;
						
						jQuery("#freeins").jqGrid('GridUnload');

						if(jQuery.isEmptyObject(notspecific) == false) {
							createnotspecificset(notspecific, which_set);
						} 
						//sending data via ajax and db insertion
						jQuery.ajax({
							url: "index.php?route=seller/seller_upload/getFreeSize",
							type: 'POST',
							datatype : 'json',		
							data: {Free_Size: inventory_data, product_ids:product_ids,set_type_arr:set_type_arr},
							success: function (data) {
								data = JSON.parse(data);
								if(data.msg == 1) {
									alert("Free Size Set submitted successfully");
								} else {
									
									alert("Data inserted successfully! WholesaleBox team will validate this data");

								}
							}
						});
						
					}
				});
				
			var createNotSpecificGrid = function(notspecific) {
				
				var grid = jQuery('#notspecificgrid');				
				var flag_error = 0;
				var data = [];
				for ( var i = 0; i < notspecific.length;  i++ ) {
					data[i] = []; 
				}
				for(var i =0 ;i < notspecific.length; i++) {
				
					data[i]['SKU Code'] = notspecific[i];
					data[i]['Number of Pieces in Set'] = '';
					data[i]['Quantity'] = '';

				}
				
				var lastsel2;
				
				jQuery('#notspecificgrid').jqGrid({
					datatype: "local",
					data: data,
				//	pager: true,
					editurl: 'clientArray',	
					loadonce: true,	
					rownumbers : true,
					cellLayout:30,
				//	forceFit : true,
					rowNum: 200,
				//	gridview: true,
					align : 'center',
					cellEdit: true,
				//	rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					cellsubmit: 'clientArray',
					align : "center",
				//	pgbuttons: false, 
				//	pgtext: null,
					/*ondblClickRow: function(id) {
					  if (id) {
						$('#colorgrid').jqGrid('restoreRow', lastsel2);
						$('#colorgrid').jqGrid('editRow', id, true);
						lastsel2 = id;
						}
					},*/
					colModel: [
					{name:'SKU Code',index:'SKU Code',align : "center"},
					
					{name:'Pieces in Set',index:'Pieces in Set', editable: true, edittype:'text', align : "center",
						editrules: { required: true, number : true}
					},
					{name:'Quantity',index:'Quantity', editable: true, edittype:'text', align : "center",
						editrules: { required: true, number : true}
					},
				 ],
				 rownumbers : true,
				 caption : 'Not Specific Set Description',
				}).jqGrid('navGrid', '#notspecificpager');	
				
			};
			
			jQuery('#notspecific').on('click',function() {
					var rowdata = [];
					set_count = 'N';

					//getting data from not specific
					rowdata = $('#notspecificgrid').jqGrid('getRowData');
					
					var bool = checkEmptyCell(rowdata,0); //checking for empty cells
				
					//checking for errors as well as empty cells in size grid
					if(bool[0] == 1) {
						
						alert("Empty Fields not allowed");
						
					} else if(bool[0] != 1){
						submit_no_error++;
						//hiding submit button to avoid any changes to the submitted data
						jQuery('#notspecific').hide();
						
						//unload the color grid
						jQuery("#notspecificgrid").jqGrid('GridUnload');
						
						var inventory_data = $('#grid').jqGrid('getRowData');
						
						if(set_count== 'N' && submit_no_error == 1) {
							for(var i =0; i < inventory_data.length; i++) {
								inventory_data[i]['Sizes in Size Set'] = '';
								inventory_data[i]['Pieces in Size Set'] = '';
								inventory_data[i]['Color Set quantity'] = '';
								inventory_data[i]['Color Description'] = '';
								inventory_data[i]['Color Set Sizes'] = ''; 
								inventory_data[i]['Pieces in Free Size Set'] = '';	
								inventory_data[i]['Free Size quantity'] = '';	
								inventory_data[i]['Pieces in Set'] = '';	
								inventory_data[i]['Quantity'] = '';	

							}
						}
						
						for(var i = 0; i < rowdata.length; i++) {
							rowdata[i]['Sizes in Size Set'] = 'NA';
							rowdata[i]['Pieces in Size Set'] = 'NA';
							rowdata[i]['Color Set quantity'] = 'NA';
							rowdata[i]['Color Set Sizes'] = 'NA';
							rowdata[i]['Color Description'] = 'NA';
							rowdata[i]['Pieces in Free Size Set'] = 'NA';	
							rowdata[i]['Free Size quantity'] = 'NA';
						}
						
						var k = 0;
						var j = 0;
						
						for(var i =0 ;i<inventory_data.length; i++) {
							if(rowdata[j]['SKU Code'] == inventory_data[i]['SKU Code']) {
								inventory_data[i]['Sizes in Size Set'] = 'NA';
								inventory_data[i]['Color Set quantity'] = 'NA';
								inventory_data[i]['Color Set Sizes'] = 'NA';
								inventory_data[i]['Color Description'] = 'NA';
								inventory_data[i]['Pieces in Size Set'] = 'NA' 
								inventory_data[i]['Pieces in Free Size Set'] = 'NA'; 
								inventory_data[i]['Free Size quantity'] = 'NA'; 
								inventory_data[i]['Pieces in Set'] = rowdata[j]['Pieces in Set']; 
								inventory_data[i]['Quantity'] = rowdata[j]['Quantity']; 
								j++;
								}
							if(j >= rowdata.length) {
								break;
							}
						}	
						var cm = jQuery("#grid").jqGrid("getGridParam", "colModel");		
						cm.shift();
					
						if(set_count== 'N' && submit_no_error == 1) {
							var i = 0;
							var sku_code = cm.shift();
							var set_type = cm.shift();
							cm.unshift({
								name : 'Color Set quantity',
								index : 'Color Set quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Color Set Sizes',
								index : 'Color Set Sizes',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Color Description',
								index : 'Color Description',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Sizes in Size Set',
								index : 'Sizes in Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							cm.unshift({
								name : 'Pieces in Size Set',
								index : 'Pieces in Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});
							
							cm.unshift({
								name : 'Pieces in Free Size Set',
								index : 'Pieces in Free Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							
							cm.unshift({
								name : 'Free Size quantity',
								index : 'Free Size quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							cm.unshift({
								name : 'Pieces in Free Size Set',
								index : 'Pieces in Free Size Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							
							cm.unshift({
								name : 'Free Size quantity',
								index : 'Free Size quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							cm.unshift({
								name : 'Pieces in Set',
								index : 'Pieces in Set',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							
							cm.unshift({
								name : 'Quantity',
								index : 'Quantity',
								editable: true,
								editrules: { required: true },
								align : "center",
							});	
							cm.unshift(set_type);
							cm.unshift(sku_code);
							}
					
						jQuery("#grid").jqGrid('GridUnload');
						creatNewGrid(cm,inventory_data);
						which_set++;
						jQuery("#freeins").jqGrid('GridUnload');
						//sending data via ajax and db insertion
						jQuery.ajax({
							url: "index.php?route=seller/seller_upload/getNotSpecificSet",
							type: 'POST',
							datatype : 'json',		
							data: {not_specific_data: inventory_data, product_ids:product_ids,set_type_arr:set_type_arr},
							success: function (data) {
								data = JSON.parse(data);
								if(data.msg == 1) {
									alert("Data inserted successfully! WholesaleBox team will validate this data");
								}
							}
						});
						
					}
				});
				
			var size = [];
			var color = [];
			var free = [];
			var notspecific = [];
			//creating grid having size/color options respectively 
			var creategrid4 = function (rowdataold) {
				
				var sku_arr = getSkuMethod(rowdataold);
				for(var i = 0; i < rowdataold.length; i++) {
					for (var key in rowdataold[i]) {
						if (rowdataold[i].hasOwnProperty(key)) {
							if(key == 'Set Type') {
								if(states[rowdataold[i][key]] == "Color Set") {
									color.push(sku_arr[i]);
								} else if(states[rowdataold[i][key]] == "Size Set") {
									size.push(sku_arr[i]);
								} else if(states[rowdataold[i][key]] == "Free Size") {
									free.push(sku_arr[i]);
								}  else if(states[rowdataold[i][key]] == "Not Specific") {
									notspecific.push(sku_arr[i]);
								}
							}
						}
					}
				}
			
			if (jQuery.isEmptyObject(color) == false && which_set == 0) {
					
					//createInstructionColorGrid();
					createColorGrid(color);
					jQuery('#color').show();
			} else if(jQuery.isEmptyObject(color) != false && jQuery.isEmptyObject(size) == false && which_set == 0) {
					
					//createInstructionSizeGrid();
					createSizeGrid(size);
					jQuery('#size').show();
			} else if(jQuery.isEmptyObject(color) != false && jQuery.isEmptyObject(size) != false && jQuery.isEmptyObject(free) == false && which_set == 0 ) {
					
					//createInstructionFreeGrid();
					createFreeGrid(free);
					$('#free').show();
			} else if(jQuery.isEmptyObject(color) != false && jQuery.isEmptyObject(size) != false  && jQuery.isEmptyObject(free) != false && which_set == 0 ) {
					
					//createInstructionFreeGrid();
					createNotSpecificGrid(notspecific);
					$('#notspecific').show();
					
			}
			};
			
			var createsizeset = function(size,which_set) {
				jQuery("#colorins").jqGrid('GridUnload');
				$('#fourthdiv').remove();
				if(jQuery.isEmptyObject(size) == false && which_set == 1) {
					//createInstructionSizeGrid();
					createSizeGrid(size);
					jQuery('#size').show();
				}
			};
			
			var createfreeset = function(free,which_set) {
				jQuery("#sizeins").jqGrid('GridUnload');
				//$('#fifthdiv').remove();
				if(jQuery.isEmptyObject(free) == false) {
					//createInstructionFreeGrid();
					createFreeGrid(free);
					jQuery('#free').show();
				}
			};
			
			var createnotspecificset = function(notspecific, which_set) {
				$('#fifthdiv').remove();
				if(jQuery.isEmptyObject(notspecific) == false) {
					//createInstructionFreeGrid();
					createNotSpecificGrid(notspecific);
					jQuery('#notspecific').show();
				}
			};

			var createGrid3 = function (sku_arr) {
				var grid = $('#grid3');
				var grid3_data = [];
				var row = row_count-3;
				
					for(var i = 0;i<sku_arr.length;i++) {
						grid3_data[i] = [];
				}
				
				for(var i = 0;i<sku_arr.length;i++) {

					grid3_data[i]['SKU Code'] = sku_arr[i];
					grid3_data[i]['Set Type'] = '';
					grid3_data[i]['Set Description'] = '';
					grid3_data[i]['No of Pieces in Each Set'] = '';
				}
				
				jQuery('#grid3').jqGrid({
				data: grid3_data,
				datatype: "local",
				colModel: [
                    {
                        name: 'SKU Code',
                    	align: "center",
                    },
                    {
                        name: 'Set Type', editable: true, formatter: 'select', editrules: { required: true },
                        stype:'select', edittype: 'select', editoptions: { value: states },
                        searchoptions: { value: states }, 
                        align: "center",
						editoptions : {
							value: states,
							dataEvents: [ {
								type: 'change',
                                fn: function(e) {
                                        var v = parseInt($(e.target).val(), 10);
                                        var newOptions = '';
									}
									}
								]	
							},
						},
                    ],
					rownumbers : true,
					//pager: true,
					onSelectRow: function (id) {
						if (id && id !== lastSel) {
							if (lastSel != -1) {
								//grid.setColProp('State', { editoptions: { value: states} });
							   // resetStatesValues();
							   // grid.restoreRow(lastSel);
							}
							lastSel = id;
						}
					},
					ondblClickRow: function (id, ri, ci) {
						if (id && id !== lastSel) {
							grid.restoreRow(lastSel);
							lastSel = id;
						}                   
						grid.editRow(id, true, null, null, 'clientArray', null,
									 function (rowid, response) {  // aftersavefunc
										 grid.setColProp('Set Type', { editoptions: { value: states} });
									 });
						return;
					},
					editurl: 'clientArray',
					rowNum: 9999,
				//	gridview: true,
				//	rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					align: "center",
					ignoreCase:true,              
					rownumbers: true,
					key : true,
				//    pgbuttons: false, 
				//	pgtext: null,
					caption : 'Set Description Grid',
				}).jqGrid('navGrid', '#pager3');			
				
			};
			
            var creatNewGrid = function(colmodels,grid_data) {
				
				colmodels.unshift( 
					{name:'actions',index:'actions',width:55,align:'center',sortable:false,formatter:'actions',
                     formatoptions:{
                         keys: true, // we want use [Enter] key to save the row and [Esc] to cancel editing.
                         onEdit:function(rowid) {
                         },
                         onSuccess:function(jqXHR) {
                             // we can verify the server response and interpret it do as an error
                             // in the case we should return false. In the case onError will be called
                             return true;
                         },
                         onError:function(rowid, jqXHR, textStatus) {
                          
                         },
                         afterSave:function(rowid) {
                         },
                         afterRestore:function(rowid) {
                         },
                        delOptions: {
							url: 'index.php?route=seller/seller_upload/getDelete',
							mtype: "POST", 
							keys: true
					}
                  }}
                );
               
               console.log(colmodels);
                
               jQuery("#grid").jqGrid({
				    data: grid_data,
					datatype: "local",
					colModel: colmodels,	
					//editurl: 'clientArray',
					//editable  : true,
				
					/*ondblClickRow: function(id) {
					  if (id) {
						$('#sizegrid').jqGrid('restoreRow', lastsel2);
						$('#sizegrid').jqGrid('editRow', id, true);
						lastsel2 = id;
						}
						},*/			
					loadonce: true,
					//'cellsubmit' : 'clientArray',
					//editurl: 'clientArray',
					align: "center",
					caption : 'Bulk Inventory Listing',	
					rownumbers : true,
				//	rowNum : -1,
					rowNum: 120,
					gridview: true,
					editrules: { required: true },
					gridComplete: function () {
					var ids = [];
					ids = jQuery("#grid").jqGrid('getDataIDs');
					var items = [];
					for(var variable=0;variable<ids.length;variable++) {
						var rowId = ids[variable];	
						jQuery("#" + rowId, "#grid").css("color", "green");
						jQuery("#" + rowId, "#grid").css("font-weight:bold; ");
						//$("#" + rowId, "#grid").css("background", "yelow");
						//jQuery("#grid").setCell (rowId,'Tax','',{background:'#ff0000'});
					}
					//$(".hid").css('display','none');
				},				
                }).jqGrid('navGrid', '#pager');
            };
            
            var creatNewGrid2 = function(colmodels,grid_data) {
				
               jQuery("#grid2").jqGrid({
					datatype: "local",
					data: grid_data,
					cellLayout:30,
					editurl: 'clientArray',
					ondblClickRow: function(id) {
					  if (id) {
					  	$('#grid2').jqGrid('restoreRow', lastsel2);
					  	$('#grid2').jqGrid('editRow', id, true);
					  	lastsel2 = id;
					  }
					},					
					loadonce: true,	
					caption : 'Filter Group',
					rowNum: 9999,
					//gridview: true,
					colModel:colmodels,
					align : "center",
					editrules: { required: true },
					//rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
				//	pgbuttons: false, 
				//	pgtext: null,  
					 
                }).jqGrid('navGrid', '#pager2', {add:false,edit:false,del:false})
            
            };
            
            var creatNewGridnonmandatory = function(colmodels,grid_data) {
				
               jQuery("#gridnonman").jqGrid({
					datatype: "local",
					data: grid_data,
					cellLayout:30,
					editurl: 'clientArray',
					ondblClickRow: function(id) {
					  if (id) {
					  	$('#gridnonman').jqGrid('restoreRow', lastsel2);
					  	$('#gridnonman').jqGrid('editRow', id, true);
					  	lastsel2 = id;
					  }
					},					
					loadonce: true,	
					caption : 'Non Mandatory Filter Group',
					rowNum: 9999,
					//gridview: true,
					colModel:colmodels,
					align : "center",
					editrules: { required: true },
					//rowNum:-1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
				//	pgbuttons: false, 
				//	pgtext: null,  
					 
                }).jqGrid('navGrid', '#pagernonman', {add:false,edit:false,del:false})
            
            };
            
            var creatNonEditableGrid = function(colmodels,grid_data) {
				colmodels.unshift( 
					{name:'actions',index:'actions',width:55,align:'center',sortable:false,formatter:'actions',
                     formatoptions:{
                         keys: true, // we want use [Enter] key to save the row and [Esc] to cancel editing.
                         onEdit:function(rowid) {
                           /*  alert("in onEdit: rowid="+rowid+"\nWe don't need return anything");*/
                         },
                         onSuccess:function(jqXHR) {
                             // the function will be used as "succesfunc" parameter of editRow function
                             // (see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing#editrow)
                            /* alert("in onSuccess used only for remote editing:"+
                                   "\nresponseText="+jqXHR.responseText+
                                   "\n\nWe can verify the server response and return false in case of"+
                                   " error response. return true confirm that the response is successful");*/
                             // we can verify the server response and interpret it do as an error
                             // in the case we should return false. In the case onError will be called
                             return true;
                         },
                         onError:function(rowid, jqXHR, textStatus) {
                             // the function will be used as "errorfunc" parameter of editRow function
                             // (see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing#editrow)
                             // and saveRow function
                             // (see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:inline_editing#saverow)
                            /* alert("in onError used only for remote editing:"+
                                   "\nresponseText="+jqXHR.responseText+
                                   "\nstatus="+jqXHR.status+
                                   "\nstatusText"+jqXHR.statusText+
                                   "\n\nWe don't need return anything");*/
                         },
                         afterSave:function(rowid) {
                           /*  alert("in afterSave (Submit): rowid="+rowid+"\nWe don't need return anything");*/
                         },
                         afterRestore:function(rowid) {
                            /* alert("in afterRestore (Cancel): rowid="+rowid+"\nWe don't need return anything");*/
                         },
                        delOptions: {
							url: 'index.php?route=seller/seller_upload/getDelete',
							mtype: "POST", 
							keys: true
						}
                     }}
                   );
               
               jQuery("#grid").jqGrid({
					datatype: "local",
					data: grid_data,
					cellLayout:50,
					//editurl: 'clientArray',
					//pager: true,		
					//loadonce: true,	
					caption : 'Bulk Inventory Listing',
					colModel:colmodels,
					align : "center",
					rownumbers : true,
					//rowNum: -1,   // this loads all the records
					//cmTemplate: { autoResizable: true },
					//autoResizing: { compact: true },
				   //pgbuttons: false, 
				   //pgtext: null,
					rowNum: 9999,
					datatype: "local",
					cellLayout:30,
					editurl: 'clientArray',		
					loadonce: true,	
					rowNum: 9999,
					align : "center",
					editrules: { required: true },
					gridComplete: function () {
					var ids = [];
					ids = jQuery("#grid").jqGrid('getDataIDs');
					var items = [];
					for(var variable=0;variable<ids.length;variable++) {
						var rowId = ids[variable];	
						jQuery("#" + rowId, "#grid").css("color", "green");
						jQuery("#" + rowId, "#grid").css("font-weight:bold; ");
					}
				},
                }).jqGrid('navGrid','#pager',{add:false,edit:false},{multipleSearch:true,overlay:false});                
            };
            
            var createInstructGird = function() {				

				var data = [{'Instructions':"This page is helpful to select filters corresponding to SKU code"}, 
							{'Instructions':"As soon as user clicks on filter group button, a grid appears with SKU Codes and Filters"},
							{'Instructions' :"In order to enter value, user must double click on the empty cell"},
							{'Instructions' : 'Once user will start typing, a list of suggestions corresponding to the typed values will be shown'},
							{'Instructions' : 'To submit the typed value in cell, user must click enter'},
							{'Instructions' : "The table can be submitted by clicking on the submit button"},
							];
				
				 jQuery("#gridinstruct1").jqGrid({
					data : data,
					datatype: "local",
					rowNum: 9999,	
					colModel: [
					{
						name: 'Instructions',
						index: 'Instructions',
						width : 300,
					},
					],
					data: data,
					caption : 'Instructions Table',
					align : "center",
					gridComplete: function () {
					var ids = [];
					ids = jQuery("#gridinstruct1").jqGrid('getDataIDs');
					var items = [];
					for(var variable=0;variable<ids.length;variable++) {
						var rowId = ids[variable];	
						jQuery("#" + rowId, "#gridinstruct1").css("color", "#120802");
						jQuery("#" + rowId, "#gridinstruct1").css("font-weight:bold; ");
					}
				},
                }).jqGrid('navGrid', '#instructp1', {add:false,edit:false,del:false});
                
			};
            var createInstructGird2 = function() {
				var data = [{'Instructions':"This page is helpful to select set type corresponding to SKU code"}, 
							{'Instructions':"Size Set: Different sizes for same design in a set"},
							{'Instructions':"Color Set: Different colors in a set, and size is same for all"},
							{'Instructions' :"In order to enter value, user must double click on the empty cell and select from the dropdown options"},
						//	{'Instructions' : 'Once user will start typing, a list of suggestions corresponding to the typed values will be shown'},
							{'Instructions' : 'To submit the typed value in cell, user must click enter'},
							{'Instructions' : "The table can be submitted by clicking on the submit button"},
							];
				
				 jQuery("#gridinstruct2").jqGrid({
					data : data,
					datatype: "local",
					colModel: [
					
					{
						name: 'Instructions',
						index: 'Instructions',
						width : 300,
					},
					
					],
				//	pgbuttons: false, 
				//	pgtext: null,
					data: data,
					cellLayout:30,
				//	pager: true,		
					caption : 'Instructions Table',
					align : "center",
				//	rowNum: -1,   // this loads all the records
				//	cmTemplate: { autoResizable: true },
				//	autoResizing: { compact: true },
					gridComplete: function () {
					var ids = [];
					ids = jQuery("#gridinstruct2").jqGrid('getDataIDs');
					var items = [];
					for(var variable=0;variable<ids.length;variable++) {
						var rowId = ids[variable];	
						jQuery("#" + rowId, "#gridinstruct2").css("color", "#120802");
						jQuery("#" + rowId, "#gridinstruct2").css("font-weight:bold; ");
					}
					//$(".hid").css('display','none');
				},
				
                }).jqGrid('navGrid', '#instructp2', {add:false,edit:false,del:false});
                
			};
            var removeByAttr = function(arr, attr, value){
				var i = arr.length;
				while(i--){
					if( arr[i] 
						&& arr[i].hasOwnProperty(attr) 
						&& (arguments.length > 2 && arr[i][attr] === value ) ){ 
						arr.splice(i,1);
						}
					}
				return arr;
			};
			
			var getSkuMethod = function(grid_data) {
				var sku_arr = [];
				for(var i = 0; i < grid_data.length; i++) {
					sku_arr.push(grid_data[i]['SKU Code']);
				}
				
				return sku_arr;
			};
			
			var getIdsFilter = function(rowdata,text) {
				
				var filters_id_array = [];
				for(var i = 0; i < rowdata.length; i++) {
					var index = filters_name2.indexOf(rowdata[i][text]);
					var filter_id = filters_id[index];
					filters_id_array.push(filter_id);
				}
				return filters_id_array;
			};
			
			var getIdsFilterNonMandatory = function(rowdata,text) {
				
				var non_mandatory_filters_id_array = [];
				
				for(var i = 0; i < rowdata.length; i++) {
					var index = non_mandatory_filters_name2.indexOf(rowdata[i][text]);
					var filter_id = non_mandatory_filters_id[index];
					non_mandatory_filters_id_array.push(filter_id);
				}
				
				return non_mandatory_filters_id_array;
			};
			
			var checkEmptyCell = function(rowdata,number) {
				var tmp = 0;
				var id = [];
				var column = [];
				for(var i =0; i < rowdata.length; i++) {
					for (var key in rowdata[i]) {	
						if (rowdata[i].hasOwnProperty(key)) {
							if((rowdata[i][key]).match(/<button class="btn btn-default" title="click to delete" "="">Delete SKU/g)) {
								tmp = 0;
							} if(number == 2) {
								 if((rowdata[i][key]).match(/<span class="editable">/g) || (rowdata[i][key]).match(/<input/g) || (rowdata[i][key]).match(/<select role="select"/g)) {
									 tmp = 1;
								 } else {
									 tmp = 0;
								 }

							} else if (number == 1) {
								
								if((rowdata[i][key]).match(/<span class="editable">/g) || (rowdata[i][key]).match(/<input/g) || (rowdata[i][key]).match(/<select role="select"/g)) {
									tmp = 1;
									break;
								} else {
									tmp = 0;
								}
	
								
							} else {
								
								if((rowdata[i][key] == ""  || (rowdata[i][key]).match(/<span class="editable">/g) || (rowdata[i][key]).match(/<input/g) || (rowdata[i][key]).match(/<select role="select"/g))) {
									tmp = 1;
									break;
								} else {
									tmp = 0;
								}
									
							}
						}
					}
					if(tmp == 1) {
						break;
					}
				}
				
				for(var i =0; i < rowdata.length; i++) {
					for (var key in rowdata[i]) {	
						if (rowdata[i].hasOwnProperty(key)) {
							if(rowdata[i][key] == "") {
								id.push(i);
								column.push(key);
							}
						}
					}
				}
			
				return [tmp,id,column];
			};

			for(var i = 0; i < headers.length; i++) {
				var prop = headers[i];
				if (headers.hasOwnProperty(i)) {
					listOfColumnNames.push(prop);
					var columnWidth = (prop == "Any additional Comments") ? 380 : 180;
					
					//pushing colmodel values						
					listOfColumnModels.push({
					name: prop,
					editable : true,
					cellattr: function (rowId, val, item) {
						var $grid = jQuery("#grid");
						var i =0;
						for(var i = 2; i <= row_count-2; i=i+2) {
							if(rowId == "jqg"+i) {
								if (val !== "") {
								} 
							} 	 	
						}
					},
					sortable: false,
					width: columnWidth,
					align: "center",
					cellLayout:30,                
					edittype:'text',
					loadonce: true,
					caption : 'Filter Group',
				//	pager: true,
					sortable: false,
					width: columnWidth,
					align: "center",
					cellLayout:30,                
					rownumbers: true,
					edittype:'text',
					loadonce: true,
					//caption : 'Filter Group',
					//pager: true,
					
					});
				}
			}
			
						
			listOfColumnModels.unshift({
				name : 'actions',
				index: 'actions', 
				formatter: button_add_formatter,
				/*	formatoptions: {
					keys: true,
					editbutton: false,
					deletebutton: false,
					delOptions: {
						url: 'index.php?route=seller/seller_upload/getDelete',
						mtype: "POST", 
						keys: true
						}
					  },*/
				   sortable : false,	   
				});
				
			function button_add_formatter (cellvalue, options, rowObject) {
				var number = options['rowId'].substr(options['rowId'].length - 1) - 1;
				if(number % 2 == 0) {
					return '<button name = "delete" class="btn btn-danger btn-sm" title="click to delete"">delete</button> <button id = "id_'+ number + '" name = "edit" title = "click to edit" class="btn btn-default btn-sm edit_'+ number + '">edit</button>';
				}

			<!--	return '<button id = "id_'+ number + '" name = "delete" class="btn btn-default btn-sm used_button'+ number + '" title="click to delete"">delete</button> <button id = "id_'+ number + '" name = "delete" class="btn btn-default btn-sm edit_'+ number + '">edit</button>';--> 
			<!--	return '<button class="btn btn-default" title="click to delete"">Delete SKU</button>'; -->
			}
			
			var mydata = data,					                  
			$grid = jQuery("#grid");
			$grid.jqGrid({
				data: mydata,
				datatype: "local",
				colModel: listOfColumnModels,
				ondblClickRow: function(id) {
					  if (id) {
						var regex = /(\d+)/g;
						var number = id.match(regex);
						if(number %2 !== 0){
							jQuery('#grid').jqGrid('restoreRow', lastsel2);
							jQuery('#grid').jqGrid('editRow', id, true);
							lastsel2 = id;
							}
						}
					},	
				rowNum: 200,
				gridComplete: function () {
					var ids = [];
					ids = jQuery("#grid").jqGrid('getDataIDs');
					
					var items = [];
					var count_btn = 0;
					for(var variable=0;variable<ids.length;variable++) {
						var rowId = ids[variable];	
						var rowData = jQuery('#grid').jqGrid ('getRowData', rowId);
						var a = jQuery('#grid').jqGrid('getCell',rowId, 'SKU Code'); 
						var b = jQuery('#grid').jqGrid('getCell',rowId, 'Pieces in Set'); 
						var c = jQuery('#grid').jqGrid('getCell',rowId, 'Transfer Price'); 
						var d = jQuery('#grid').jqGrid('getCell',rowId, 'AvailableSets'); 
						var e = jQuery('#grid').jqGrid('getCell',rowId, 'Weight of a Piece (in gm)'); 
						var f = jQuery('#grid').jqGrid('getCell',rowId, 'Size Set / Color Set'); 
						var g = jQuery('#grid').jqGrid('getCell',rowId, 'Size'); 
						var h = jQuery('#grid').jqGrid('getCell',rowId, 'Set Description'); 
						var i = jQuery('#grid').jqGrid('getCell',rowId, 'Any additional Comments'); 
						var j = jQuery('#grid').jqGrid('getCell',rowId, 'Tax');

						if(variable%2 !=0 ) {
							jQuery("#grid").find('actions').addClass('classname');
							jQuery('#grid').jqGrid('setCell', rowId,  'actions', '', 'classname');
							$('.classname').html('');
						}
						
						if(variable%2 == 0) {
							jQuery("#grid").find('actions').addClass('used_button'+count_btn);
							jQuery('#grid').jqGrid('setCell', rowId,  'actions', '', 'used_button'+count_btn);
							$('.used_button'+count_btn).attr("id","id_"+count_btn);
							count_btn++;
						} 
						
						
						if(variable%2 !=0 && a!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'SKU Code', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'SKU Code', '', {'color':'red'});
							
						} if(variable%2 !=0 && b!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Pieces in Set', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Pieces in Set', '', {'color':'red'});
							
						} if(variable%2 !=0 && c!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Transfer Price', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Transfer Price', '', {'color':'red'});
							
						} if(variable%2 !=0 && d!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'AvailableSets', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'AvailableSets', '', {'color':'red'});
							
						} if(variable%2 !=0 && e!= "" && e != "Please recheck this weight") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'color':'red'});
							
						} if(variable%2 !=0 && i!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Any additional Comments', '', {'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Any additional Comments', '', {'color':'red'});
							
						}  if(variable%2 !=0 && j!= "") {
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Tax', '',{'background-color' :'#FFCCCC'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Tax', '', {'color':'red'});
							
						} if(variable%2 !=0  && e == "Please recheck the weight") {
							warnings = 1;
							var rowId_previous_row = ids[variable-1];
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'background-color' :'rgb(234, 215, 86)'});
							jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'color':'black'});
						}
						
						if(a == "" && b == "" && c == "" && d == "" && e == "" && f == "" && g == "" && h == "" && i =="" && j == "") {
							jQuery("#" + rowId, "#grid").hide();
						} else if(variable%2 == 0 ){
							jQuery("#" + rowId, "#grid").css("color", "green");
						} else if(variable %2 != 0) {
							jQuery("#" + rowId, "#grid").css("color", "#e50000");
						}
					}
					jQuery(".hid").css('display','none');
				},				
				iconSet: "fontAwesome",
				autoencode: true,
				caption: "Bulk Inventory Listing Grid",

				}).jqGrid("navGrid", {add: false, search: false, del : false, refresh: false, edit : false});
				
				
				function reindex_array_keys(array, start){
					var temp = [];
					start = typeof start == 'undefined' ? 0 : start;
					start = typeof start != 'number' ? 0 : start;
					for(var i in array){
						temp[start++] = array[i];
					}
					return temp;
				}
				
				var removed = [];
				//checking for which delete button has been clicked
				var ids = jQuery("#grid").jqGrid('getDataIDs');
				var grid_d = jQuery('#grid').jqGrid('getRowData');
				
				//delete in grid for first time
				for(var i = 0; i < ids.length; i++) {
					$('#id_'+i).on('click',function(event){
						if($(event.target).is('button[name="delete"]')){
							//alert('testsdsd');
							var id = $(this).prop("id");
							alert(id);
							var num = id.split('_')[1];
						//	alert(num);
							delete ids[2*num];
						//	alert("first");
							delete product_ids[num];
							delete ids[parseInt(2*num)+1];
							delete grid_d[2*num];
							delete grid_d[parseInt(2*num)+1];
							$('#'+parseInt(2*num)+1).hide();
							var a = parseInt(2*num)+1;
							$('#'+ a).hide();

							jQuery('#grid').jqGrid('GridUnload');

							refreshgrid(grid_d,listOfColumnModels);
						}
						
						if($(event.target).is('button[name="edit"]')) {							
							$( this ).trigger( "dblclick" );
						}
					});
					
				}
				
				var refreshgrid = function(grid_d,listOfColumnModels) {
					
					var data = reindex_array_keys(grid_d,0);
					product_ids = reindex_array_keys(product_ids,0);
					
					jQuery("#grid").jqGrid({
						datatype: "local",
						data: data,
						cellLayout:30,
						//editurl: 'clientArray',
						loadonce: true,	
						caption : 'Bulk Inventory Listing',
						colModel:listOfColumnModels,
						align : "center",
						rowNum: -1,   // this loads all the records
						cmTemplate: { autoResizable: true },
						autoResizing: { compact: true },
						pgbuttons: false, 
						pgtext: null,
						rowNum: 9999,
						gridview: true,
						ondblClickRow: function(id) {
						  if (id) {
							var regex = /(\d+)/g;
							var number = id.match(regex);
							if(number %2 !== 0){
								jQuery('#grid').jqGrid('restoreRow', lastsel2);
								jQuery('#grid').jqGrid('editRow', id, true);
								lastsel2 = id;
								}
							}
						},	
						gridComplete: function () {
						var ids = [];
						ids = jQuery("#grid").jqGrid('getDataIDs');
						
						var items = [];
						var count_btn = 0;
						for(var variable=0;variable<ids.length;variable++) {
							var rowId = ids[variable];	
							var rowData = jQuery('#grid').jqGrid ('getRowData', rowId);
							var a = jQuery('#grid').jqGrid('getCell',rowId, 'SKU Code'); 
							var b = jQuery('#grid').jqGrid('getCell',rowId, 'Pieces in Set'); 
							var c = jQuery('#grid').jqGrid('getCell',rowId, 'Transfer Price'); 
							var d = jQuery('#grid').jqGrid('getCell',rowId, 'AvailableSets'); 
							var e = jQuery('#grid').jqGrid('getCell',rowId, 'Weight of a Piece (in gm)'); 
							var f = jQuery('#grid').jqGrid('getCell',rowId, 'Size Set / Color Set'); 
							var g = jQuery('#grid').jqGrid('getCell',rowId, 'Size'); 
							var h = jQuery('#grid').jqGrid('getCell',rowId, 'Set Description'); 
							var i = jQuery('#grid').jqGrid('getCell',rowId, 'Any additional Comments'); 
							var j = jQuery('#grid').jqGrid('getCell',rowId, 'Tax');

							if(variable%2 !=0 ) {
								jQuery("#grid").find('actions').addClass('classname');
								jQuery('#grid').jqGrid('setCell', rowId,  'actions', '', 'classname');
								$('.classname').html('');
							}
							if(variable%2 == 0) {
								jQuery("#grid").find('actions').addClass('used_button'+count_btn);
								jQuery('#grid').jqGrid('setCell', rowId,  'actions', '', 'used_button'+count_btn);
								$('.used_button'+count_btn).attr("id","id_"+count_btn);
								count_btn++;
							}
							
							if(variable%2 !=0 && a!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'SKU Code', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'SKU Code', '', {'color':'red'});
								
							} if(variable%2 !=0 && b!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Pieces in Set', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Pieces in Set', '', {'color':'red'});
								
							} if(variable%2 !=0 && c!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Transfer Price', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Transfer Price', '', {'color':'red'});
								
							} if(variable%2 !=0 && d!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'AvailableSets', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'AvailableSets', '', {'color':'red'});
								
							} if(variable%2 !=0 && e!= "" && e != "Please recheck this weight") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'color':'red'});
								
							} if(variable%2 !=0 && i!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Any additional Comments', '', {'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Any additional Comments', '', {'color':'red'});
								
							}  if(variable%2 !=0 && j!= "") {
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Tax', '',{'background-color' :'#FFCCCC'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Tax', '', {'color':'red'});
								
							} if(variable%2 !=0  && e == "Please recheck the weight") {
								warnings = 1;
								var rowId_previous_row = ids[variable-1];
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'background-color' :'rgb(234, 215, 86)'});
								jQuery('#grid').jqGrid('setCell', rowId_previous_row,  'Weight of a Piece (in gm)', '', {'color':'black'});
							}
							
							if(a == "" && b == "" && c == "" && d == "" && e == "" && f == "" && g == "" && h == "" && i =="" && j == "") {
								jQuery("#" + rowId, "#grid").hide();
							} else if(variable%2 == 0 ){
								jQuery("#" + rowId, "#grid").css("color", "green");
							} else if(variable %2 != 0) {
								jQuery("#" + rowId, "#grid").css("color", "#e50000");
							}
						}
						jQuery(".hid").css('display','none');
				},
                }).jqGrid('navGrid', '#pager', {});
					//next time grid delte
                	var ids = jQuery("#grid").jqGrid('getDataIDs');
					var grid_d = jQuery('#grid').jqGrid('getRowData');
					for(var i = 0; i < ids.length; i++) {
					$('#id_'+i).on('click',function(){
						if($(event.target).is('button[name="delete"]')){
							var id = $(this).prop("id");
					//		alert(id);
							var num = id.split('_')[1];
							delete ids[2*num];
							delete product_ids[num];
							delete ids[parseInt(2*num)+1];
							delete grid_d[2*num];
							delete grid_d[parseInt(2*num)+1];
							$('#'+parseInt(2*num)+1).hide();
							var a = parseInt(2*num)+1;
							$('#'+ a).hide();
							jQuery('#grid').jqGrid('GridUnload');
							refreshgrid(grid_d,listOfColumnModels);
						} 
						
						if($(event.target).is('button[name="edit"]')) {
							$( this ).trigger( "dblclick" );

						}
					});
				}
				};
			
				var grid = [];   
				var send_grid_data = [];
				for ( var i = 0; i < row_count/2; i++ ) {
					grid[i] = []; 
				}
				
				var count = 0;	
				var created_set_type = 0;
				jQuery('#submit_button').on('click', function() {
					
				
					var rowids = [];
					var columns = [];
					
					var rdata = jQuery('#grid2').jqGrid('getRowData');
					
					var ids = jQuery("#grid2").jqGrid('getDataIDs');
					var bool = checkEmptyCell(rdata,0); //checking for empty cells
					
					var tmp = bool[0];
					var rowids = bool[1];
					var columns = bool[2];
					
					if(tmp == 1) {				
						alert("Empty Values not allowed");	
					} else {
						jQuery('#gridinstruct1').jqGrid('GridUnload');
						$('#submit_button').hide();
						$('#' + filtergroup[button_count]).attr("disabled",false);
						var rowdata = $('#grid2').jqGrid('getRowData');
						$("#grid2").jqGrid('GridUnload');
						var text = filter_groups.slice(-1).pop();	
						var filter_ids = getIdsFilter(rowdata,text);
						
						for(var i = 0; i < rowdata.length; i++) {
							grid_data_to_show[i][text]= rowdata[i][text];
						}
						
						$("#grid").jqGrid('GridUnload');
						
						if(listOfColumnModels[0]['name'] == 'actions') {
							listOfColumnModels.shift();
						}
						
						creatNewGrid(listOfColumnModels,grid_data_to_show);
						final_arr.push(filter_ids);
						final_groups.push(text);
						
						count++;
						if(count == filter_group_length) {
						
						//hiding submit button to avoid any changes to the submitted data
							jQuery('#submit_button').hide();
							jQuery('#gridinstruct1').jqGrid('GridUnload');
							jQuery( "#firstdiv_1" ).remove();
							$('#filters').remove();
							mandatory_filters = 1;
						//sending data via ajax and db insertion
							jQuery.ajax({
							url: "index.php?route=seller/seller_upload/filtersId",
							type: 'POST',
							datatype : 'json',		
							data: {filter_group: final_groups, filter_ids : final_arr,product_ids:product_ids},
							success: function (data) {
								alert("Filters Submitted Succesfully!");
							}
						});
						
						if(empty_nonmandatory == 1) {
							created_set_type = 1;
							jQuery('#submit_button_nonman').hide();
							jQuery('#gridinstruct1').jqGrid('GridUnload');
							jQuery( "#non_mandatory_div" ).remove();
							$('#filters_nonmandatory').remove();
							allGridData = $('#grid').jqGrid("getRowData");
							var sku_arr = getSkuMethod(allGridData);
							createGrid3(sku_arr);
							createInstructGird2();
							jQuery('#button3').show();
							
						} else {
							var length_non_mandatory_filter_group = non_mandatory_filter_groups.length;
							for(var j = button_count_non_mandatory+1; j < non_mandatory_filter_groups.length; j++) {
								jQuery('.add_' + j).attr("disabled", true);
							}
									
							for(var j = button_count_non_mandatory+1; j < non_mandatory_filter_groups.length; j++) {
								jQuery('.skip_' + j).attr("disabled", true);
							}
							
							for(var i = 0; i < non_mandatory_filter_groups.length; i++) {
								$('#skip_'+non_mandatory_filter_groups[i]).on('click',function() {	
									var id = (this).id;
									var num = id.split('_')[1];
									$('#tr_'+num).remove();
									var text = $('#td_' + num).html();
									added_filters_non_mandatory.push(num);
									
								//	alert(button_count_non_mandatory);
									button_count_non_mandatory++;
									jQuery('.add_' + button_count_non_mandatory).attr("disabled", false);
									jQuery('.skip_' + button_count_non_mandatory).attr("disabled", false);
								//	$('#add_' + non_mandatory_filter_groups[button_count_non_mandatory]).attr("disabled",false);
									
									var index = non_mandatory_filter_groups.indexOf(num);
									if (index > -1) {
											non_mandatory_filter_groups.splice(index, 1);
									}
									
									if(non_mandatory_filter_groups.length == 0 || length_non_mandatory_filter_group == added_filters_non_mandatory.length ) {
										created_set_type = 1;
										jQuery('#submit_button_nonman').hide();
										jQuery('#gridinstruct1').jqGrid('GridUnload');
										jQuery( "#non_mandatory_div" ).remove();
										$('#filters_nonmandatory').remove();
										alert("please fill select set type");
										allGridData = $('#grid').jqGrid("getRowData");
										var sku_arr = getSkuMethod(allGridData);
										createGrid3(sku_arr);
										createInstructGird2();
										jQuery('#button3').show();
									}
									
								});
								
								$('#add_'+non_mandatory_filter_groups[i]).unbind().on('click',function() {
									
									var id = this.id;
									
									var num = id.split('_')[1];
									var text = $('#td_' + num).html();
									
									button_count_non_mandatory++;
									jQuery('.add_' + button_count_non_mandatory).attr("disabled", false);
									jQuery('.skip_' + button_count_non_mandatory).attr("disabled", false);									
									jQuery('#tr_' + id.split('_')[1]).remove();
									added_filters_non_mandatory.push(num);
									

									
									$('#submit_button_nonman').show();
								
									filtergroup_nonmandatory.push(id);
									
									var grid = jQuery("#grid");
									var gridnonman = jQuery("#gridnonman");
									allGridParams = $grid.jqGrid("getGridParam");
									allGridData = $grid.jqGrid("getRowData");
									
									//removing errors row
									var col_model_data = [];
									for(var i = 0;i<(allGridData.length);i++) {
										col_model_data[i] = [];
									}
											
									//grid_data_to_show = deleteRow(grid_data, row_count);
									var sku_arr = getSkuMethod(allGridData);
									
									for(var i = 0;i<allGridData.length;i++) {
										col_model_data[i]['SKU Code'] = sku_arr[i];
										col_model_data[i][text] = '';
									}
									

									var col_model = [];
									col_model.push({
										name: 'SKU Code',
										index : num,
										align : "center",
										rownumbers : true,
										editrules: { required: true },
										
									});
											
									col_model.push({
										name: text,
										index : num,
										editable: true,
										edittype:'custom',
										classes:"autocomplete",
										cellLayout:30,                
										rownumbers: true,
										align : "center",
										sortable: false,
										editoptions:{'custom_element':autocomplete_element_nonmandatory,'custom_value':autocomplete_value_nonmandatory},
									});
											

									filtergroups_nonmandatory.push(text);
									allGridParams.colModel.shift();						
									var SKU_Code = allGridParams.colModel.shift();
									
									allGridParams.colModel.unshift({		
										name: text,
										index : id,
										align : "center",
										sortable: false,
									});
									
	
									allGridParams.colModel.unshift({		
										align:SKU_Code.align,
										caption:SKU_Code.caption,
										cellLayout:SKU_Code.cellLayout,
										cellattr:SKU_Code.cellattr,
										height:SKU_Code.height,
										loadonce:SKU_Code.loadonce,
										name:SKU_Code.name,
										pager:SKU_Code.pager,
										rownumbers:SKU_Code.rownumbers,
										viewrecords:SKU_Code.viewrecords,
										width:SKU_Code.width,
										sortable: false,

									});
											
											
									for(var i =0; i < allGridParams.length; i++) {
										allGridParams[i][text] = '';
									}
									
									creatNewGridnonmandatory(col_model,col_model_data);
										
									createInstructGird();
									
									function autocomplete_element_nonmandatory(value, options) {	

										var cm = jQuery("#gridnonman").jqGrid("getGridParam", "colModel");	
										var id = cm[0]['index'];
										// create input element
										var $ac = $('<input type="text"  required="required" />');
										// setting value to the one passed from jqGrid
										var a = $ac.val(value);
										var non_mandatory_filters_name = [];
										for(var i = 0; i < non_mandatory_filters_array[num].length; i++) {
											non_mandatory_filters_name.push(non_mandatory_filters_array[num][i].name);
											non_mandatory_filters_name2.push(non_mandatory_filters_array[num][i].name);
											non_mandatory_filters_id.push(non_mandatory_filters_array[id][i].filter_id);
										}
										
										$ac.autocomplete( {
											source: non_mandatory_filters_name
										});
										
										return $ac.get(0); 
									}
										
									function autocomplete_value_nonmandatory(elem, op, v) {
											
										var a = $(elem).val();
										if(non_mandatory_filters_name2.indexOf(a) == -1) {
											alert("entered value is not valid");
											return "";
										} else {
											if (op == 'set') {
												jQuery(elem).val(v);
											}
											return jQuery(elem).val();
										}
									}
							});
						}
							
						}

						}
					}	
				});
			
			var count_nonmandatory = 0;
			jQuery('#submit_button_nonman').on('click', function() {

				var rowids = [];
				var columns = [];
				
				var rdata = jQuery('#gridnonman').jqGrid('getRowData');
				var ids = jQuery("#gridnonman").jqGrid('getDataIDs');
				var bool = checkEmptyCell(rdata,2); //checking for empty cells
				var tmp = bool[0];
				var rowids = bool[1];
				var columns = bool[2];
					
				if(tmp == 1) {				
					alert("Empty Values not allowed");	
				} else {
					jQuery('#gridinstruct1').jqGrid('GridUnload');
					$('#submit_button_nonman').hide();
					$('#add_' + non_mandatory_filter_groups[button_count_non_mandatory]).attr("disabled",false);
					var rowdata = $('#gridnonman').jqGrid('getRowData');

					$("#gridnonman").jqGrid('GridUnload');
					
					var text = filtergroups_nonmandatory.slice(-1).pop();	
					var filter_ids = getIdsFilterNonMandatory(rowdata,text);
				
					for(var i = 0; i < rowdata.length; i++) {
						allGridData[i][text]= rowdata[i][text];
					}
					
					$("#grid").jqGrid('GridUnload');
						
					/*if(listOfColumnModels[0]['name'] == 'actions') {
						listOfColumnModels.shift();
					}*/
									
					creatNewGrid(allGridParams.colModel,allGridData);
					
					final_arr_non_mandatory.push(filter_ids);
					final_groups_nonmandatory.push(text);
						
					count_nonmandatory++;
					if(count_nonmandatory == non_mandatory_filter_groups.length) {
						
					//hiding submit button to avoid any changes to the submitted data
						jQuery('#submit_button_nonman').hide();
						jQuery('#gridinstruct1').jqGrid('GridUnload');
						jQuery( "#non_mandatory_div" ).remove();
						$('#filters_nonmandatory').remove();

						//sending data via ajax and db insertion
						jQuery.ajax({
							url: "index.php?route=seller/seller_upload/filtersId",
							type: 'POST',
							datatype : 'json',		
							data: {filter_group: final_groups_nonmandatory, filter_ids : final_arr_non_mandatory,product_ids:product_ids},
							success: function (data) {
								alert("Filters Submitted Succesfully!");
							}
						});
						if(created_set_type == 0) {
							var sku_arr = getSkuMethod(allGridData);
							createGrid3(sku_arr);
							createInstructGird2();
							jQuery('#button3').show();
						}
					}
				}
				
			});

			$('#button3').click(function() {
			
				var rowids = [];
				var columns = [];
				var rowdataold = jQuery('#grid').jqGrid('getRowData');
				var rowdata = jQuery('#grid3').jqGrid('getRowData');
				var ids = jQuery("#grid3").jqGrid('getDataIDs');
				var col_model1 = jQuery("#grid").jqGrid ('getGridParam', 'colModel');
				var col_model3 = jQuery("#grid3").jqGrid ('getGridParam', 'colModel');
				var bool = checkEmptyCell(rowdata,0); //checking for empty cells
				var tmp = bool[0];
				var rowids = bool[1];
				var columns = bool[2];
				
				if(tmp == 1) {
					
					for(var i = 0; i < rowids.length; i++) {
						jQuery('#grid3').jqGrid('setCell', ids[rowids[i]], columns[i], '' , 'highlight');
					}
					for(var i = 0; i < ids.length; i++) {
						if(i != rowids[i]) {
							jQuery('#grid3').jqGrid('setCell', ids[i],'', '' , 'success-highlight');
						}
					}
					
					alert("Empty Values not allowed");	
				} else {
					set_type_arr = Object.values(rowdata);
					col_model1.shift();
					col_model3.shift();
					col_model3.shift();
					var names = [];
					for(var i =0; i < col_model3.length; i++) {					
						names.push(col_model3[i]['name']);
					}
					
					for(var i = 0; i < rowdata.length; i++) {
						for(var j = 0; j < names.length; j++) {
							rowdataold[i][names[j]]= rowdata[i][names[j]];
						}
					}
									
					var sku = col_model1.shift();
					for (var key in col_model3) {
						if (col_model3.hasOwnProperty(key)) {
							col_model1.unshift(col_model3[key]);
						}
					}
					
					col_model1.unshift(sku);
					jQuery("#grid3").jqGrid('GridUnload');
					jQuery("#grid").jqGrid('GridUnload');
					
					creatNewGrid(col_model1,rowdataold);
					creategrid4(rowdataold);
					jQuery('#button3').hide();
					jQuery("#gridinstruct2").jqGrid('GridUnload');
					jQuery('#seconddiv').remove();
				}
			});
			var submit_count = 0;
			
		
			//submitting the first jqgrid				
			jQuery("#submittable").click(function () {
				$grid = $("#grid");
				var griddata = jQuery("#grid").jqGrid('getRowData');
				var deleted_arr = [];
				for(var i = 0; i < griddata.length; i++) {
					delete griddata[i]["actions"];
				}
				
				var rowids = [];
				var columns = [];
					
				var rdata = jQuery('#grid').jqGrid('getRowData');
					
				var ids = jQuery("#grid").jqGrid('getDataIDs');
				var bool = checkEmptyCell(rdata,1); //checking for empty cells
				var tmp = bool[0];
				var rowids = bool[1];
				var columns = bool[2];
				
				
				
				
				if(griddata.length == 0) {
					alert("No data to submit. Please ensure data in grid!");
				} else if(tmp == 1) {				
					alert("Please click on enter to submit the values");	
				} else {
					jQuery.ajax({
						url: "index.php?route=seller/seller_upload/change_jqgrid_data",
						type: 'POST',
						datatype : 'json',		
						data: {griddata: griddata, category_id : category_id , import_csv_id : import_csv_id, row_count : row_count, col_count : col_count, headers : headers, rules : rules,product_ids:product_ids, product_ids_old : product_ids_old},
						success: function (data) {
								var data = JSON.parse(data);
								var flag = data.flag;
								var warning = data.warning;
								//alert(warning);
								var $grid=$("#grid");
								var col_model_grid = jQuery("#grid").jqGrid ('getGridParam', 'colModel');
								var grid_data = data.final_data;
								if(flag == 1) {
									$grid.jqGrid('setGridParam', { datatype: 'local', data: data.final_data,  colModel : col_model_grid}).trigger("reloadGrid");
										var ids = jQuery("#grid").jqGrid('getDataIDs');
										var grid_d = jQuery('#grid').jqGrid('getRowData');
										for(var i = 0; i < ids.length; i++) {
										$('#id_'+i).on('click',function(){
											
											if($(event.target).is('button[name="edit"]')) {
												$( this ).trigger( "dblclick" );
											}
											
											if($(event.target).is('button[name="delete"]')) {
												var id = $(this).prop("id");
												//alert("after save");
												var num = id.split('_')[1];
												//alert(num);
												delete ids[2*num];
												delete ids[parseInt(2*num)+1];
												delete grid_d[2*num];
												delete grid_d[parseInt(2*num)+1];
												$('#'+parseInt(2*num)+1).hide();
												var a = parseInt(2*num)+1;
												$('#'+ a).hide();
												jQuery('#grid').jqGrid('GridUnload');
												refreshgrid(grid_d,listOfColumnModels);
											}
											
										});
									}
								} else if(flag == 0) {
									//alert("please recheck the weight");
									col_model_grid.shift();
									jQuery("#grid").jqGrid('GridUnload');
									var grid_data_delte= deleteRow(grid_data, row_count);
									creatNonEditableGrid(col_model_grid,grid_data_delte);
								}
								
								if (typeof data.filters === 'undefined' ) {
									alert("Please fix the errors");
								} else if(typeof data.filters !== 'undefined'){
									jQuery('#submittable').attr("disabled", true);
									jQuery("#submittable").hide();
									alert("Thank you! Inventory Submitted. Please fill filters");
									var filters_array = data.filters;
									
									non_mandatory_filters_array = data.non_mandatory_filters;									

									filtergroup = data.filter_groups.filter_group_id;
									non_mandatory_filter_groups = data.non_mandatory_filter_groups.filter_group_id;
									non_mandatory_filter_groups_length = non_mandatory_filter_groups.length;
									filter_group_length = filtergroup.length;

									//adding filter group buttons
									
									var trHTML2 = '';
									trHTML2 += '<table class="table table-bordered"><th> Mandatory Filter Groups</th></table>';
									trHTML2 +='<table class="ui-jqgrid-btable table table-bordered"><th>Filter Group Name</th><th>Add Filter</th>';
									for(var i = 0;i<data.filter_groups.filter_group_name.length;i++) {
										for (var key in data.filter_groups.filter_group_name[i]) {	
											if (data.filter_groups.filter_group_name[i].hasOwnProperty(key)) {
											trHTML2 +='<tr id = "tr_' + data.filter_groups.filter_group_id[i] + '"><td id = td_' + data.filter_groups.filter_group_id[i] + '>' + data.filter_groups.filter_group_name[i][key]
													 + '</td><td>' + 
													 '<input class="btn-sm  btn-success" type="button" ' + 
													 'id=' + data.filter_groups.filter_group_id[i] +
													' value="Add"></td></tr>';
											 /*jQuery('#filters').append('<input class="btn  btn-primary" type="button" id=' + 
												data.filter_groups.filter_group_id[i] +
												' value="' + data.filter_groups.filter_group_name[i][key]
												 + '">').append('<br>').append('<br>');	*/
											}
										}
									}
									
									$("#filters").append($(trHTML2));   

									var trHTML = '';
									trHTML += '<table class="table table-bordered"><th> Non Mandatory Filter Groups</th></table>';
									trHTML += '<table class="ui-jqgrid-btable table table-bordered"><th>Filter Group Name</th><th>Skip Filter</th><th>Add Filter</th>';

									for(var i = 0;i<data.non_mandatory_filter_groups.filter_group_name.length;i++) {
										for (var key in data.non_mandatory_filter_groups.filter_group_name[i]) {
											if (data.non_mandatory_filter_groups.filter_group_name[i].hasOwnProperty(key)) {
												trHTML +='<tr id = "tr_' +data.non_mandatory_filter_groups.filter_group_id[i]+'" ><td id= td_' + 
													data.non_mandatory_filter_groups.filter_group_id[i] +
													'>' + data.non_mandatory_filter_groups.filter_group_name[i][key]
													 + '</td><td><input class="btn-sm  btn-danger skip_' + i + '" type="button" id=skip_' + 
													data.non_mandatory_filter_groups.filter_group_id[i] +
													' value="skip"></td><td><input class="btn-sm  btn-success add_' + i + '" type="button" id=add_' + 
													data.non_mandatory_filter_groups.filter_group_id[i] +
													' value="Add"></td></tr>';
											}
											$('#skip_'+data.non_mandatory_filter_groups.filter_group_id[i]).attr('disabled',true);
											$('#add_'+data.non_mandatory_filter_groups.filter_group_id[i]).attr('disabled',true);

										}
									}
									
								
									trHTML += '</table>';

									if(non_mandatory_filter_groups[0] == "") {
										empty_nonmandatory = 1;
									} else {
										$("#filters_nonmandatory").append($(trHTML));   
									}
									
									//adding trHTML to non mandtory filters html
									
									
									for(var i = 0; i < data.non_mandatory_filter_groups.filter_group_id.length; i++) {
										
										jQuery('#skip_' + data.non_mandatory_filter_groups.filter_group_id[i]).on('click', function() {
											if(mandatory_filters == 0)  {
												alert("Please fill all the mandatory filters first! After that you can fill non mandatory filters");
											}
										});
										jQuery('#add_' + data.non_mandatory_filter_groups.filter_group_id[i]).on('click', function() {
											if(mandatory_filters == 0)  {
												alert("Please fill all the mandatory filters first! After that you can fill non mandatory filters");
											}
										});
									}
									
									for(var i = button_count+1; i < data.filter_groups.filter_group_id.length; i++) {
										jQuery('#' + data.filter_groups.filter_group_id[i]).attr("disabled", true);
									}
									
									for(var i = 0; i < data.filter_groups.filter_group_id.length; i++) {
										
									jQuery('#' + data.filter_groups.filter_group_id[i]).on('click', function() {
									//$('#filters > .btn').unbind().on('click',function(){
										jQuery('#' + data.filter_groups.filter_group_id[button_count]).remove();
										//$("br").remove();
										button_count++;

										for(var i = button_count; i < data.filter_groups.filter_group_id.length; i++) {
											jQuery('#' + data.filter_groups.filter_group_id[i]).attr("disabled", true);
										}
										
									//	$('#filters > .btn').prop("disabled",true);
									//	$(this).prop("disabled",false);
										jQuery('#submit_button').show();  
										var id = this.id;
										var text = ($('#td_' + id).html());
										filtergroup.push(id);
										jQuery('#tr_' + id).hide();
										//$('#filters > .btn').prop("disabled",false);
										//$(this).prop("disabled",true);
										var grid = jQuery("#grid");
										var grid2 = jQuery("#grid2");
										var allGridParams = $grid.jqGrid("getGridParam");

										//removing errors row
										var col_model_data = [];
										for(var i = 0;i<(grid_data.length)/2;i++) {
											col_model_data[i] = [];
										}
										
										grid_data_to_show = deleteRow(grid_data, row_count);
										var sku_arr = getSkuMethod(grid_data_to_show);
										
										//removeByAttr(grid_data_to_show, 'name', 'SKU Code');
										for(var i = 0;i<grid_data.length/2;i++) {
											col_model_data[i]['SKU Code'] = sku_arr[i];
											col_model_data[i][text] = '';
										}
									
										var col_model = [];
										col_model.push({
											name: 'SKU Code',
											index : id,
											align : "center",
											rownumbers : true,
											editrules: { required: true },

										});
										
										col_model.push({
											name: text,
											index : id,
											editable: true,
											edittype:'custom',
											classes:"autocomplete",
											cellLayout:30,                
											rownumbers: true,
											align : "center",
											sortable: false,
											editoptions:{'custom_element':autocomplete_element,'custom_value':autocomplete_value},
										});
										
										
										filter_groups.push(text);
										if(listOfColumnModels[0]['name'] == 'actions') {
											listOfColumnModels.shift();
										}
							
										var SKU_Code = listOfColumnModels.shift();
										
										listOfColumnModels.unshift({		
											name: text,
											index : id,
											align : "center",
											sortable: false,
											editable : true,
											edittype : 'text',
											loadonce : true,
											sortable : false,
											width : 180

										});
											
										listOfColumnModels.unshift({		
											align:SKU_Code.align,
											caption:SKU_Code.caption,
											cellLayout:SKU_Code.cellLayout,
											cellattr:SKU_Code.cellattr,
											height:SKU_Code.height,
											loadonce:SKU_Code.loadonce,
											name:SKU_Code.name,
											pager:SKU_Code.pager,
											rownumbers:SKU_Code.rownumbers,
											viewrecords:SKU_Code.viewrecords,
											width:SKU_Code.width,
											editable : true,
											edittype : 'text',
											loadonce : true,
											sortable : false,
											width : 180

											});
											
										
										for(var i =0; i < grid_data_to_show.length/2; i++) {
												grid_data_to_show[i][text] = '';
											}
										
										creatNewGrid2(col_model,col_model_data);
										
										createInstructGird();
										function autocomplete_element(value, options) {	
											
											var cm = jQuery("#grid2").jqGrid("getGridParam", "colModel");	
											var id = cm[0]['index'];
											// create input element
											var $ac = $('<input type="text"  required="required" />');
											// setting value to the one passed from jqGrid
											var a = $ac.val(value);
											var filters_name = [];
											for(var i = 0; i < filters_array[id].length; i++) {
												filters_name.push(filters_array[id][i].name);
												filters_name2.push(filters_array[id][i].name);
												filters_id.push(filters_array[id][i].filter_id);
											}
											$ac.autocomplete( {
												source: filters_name
											});
											return $ac.get(0); 
										}
									
										function autocomplete_value(elem, op, v) {
											
											var a = $(elem).val();
											if(filters_name2.indexOf(a) == -1) {
												alert("entered value is not valid");
												return "";
											} else {
												if (op == 'set') {
													jQuery(elem).val(v);
												}
												return jQuery(elem).val();
											}
										}
									
									});							

									}
								}
							},
						});
					}
				});		
			});	
		//]]>
		</script>


<body>


</body>
</html>
