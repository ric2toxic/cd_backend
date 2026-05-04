<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-paycharge" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-paycharge" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="paycharge_status" id="input-status" class="form-control">
                <?php if ($paycharge_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><span data-toggle="tooltip" title="<?php echo $help_sort_order; ?>"><?php echo $entry_sort_order; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="paycharge_sort_order" value="<?php echo $paycharge_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
        
        <!-- Paycharge Tabs --> 
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-paycharge_methods" data-toggle="tab"><?php echo $tab_method_rules?></a></li>
            <li><a href="#tab-paycharge_rules" data-toggle="tab"><?php echo $tab_exception_rules?></a></li>
          </ul>
        <!-- Paycharge Tabs --> 

        <div class="tab-content">
          
          <div class="tab-pane active" id="tab-paycharge_methods">
            
            <div class="table-responsive">
              <table class="table table-bordered table-hover TextAppend">
                <thead>
                  <tr>
                    <td width="24%" class="text-left"><?php echo $column_rules; ?></td>
                    <td width="24%" class="text-left"><span data-toggle="tooltip" title="<?php echo $help_charge; ?>"><?php echo $column_charge; ?></span></td>
                    <td width="24%" class="text-left"><?php echo "Amount"; ?></td>
                    <td width="24%" class="text-left"><?php echo $column_description; ?></td>
                    <td width="4%"></td>
                  </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach($paycharges as $paycharge){
                ?>
                  <tr id="paycharge_<?php echo $i; ?>">
                    <td width="24%" class="text-left" style="vertical-align:top;">
                      <select name="paycharge[<?php echo $i; ?>][payment_method]" class="form-control">
                        <?php foreach ($payments as $payment) { ?>
                        <?php if ($payment['code'] == $paycharge['payment_method']) { ?>
                        <option value="<?php echo $payment['code']; ?>" selected="selected"><?php echo $payment['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $payment['code']; ?>"><?php echo $payment['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </td>
                    <td width="24%" class="text-left" style="vertical-align:top;">
                      <div class="input-group">
                        <span class="input-group-addon" style="min-width:40px;"><?php echo $entry_percentage[0]; ?></span>
                        <input type="text" name="paycharge[<?php echo $i; ?>][valuep]" value="<?php echo $paycharge['valuep']; ?>" placeholder="<?php echo $entry_percentage[1]; ?>" class="form-control" />
                      </div>
                    </td>
                    <td width="24%" class="text-left" style="vertical-align:top;">
                          <div class="input-group">
                              <span class="input-group-addon" style="min-width:40px;"><i class="fa fa-inr" aria-hidden="true"></i></span>
                              <input type="text" name="paycharge[<?php echo $i; ?>][amount]" value="<?php echo $paycharge['amount'] ?? 0; ?>" placeholder="Amount" class="form-control" />
                          </div>
                      </td>
                    <td width="24%" class="text-left" style="vertical-align:top;">
                      <?php foreach ($languages as $language) { ?>
                      <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="paycharge[<?php echo $i; ?>][description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($paycharge['description'][$language['language_id']]) ? $paycharge['description'][$language['language_id']]['name'] : ''; ?>" class="form-control" /></div>
                      <?php } ?>
                    </td>
                    <td width="4%" class="text-left"><button type="button" onclick="tremove('paycharge_<?php echo $i; ?>');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                  </tr>
                  <?php
                  $i++;
                  }
                ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4"></td>
                    <td class="text-left"><button type="button" onclick="upgrade();" data-toggle="tooltip" title="<?php echo $button_paycharge_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                  </tr>
                </tfoot>
              </table>
              </div>
          </div>

          <div class="tab-pane" id="tab-paycharge_rules">
              <div class="form-group">
                 <div class="col-sm-2">
                  <input class="form-control" type="text" name="pid" id="pid" placeholder="By Product Id">
                </div>
                <div class="col-sm-2">
                  <input class="form-control" type="text" name="pmodel" id="pmodel" placeholder="By Product Model">
                </div>
                <div class="col-sm-2">
                  <input class="form-control" type="text" name="psku" id="psku" placeholder="By Product SKU">
                </div>
                <div class="col-sm-2">
                  <select name="catid" id="catid" class="form-control">
                   <option value="0">Select Category</option>
                   <?php foreach ($categories as $category_1) { ?>
                     <?php if ($category_1['category_id'] == $filter_category) { ?>
                     <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
                     <?php } else { ?>
                     <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
                     <?php } ?>
                     <?php foreach ($category_1['children'] as $category_2) { ?>
                     <?php if ($category_2['category_id'] == $filter_category) { ?>
                     <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                     <?php } else { ?>
                     <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
                     <?php } ?>
                     <?php foreach ($category_2['children'] as $category_3) { ?>
                     <?php if ($category_3['category_id'] == $filter_category) { ?>
                     <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                     <?php } else { ?>
                     <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
                     <?php } ?>
                     <?php } ?>
                     <?php } ?>
                   <?php } ?>
                 </select>
                </div>
                <div class="col-sm-2">
                  <input class="form-control" type="text" name="seller_nickname" id="seller_nickname" placeholder="By Seller Nickname">
                </div>
                <div class="col-sm-2">
                  <button type="button" class="btn btn-primary search-exception-rules">Search</button>
                </div>
              </div>

              <div id="exception-search-result"><!--Paycharge Exception Search Result--></div>

          </div>
        </div>
          
        </form>
      </div>
    </div>
    <div style="text-align:right;padding:0 20px;"><?php echo $fs_version; ?></div>
  </div>
</div>
<div id="AddDivBox" style="display:none;">
	<table id="OuterTable">
	  <tr id="paycharge_0">
		<td width="24%" class="text-left" style=" vertical-align:top;">
		  <select name="paycharge[0][payment_method]" class="form-control">
			<?php foreach ($payments as $payment) { ?>
			<?php if ($payment['code'] == $paycharge['payment_method']) { ?>
			<option value="<?php echo $payment['code']; ?>" selected="selected"><?php echo $payment['name']; ?></option>
			<?php } else { ?>
			<option value="<?php echo $payment['code']; ?>"><?php echo $payment['name']; ?></option>
			<?php } ?>
			<?php } ?>
		  </select>
		</td>
		<td width="24%" class="text-left" style="vertical-align:top;">
		  <div class="input-group">
			<span class="input-group-addon" style="min-width:40px;"><?php echo $entry_percentage[0]; ?></span>
			<input type="text" name="paycharge[0][valuep]" value="" placeholder="<?php echo $entry_percentage[1]; ?>" class="form-control" />
		  </div>
		</td>
        <td width="24%" class="text-left" style="vertical-align:top;">
              <div class="input-group">
                  <span class="input-group-addon" style="min-width:43px;"><i class="fa fa-inr" aria-hidden="true"></i></span>
                  <input type="text" name="paycharge[0][amount]" value="" placeholder="Amount" class="form-control" />
              </div>
          </td>
		<td width="24%" class="text-left" style="width:23%;vertical-align:top;">
		  <?php foreach ($languages as $language) { ?>
		  <div class="input-group"><span class="input-group-addon"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span><input type="text" name="paycharge[0][description][<?php echo $language['language_id']; ?>][name]" value="" class="form-control" /></div>
		  <?php } ?>
		</td>
		<td width="4%" class="text-left"><button type="button" onclick="tremove('paycharge_0');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
	  </tr>
  </table>
</div>

<script type="text/javascript">
	var flag = '0';
	var replaceI = '0';
function upgrade() {
	if(flag == 0){
		replaceI = '<?php echo $i;?>';
	}
	var replaceWith = "paycharge["+replaceI+"]";
	var tobereplaced = "paycharge[0]";

	var replaceWithId = "paycharge_"+replaceI;
	var tobereplacedId = "paycharge_0";

	var text = $("#AddDivBox").html();

  var new_text = text.replace(tobereplaced, replaceWith);
	var new_text = new_text.replace(tobereplaced, replaceWith);
	var new_text = new_text.replace(tobereplaced, replaceWith);
  var new_text = text.replace(/\paycharge\[0\]/g, replaceWith);
	var new_text = new_text.replace(tobereplacedId, replaceWithId);
	var new_text = new_text.replace(tobereplacedId, replaceWithId);

	var new_text = new_text.replace('<table id="OuterTable">', '');
	var new_text = new_text.replace('<tbody>', '');
	var new_text = new_text.replace('</tbody>', '');
	var new_text = new_text.replace('</table>', '');

  if($("table.TextAppend tbody").append(new_text)){
		flag = '1';
		replaceI++;
	}

	/*var upPayCharge = confirm('Upgrade Your PayCharge to get all features!\n- More rules\n- Fixed Value\n- Tax Classes\n- More options');

	if (upPayCharge) {
		window.location.href = 'http://www.opencart.com/index.php?route=extension/extension&filter_username=fabiom7';
	}*/
}
function tremove(id) {
	$("tr#"+id).remove();
}

function updateException(action, type, type_id)
{
  var ajax_loader = '<img src="<?php echo $image_path?>">';
  var applicable  = 0;
  if($('#'+type+'_applicable_'+type_id).prop('checked') == true){
    applicable  = 1;
  }
  $.ajax({
          url: 'index.php?route=total/paycharge/updateException&token=<?php echo $token; ?>&action='+action+'&type='+type+'&type_id='+type_id+'&applicable='+applicable,
          //dataType: 'json',
          complete: function() {},
          beforeSend: function() {
            $("#"+type+"-"+action+"-button-"+type_id).html(ajax_loader);
          },
          success: function(data) {
            if(action == 'add') {
              $("#"+type+"-add-button-"+type_id).html("Added "+type+" in exception rules.");
            }else if(action == 'delete'){
              $("#"+type+"-delete-button-"+type_id).html("Removed "+type+" from exception rules.");
            }

          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
}

$(document).on('change','.update-applicable-status',function(){
  var ajax_loader = '<img src="<?php echo $image_path?>">';
  var exception_rule_id = $(this).data('exception-id');
  var applicable_status = 0;
  if(this.checked == true){
    applicable_status = 1;
  }
  if(exception_rule_id > 0 ) {
    $.ajax({
          url: 'index.php?route=total/paycharge/updateApplicableStatus&token=<?php echo $token; ?>&exception_rule_id='+exception_rule_id+'&applicable_status='+applicable_status,
          complete: function() {},
          beforeSend: function() {
            $("#"+exception_rule_id).html(ajax_loader);
          },
          success: function(data) {
            $("#"+exception_rule_id).html("");
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
    }
  
})

$(document).on("click",".search-exception-rules",function(){
  
  var pid       = $("#pid").val();
  var pmodel    = $("#pmodel").val();
  var psku      = $("#psku").val();
  var catid     = $("#catid").val();
  var seller_nickname = $("#seller_nickname").val();
  var ajax_loader = '<img src="<?php echo $image_path?>">';
  $.ajax({
          url: 'index.php?route=total/paycharge/paycharge_rules&token=<?php echo $token; ?>&pid='+pid+'&pmodel='+pmodel+'&psku='+psku+'&catid='+catid+'&seller_nickname='+seller_nickname,
          dataType: 'json',
          beforeSend: function() {
            $('#exception-search-result').html(ajax_loader);
          },
          complete: function() {},
          success: function(json) {
            if($.isEmptyObject(json)){
              $("#exception-search-result").html('<p>No data found.</p>');
            }else{
                html = '';
                $.each(json,function(index,item){
                  //Product  
                    if(index == 'product') {
                      if(item[0] !== undefined){
                        html += "<table width='100%' style='margin-bottom:10px'>";
                          html += "<tr>";
                            html += "<th>Product ID</th>";
                            html += "<th>Product Name</th>";
                            html += "<th>Model</th>";
                            html += "<th>SKU</th>";
                            html += "<th>Applicable On Membership</th>";
                            html += "<th>Action</th>";
                          html += "</tr>";
                        $.each(item,function(key,product){
                           html += "<tr>";
                            html += "<td>"+product.product_id+"</td>";
                            html += "<td>"+product.name+"</td>";
                            html += "<td>"+product.model+"</td>";
                            html += "<td>"+product.sku+"</td>";
                            var applicable_on_membership = '';
                            if( product.isException == 0 || product.applicableOnMembership == '1' ){
                              applicable_on_membership = 'checked';
                            }
                            html += "<td>";
                              html += "<input class='update-applicable-status' data-exception-id='"+product.isException+"' type='checkbox' id='product_applicable_"+product.product_id+"' "+applicable_on_membership+" />";
                              html += "<br><span id='"+product.isException+"'></span>";
                            html += "</td>";
                            if(product.isException > 0){
                              html += "<td id='product-delete-button-"+product.product_id+"'><button type='button' onclick=updateException('delete','product','"+product.product_id+"') class='btn btn-danger' title='Remove From Exception'><i class='fa fa-minus-circle'></i></button></td>";  
                            }else{
                              html += "<td id='product-add-button-"+product.product_id+"'><button type='button' onclick=updateException('add','product','"+product.product_id+"') class='btn btn-primary' title='Add For Exception'><i class='fa fa-plus-circle'></i></button></td>";  
                            }
                          html += "</tr>";
                        })
                         html += "</table>";
                      }
                    }
                  //Category
                   if(index == 'category') {
                      html += "<table width='100%' style='margin-bottom:10px'>";
                          html += "<tr>";
                            html += "<th>Category ID</th>";
                            html += "<th>Category Name</th>";
                            html += "<th>Applicable On Membership</th>";
                            html += "<th>Action</th>";
                          html += "</tr>";
                          html += "<tr>";
                            html += "<td>"+item.category_id+"</td>";
                            html += "<td>"+item.name+"</td>";
                            var applicable_on_membership = '';
                            if( item.isException == 0 || item.applicableOnMembership == '1' ){
                              applicable_on_membership = 'checked';
                            }
                            html += "<td>";
                              html += "<input class='update-applicable-status' data-exception-id='"+item.isException+"' type='checkbox' id='category_applicable_"+item.category_id+"' "+applicable_on_membership+" />";
                              html += "<br><span id='"+item.isException+"'></span>";
                            html += "</td>";
                            if(item.isException > 0){
                              html += "<td id='category-delete-button-"+item.category_id+"'><button type='button' onclick=updateException('delete','category','"+item.category_id+"') class='btn btn-danger' title='Remove From Exception'><i class='fa fa-minus-circle'></i></button></td>";  
                            }else{
                              html += "<td id='category-add-button-"+item.category_id+"'><button type='button' onclick=updateException('add','category','"+item.category_id+"') class='btn btn-primary' title='Add For Exception'><i class='fa fa-plus-circle'></i></button></td>";  
                            }
                          html += "</tr>";
                      html += "</table>";
                   } 
                  //Seller 
                  if(index == 'seller') {
                      html += "<table width='100%' style='margin-bottom:10px'>";
                          html += "<tr>";
                            html += "<th>Seller ID</th>";
                            html += "<th>Seller Nickname</th>";
                            html += "<th>Company</th>";
                            html += "<th>Applicable On Membership</th>";
                            html += "<th>Action</th>";
                          html += "</tr>";
                          html += "<tr>";
                            html += "<td>"+item.seller_id+"</td>";
                            html += "<td>"+item.nickname+"</td>";
                            html += "<td>"+item.company+"</td>";
                            var applicable_on_membership = '';
                            if( item.isException == 0 || item .applicableOnMembership == '1' ){
                              applicable_on_membership = 'checked';
                            }
                            html += "<td>";
                              html += "<input class='update-applicable-status' data-exception-id='"+item.isException+"' type='checkbox' id='seller_applicable_"+item.seller_id+"' "+applicable_on_membership+" />";
                              html += "<br><span id='"+item.isException+"'></span>";
                            html += "</td>";
                            if(item.isException > 0){
                              html += "<td id='seller-delete-button-"+item.seller_id+"'><button type='button' onclick=updateException('delete','seller','"+item.seller_id+"') class='btn btn-danger' title='Remove From Exception'><i class='fa fa-minus-circle'></i></button></td>";  
                            }else{
                              html += "<td id='seller-add-button-"+item.seller_id+"'><button type='button' onclick=updateException('add','seller','"+item.seller_id+"') class='btn btn-primary' title='Add For Exception'><i class='fa fa-plus-circle'></i></button></td>";  
                            }
                          html += "</tr>";
                      html += "</table>";
                   }
                });
                $("#exception-search-result").html(html);
             }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
})
</script>
<?php echo $footer; ?>
