<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  	<div class="page-header">
    	<div class="container-fluid">
      		<div class="pull-right">
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
        		<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?> </h3>
            <span class="sent_msg" style="display: none;">Message sent successfully</span>
			</div>
			<div class="panel-body">
				<div class="tab-content">
            <fieldset>
              <legend>Add Message</legend>		 
              <form class="form-horizontal" id="notification_form" action="index.php?route=notification/notification/sendPushNotificationToCustomers&token=<?php echo $this->session->data['token']; ?>" enctype="multipart/form-data" method="post">
                	<div class="form-group">
   		    					<label class="col-sm-2 control-label" for="input-order-status">Message Type</label>
        						<div class="col-sm-10" >
            						<select name="message_type" id="message_type" class="form-control">
									<?php foreach($message_type as $message_name){ ?>
                						<option value="<?php echo  $message_name['message_type_id']; ?>"><?php echo $message_name['name']; ?></option>   	
               						<?php } ?>
            						</select>
        						</div>
        					</div>

							    <div class="form-group" id="url">
					            <label class="col-sm-2 control-label" for="input-URL">URL</label>
					            <div class="col-sm-10">
					                <input type="text" name="URL" rows="8" placeholder="URL" id="input-url" class="form-control">
					                </input>
						        </div>
					        </div>

					        <div class="form-group" id="category_div" style="display: none;">
					            <label class="col-sm-2 control-label" for="input-category_name"><?php echo $text_category_id; ?></label>
					            <div class="col-sm-10">
                        <input type="text" name="category_name"placeholder="Category Name" id="input-category_name" class="form-control">
                        </input>
                      </div>
                      <div class="col-sm-10" style="display: none;"> 
                        <input type="text" name="category_id"placeholder="Category ID" id="input-category_id" class="form-control">
                        </input>
                      </div>
					        </div>

                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-message"><?php echo $text_message; ?></label>
                      <div class="col-sm-10">
                          <textarea name="message" rows="8" placeholder="<?php echo $text_message; ?>" id="input-comment" class="form-control"></textarea>
                    </div>
                  </div>							   
                   <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-search-url"><?php echo $text_search_url; ?></label>
                      <div class="col-sm-10">
                          <input name="search_url" rows="8" placeholder="<?php echo $text_search_url; ?>" id="input-search" class="form-control"/>
                    </div>
                  </div>
                  
                  <div class="form-group" id="image_div" style="display: none;">
                       <label class="col-sm-2 control-label" for="input-message">Image</label>&nbsp;
                      <div class="col-sm-3">
                        <div class="btn btn-default">
                          <input type="file" name="image" id="fileToUpload">
                        </div>
                      </div>
                  </div>
                  <div class="form-group" id="image_show_div" style="display: none;">
                      <label class="col-sm-2"></label>
                      <div class="col-sm-10"><img src="<?php echo $default_image; ?>" width="<?php echo $default_image_width; ?>" height="<?php echo $default_image_height; ?>" /></div>
                  </div>
                  <div>
                    <input type="hidden" class="notification_type" name="notification_type" value="<?php echo $notification_type; ?>" />
                    <input type="hidden" class="notification_img_path" name="notification_img_path" value="" />
                  </div>

                  <?php if(!empty($customers)){ ?>
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-should-downlaod"><?php echo $text_customers; ?></label>
                      <div class="col-sm-10">
                        <div class="well well-sm" style="height: 150px; overflow: auto;">
                          <?php foreach($customers as $customer) { ?>
                            <div class="checkbox">
                              <label>
                                <input name="customers[]" value="<?php echo $customer['customer_id'] ?>" checked="checked" type="checkbox">
                                <?php echo $customer['customer_name'] ?> (<?php echo $customer['customer_mobile'] ?>)
                              </label>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>

                  <div class="pull-left"> 
                        <button class="btn btn-primary notification_for_preview" id="preview" data-loading-text="Loading..." id="button-notification"><i class="fa fa-paper-plane"></i>Preview</button>
                  </div>
      						<div class="pull-right"> 
      					        <button class="btn btn-primary notification_for_all" id="send_button" data-loading-text="Loading..." id="button-notification"><i class="fa fa-paper-plane"></i> Send</button>
      					  </div>
					    </form>
    				</fieldset>
    			</div>
    		</div>
    	</div>
    </div>
</div>

<?php echo $footer; ?> 
<script type="text/javascript">
<!--
$(document).ready(function () {
    $("#message_type").change(function (e) {
       var value = $(this).val();
       if(value == 2 || value ==4){
       	$("#category_div").show();
       }else{
       	$("#category_div").hide();
       }
       if(value ==2 || value ==3 || value == 4){
        $("#image_div").show();
        $('#image_show_div').show();
        $("#send_button").attr('disabled','disabled');
       }else{
        $("#image_div").hide();
        $("#image_show_div").hide();
        $('#send_button').removeAttr('disabled');
       }
       if(value ==2 || value == 4){
        $("#url").hide();
       }else{
        $("#url").show();
       }
       $(".sent_msg").hide();
    });
    $(".notification_for_all").click(function(){
      var data_class  = $('.notification_type').attr('value', '1');
    });
    $(".notification_for_preview").click(function(){
      var data_class  = $('.notification_type').attr('value', '0');
    });
});      
//--></script>
<script type='text/javascript'>
  $("form[name='image']").submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        var url = $form.attr( 'action' );
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            async: false,
            success: function (msg) {
               $(".sent_msg").show();
            }
        });

    });
//--></script>
<script type="text/javascript"><!--
$('input[name=\'category_name\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=notification/notification/autocomplete&token=<?php echo $this->session->data['token']; ?>&category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['category_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'category_id\']').val(item['value']);
    $('input[name=\'category_name\']').val(item['label']);
  }
});
-->
</script>
<script type="text/javascript">
     $(document).ready(function (e) {
        $("#fileToUpload").on('change',(function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append( 'image', $( '#fileToUpload' )[0].files[0] );
            $.ajax({
                type:'POST',
                url: 'index.php?route=notification/notification/uploadImage&token=<?php echo $this->session->data['token']; ?>',
                data:formData,
                cache:false,
                contentType: false,
                processData: false,
                complete: function() {
                    $('#send_button').removeAttr('disabled');
                },
                success:function(data){
                 $('#image_show_div img').attr('src',data);
                 $('.notification_img_path').val(data);
                }
            });
        }));
     });
</script>