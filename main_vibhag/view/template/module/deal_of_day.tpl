<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" xmlns="http://www.w3.org/1999/html">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured" class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="deal_of_day_status" id="input-status" class="form-control">
                                <?php if ($deal_of_day_status) { ?>
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
                            <input type="text" name="deal_of_day_sort_order" value="<?php echo $deal_of_day_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>



                    <?php
          $i = 0;
          if(isset($deal_of_day) && !empty($deal_of_day)){
          foreach($deal_of_day as $value){  ?>

                    <div class="deal_of_day_<?php echo $i; ?> row">
                        <div class="col-sm-2">
                            <label class="control-label" for="input-sellers"><?php echo $text_seller; ?></label>
                            <div class="">
                                <select name="deal_of_day[<?php echo $i; ?>][seller_id]" id="input-sellers" class="form-control">
                                    <option value=""><?php echo $text_select;?></option>
                                    <?php
					  if($sellers_list){
						foreach($sellers_list as $seller_list){
							if($seller_list['customer_id'] == $value['seller_id']){
								$selected = "selected";
							}else{
								$selected = "";
							}
					?>
                                    <option value="<?php echo $seller_list['customer_id']; ?>" <?php echo $selected; ?>><?php echo $seller_list['firstname'];?></option>
                                    <?php
						}
					  }
					?>
                                </select>
                            </div>
                            <label class="control-label" for="input-status"><?php echo $text_description; ?></label>
                            <div class="">
                                <textarea name="deal_of_day[<?php echo $i; ?>][description]" value="<?php //echo $value['description']; ?>" placeholder="<?php echo $text_description; ?>" id="input-description" class="form-control" /><?php echo $value['description']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" for="input-order-amount"><?php echo $text_order_amount; ?></label>
                            <div class="">
                                <input type="text" name="deal_of_day[<?php echo $i; ?>][order_amount]" value="<?php echo $value['order_amount']; ?>" placeholder="<?php echo $text_order_amount; ?>" id="input-order-amount" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" for="input-discount"><?php echo $text_discount; ?></label>
                            <div class="">
                                <input type="text" name="deal_of_day[<?php echo $i; ?>][discount]" value="<?php echo $value['discount']; ?>" placeholder="<?php echo $text_discount; ?>" id="input-discount-rate" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" for="input-start-date"><?php echo $text_start_date; ?></label>
                            <div class="">
                                <input type="text" name="deal_of_day[<?php echo $i; ?>][start_date]" value="<?php echo $value['start_date']; ?>" placeholder="<?php echo $text_start_date; ?>" id="input-start-date_deal_of_day_<?php echo $i; ?>" class="form-control input-start-date" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <label class="control-label" for="input-end-date"><?php echo $text_end_date; ?></label>
                            <div class="">
                                <input type="text" name="deal_of_day[<?php echo $i; ?>][end_date]" value="<?php echo $value['end_date']; ?>" placeholder="<?php echo $text_end_date; ?>" id="input-end-date_deal_of_day_0" class="form-control input-end-date" />
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <label class="control-label" for="input-status"><?php echo $text_status; ?></label>
                            <div class="">
                                <input type="text" name="deal_of_day[<?php echo $i; ?>][status]" value="<?php echo $value['status']; ?>" placeholder="<?php echo $text_status; ?>" id="input-status" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <label class="control-label" for="input-remove"> </label>
                            <div class="text-right">
                                <input type="button" onClick="removeButton('deal_of_day_<?php echo $i; ?>');" name="remove_button" id="remove_button_deal_of_day_<?php echo $i; ?>" class="btn btn-primary btn-sm remove_button"  value="Remove"/>
                            </div>
                        </div>
                    </div>
                    <?php $i++; }
			}
		  ?>
                </form>
                <br />
                <div class="add_more_button">
                    <div class="col-sm-1">
                        <input type="button" name="add_more_button" id="add_more_button" class="btn btn-primary btn-sm add_more_button"  value="Add More"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="AddDivBox" style="display:none;">
    <div class="Main_div">

        <div class="deal_of_day_0 row">
            <div class="col-sm-2">
                <label class="control-label" for="input-sellers"><?php echo $text_seller; ?></label>
                <div class="">
                    <select name="deal_of_day[0][seller_id]" id="input-sellers" class="form-control">
                        <option value=""><?php echo $text_select;?></option>
                        <?php
				  if($sellers_list){
					foreach($sellers_list as $seller_list){
						/*if($seller_list['customer_id'] == $value['seller_id']){
							$selected = "selected";
						}else{
							$selected = "";
						}*/
				?>
                        <option value="<?php echo $seller_list['customer_id']; ?>" <?php //echo $selected; ?>><?php echo $seller_list['firstname'];?></option>
                        <?php
					}
				  }
				?>
                    </select>
                </div>
                <label class="control-label" for="input-status"><?php echo $text_description; ?></label>
                <div class="">
                    <textarea name="deal_of_day[0][description]" value="<?php //echo $value['description']; ?>" placeholder="<?php echo $text_description; ?>" id="input-description" class="form-control" /><?php //echo $value['description']; ?></textarea>
                </div>
            </div>
            <div class="col-sm-2">
                <label class="control-label" for="input-order-amount"><?php echo $text_order_amount; ?></label>
                <div class="">
                    <input type="text" name="deal_of_day[0][order_amount]" value="<?php //echo $text_order_amount; ?>" placeholder="<?php echo $text_order_amount; ?>" id="input-order-amount" class="form-control" />
                </div>
            </div>
            <div class="col-sm-2">
                <label class="control-label" for="input-discount"><?php echo $text_discount; ?></label>
                <div class="">
                    <input type="text" name="deal_of_day[0][discount]" value="<?php //echo $text_discount; ?>" placeholder="<?php echo $text_discount; ?>" id="input-discount-rate" class="form-control" />
                </div>
            </div>
            <div class="col-sm-2">
                <label class="control-label" for="input-start-date"><?php echo $text_start_date; ?></label>
                <div class="">
                    <input type="text" name="deal_of_day[0][start_date]" value="<?php //echo $text_start_date; ?>" placeholder="<?php echo $text_start_date; ?>" id="input-start-date_deal_of_day_0" class="form-control input-start-date" />
                </div>
            </div>
            <div class="col-sm-2">
                <label class="control-label" for="input-end-date"><?php echo $text_end_date; ?></label>
                <div class="">
                    <input type="text" name="deal_of_day[0][end_date]" value="<?php //echo $text_end_date; ?>" placeholder="<?php echo $text_end_date; ?>" id="input-end-date_deal_of_day_0" class="form-control input-end-date" />
                </div>
            </div>
            <div class="col-sm-1">
                <label class="control-label" for="input-status"><?php echo $text_status; ?></label>
                <div class="">
                    <input type="text" name="deal_of_day[0][status]" value="<?php //echo $text_status; ?>" placeholder="<?php echo $text_status; ?>" id="input-status" class="form-control" />
                </div>
            </div>
            <div class="col-sm-1">
                <label class="control-label" for="input-remove"></label>
                <div class="text-right">
                    <input type="button" onClick="removeButton('deal_of_day_0');" name="remove_button" id="remove_button_deal_of_day_0" class="btn btn-primary btn-sm remove_button"  value="Remove"/>
                </div>
            </div>
        </div>
    </div>

</div>



<script type="text/javascript">
    $(document).ready(function () {
//    $('.input-start-date').datetimepicker({
//      //pickTime: false
//    });
//    $('.input-end-date').datetimepicker({
//      //pickTime: false
//    });

        $(document).delegate('.input-start-date', 'focus', function() {
            $(this).datetimepicker();
        });
        $(document).delegate('.input-end-date', 'focus', function() {
            $(this).datetimepicker();
        });

        var flag = '0';
        var replaceI = '0';
        $('#add_more_button').click(function(){
            if(flag == 0){
                replaceI = '<?php echo $i;?>';
            }
            var replaceWith = "deal_of_day["+replaceI+"]";
            var tobereplaced = "deal_of_day[0]";

            var replaceWithId = "deal_of_day_"+replaceI;
            var tobereplacedId = "deal_of_day_0";

            var text = $('#AddDivBox').html();


            var new_text = text.replace(tobereplaced, replaceWith);

            var new_text = new_text.replace(tobereplaced, replaceWith);
            var new_text = new_text.replace(tobereplaced, replaceWith);
            var new_text = new_text.replace(tobereplaced, replaceWith);
            var new_text = new_text.replace(tobereplaced, replaceWith);
            var new_text = new_text.replace(tobereplaced, replaceWith);
            var new_text = new_text.replace(tobereplaced, replaceWith);

            var new_text = new_text.replace(tobereplacedId, replaceWithId);
            var new_text = new_text.replace(tobereplacedId, replaceWithId);
            var new_text = new_text.replace(tobereplacedId, replaceWithId);
            var new_text = new_text.replace(tobereplacedId, replaceWithId);
            var new_text = new_text.replace(tobereplacedId, replaceWithId);
            var new_text = new_text.replace(tobereplacedId, replaceWithId);



            var new_text = new_text.replace('<div class="Main_div">', '');
            //var new_text = new_text.replace('</div>', '');
            // console.log(new_text);

            if($("Form#form-featured").append(new_text)){
                flag = '1';
                replaceI++;
            }
        });
    });

    function removeButton(data){
        $('div.'+data).remove();
    }
</script>
<?php //echo $footer; ?>
