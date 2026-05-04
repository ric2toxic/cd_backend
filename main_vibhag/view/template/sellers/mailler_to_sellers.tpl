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

    <div class="container-fluid mailer_to_seller">
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($failure) { ?>
        <div class="alert alert-danger"><i class="fa fa-check-circle"></i> <?php echo $failure; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_title; ?> </h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <fieldset>
                        <!--<legend>Add Message</legend>-->
                        <form class="form-horizontal" action="index.php?route=sellers/mailler_sellers/sendMailSeller&token=<?php echo $this->session->data['token']; ?>" enctype="multipart/form-data" method="post">
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <?php if($sellers_list){ ?>
                                        <div class="checked_sellers col-sm-12">
                                            <label class="pull-left">
                                                <input type="checkbox" name="select_all_sellers" value="" class=""  /><span class="all_seller"> <?php echo $text_select_all_sellers; ?></span>
                                            </label>
                                            <label class="pull-right col-sm-4">
                                                <select name="seller_zone" class="seller_zone check_condition" id="seller_zone">
                                                        <option value="0">Select Zone</option>
                                                        <?php foreach($seller_zone as $zone => $val){ ?>
                                                            <option value="<?php echo $val; ?>"><?php echo $zone; ?></option>
                                                        <?php } ?>
                                                </select>
                                            </label>
                                            <label class="pull-right col-sm-4">
                                                <select name="active_sellers" class="active_sellers check_condition " id="active_sellers">
                                                        <option value="0">Select Status</option>
                                                        <option value="1">Active</option>
                                                        <option value="2">Inactive</option>
                                                        <option value="3">Disabled</option>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="clearfix"></div>

                                        <select class="form-control mailler_sellers_list" name="sellers_list[]" multiple>
                                            <?php foreach($sellers_list as $sellers){ ?>
                                                <option value="<?php echo $sellers['seller_id']?>" class="active_status_<?php echo $sellers['seller_status']; ?> seller_zone_<?php echo $sellers['zone_code']; ?> sellers"><?php echo $sellers['company']?> (<?php echo $sellers['nickname'];?>) </option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>

                                </div>
                                <div class="col-sm-1"></div>
                                <div class="col-sm-6">
                                    <div class="addtional_emails_area">
                                        <label class=""  for=""><?php echo $text_additional_cc;?></label>
                                        <textarea class="form-control" name="additional_cc_emails" placeholder="<?php echo $text_additional_cc;?>" rows="4"></textarea>
                                    </div>
                                    <div class="addtional_emails_area">
                                        <label class=""  for=""><?php echo $text_additional_bcc; ?></label>
                                        <textarea class="form-control" name="additional_bcc_emails" placeholder="<?php echo $text_additional_bcc; ?>" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="control-label" for=""><?php echo $text_subject; ?></label>
                                    <input type="text" class="form-control" name="mail_subject" placeholder="<?php echo $text_subject; ?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="control-label" for=""><?php echo $text_message; ?></label>
                                    <textarea name="msg_sellers" rows="8" placeholder="<?php echo $text_message; ?>" id="mailler_sellers" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-primary notification_for_all" id="send_button" data-loading-text="Loading..." ><i class="fa fa-paper-plane"></i> <?php echo $button_send; ?></button>
                            </div>
                            <div class="pull-left">
                                <div class="fileUpload btn btn-primary">
                                    <input type="file" class="upload" id="button-attachment" name="files[]" value="" multiple>
                                </div>
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
    $('#mailler_sellers').summernote({
        height: 300,
        onpaste: function (e) {
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            document.execCommand('insertText', false, bufferText);
        }
    });

    $('input[name=\'select_all_sellers\']').click(function(){
        if($(this).prop('checked')){
            $('select[name=\'sellers_list[]\'] option').prop('selected',true);
            $('.checked_sellers span.all_seller').text(' <?php echo $text_unselect_all_sellers; ?>');
            $('input[name=\'active_sellers\']').prop('checked',false);
            $('select[name="active_sellers"] option:first').prop('selected', 'selected');
            $('select[name="seller_zone"] option:first').prop('selected', 'selected');
        }else{
            $('select[name=\'sellers_list[]\'] option').prop('selected',false);
            $('.checked_sellers span.all_seller').text(' <?php echo $text_select_all_sellers; ?>');
        }
    });


    $('.check_condition').change(function(){
        var select_status   = $('.active_sellers').val();
        var seller_zone     = $('.seller_zone').val();
    
        if(select_status != 0 || seller_zone != 0) {
            let class_name = '';
            $('select[name=\'sellers_list[]\'] option').prop('selected',false);
            
            if(select_status != 0){
                class_name = '.active_status_'+select_status;
            }
            if(seller_zone != 0){
                class_name += '.seller_zone_'+seller_zone;
            }
            if(select_status != 0 || seller_zone != 0){
                $(class_name).prop('selected',true);
            }
            
            $('input[name=\'select_all_sellers\']').prop('checked',false);
            $('.checked_sellers span.all_seller').text(' <?php echo $text_select_all_sellers; ?>');
        }else{
            $('select[name=\'sellers_list[]\'] option').prop('selected',false);
            $('.sellers').prop('selected',false);
        }
    });
</script>