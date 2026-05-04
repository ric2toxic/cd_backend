<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="container-fluid">

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php //echo $text_list; ?></h3>
      </div>
      
      <div class="panel-body">
		  <div class="row">
			  <div class="col-sm-3 text-left">
				  <select class="sellers_list form-control">
					<?php 
						foreach($sellers as $seller){
					?>
							<option value="<?php echo $seller['seller_id'];?>"><?php echo $seller['c.name'];?></option>
					<?php 
						}
					?>
				  </select>
			  </div>
			  </div>
			  <br />
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
					<td class="text-center">Image</td>
					<td class="text-center">Product Sku</td>
					<td class="text-center">Product Name</td>
					<td class="text-center">Seller</td>
					<td class="text-center">Transfer Price</td>
					<td class="text-center">Action</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($product_to_approve_list) { ?>
                <?php foreach ($product_to_approve_list as $product_to_approve) { ?>
                <tr>
					<td class="text-center"><?php if ($product_to_approve['image']) { ?>
						<img src="<?php echo $product_to_approve['image']; ?>" alt="<?php echo $product_to_approve['name']; ?>" class="img-thumbnail" />
						<?php } else { ?>
						<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
						<?php } ?>
                    </td>
					<td class="text-center"><?php echo $product_to_approve['name']; ?></td>
					<td class="text-center"><?php echo $product_to_approve['sku']; ?></td>
					<td class="text-center"><?php echo $product_to_approve['nickname']; ?></td>
					<td class="text-center"><?php echo $product_to_approve['price']; ?></td>
					<td class="text-center">
						<a href="<?php echo $approve_link.'&product_id='.$product_to_approve['product_id']; ?>" data-toggle="tooltip" title="<?php //echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-check"></i></a>
						<!--<a href="<?php //echo $cancel_link; ?>" data-toggle="tooltip" title="<?php //echo $button_edit; ?>" class="btn cancel_link">Canceled</a>-->
						<a href="javascript:void(0);" html-data="<?php echo $product_to_approve['product_id'];?>" data-toggle="tooltip" title="<?php //echo $button_edit; ?>" class="btn btn-primary cancel_link"><i class="fa fa-times"></i></a>
						<a href="<?php echo $edit_link.'&product_id='.$product_to_approve['product_id']; ?>" html-data="<?php echo $product_to_approve['product_id'];?>" data-toggle="tooltip" title="<?php //echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
						<div id="product_to_approve_comment_div_<?php echo $product_to_approve['product_id'];?>" style="display:none;" class="product_to_approve_comment_div cancel_popup">
							<span class="cancel_cross" html-data="<?php echo $product_to_approve['product_id'];?>" ><i class="fa fa-times"></i></span>
							<form action="<?php echo $cancel_link;?>" method="post" id="product_to_approve_comment_<?php echo $product_to_approve['product_id'];?>" class="product_to_approve_comment">
								<input type="hidden" name="product_id" value="<?php echo $product_to_approve['product_id'];?>" />
								<textarea name="comment" placeholder="Enter your comment"><?php echo $product_to_approve['comment']; ?></textarea>
								<input type="submit" value="Submit" class="product_to_approve_comment_submit" html-data="<?php echo $product_to_approve['product_id'];?>" />
							</form>
						</div>
					</td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8">Data Not Found.<?php //echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php //echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  
<script type="text/javascript"><!--

$("select.sellers_list").change(function(){
	var url = "<?php echo HTTP_SERVER;?>index.php?route=catalog/product/product_to_approve&token=<?php echo $token; ?>&seller="+$(this).val();
	window.location.href = url;
});

$("a.cancel_link").click(function(){
	$("div.product_to_approve_comment_div").hide();
	$("#product_to_approve_comment_div_"+$(this).attr("html-data")).show()
});

$("span.cancel_cross").click(function(){
	//alert($(this).attr("html-data"));
	$("div#product_to_approve_comment_div_"+$(this).attr("html-data")).hide();
});
$(".product_to_approve_comment_submit").click(function(){
		//alert($(this).attr("html-data"));
		//alert($("#product_to_approve_comment_"+$(this).attr("html-data")).serialize());
	//	return false;
});

//--></script></div>

<style>
.cancel_popup{background: #ccc none repeat scroll 0 0; border-radius: 10px;height: 113px; width: 225px;}
.cancel_popup span{ float: right; margin-right: 10px;margin-top: 3px; cursor:pointer;}
</style>
<?php echo $footer; ?>
