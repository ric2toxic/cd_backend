<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_create_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <form id="form_id" action="index.php?route=app/dynamic_layout/saveLayout&token=<?php echo $token; ?>" method="POST">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $heading_create_title; ?></h3>
                <div class="form-group pull-right">
                    <button class="" submit="" class="btn btn-default"><?php echo $text_save_layout; ?></button>
                </div>
            </div>
        </div>
        <input type="hidden" name="layout_index" value="<?php echo $layout_index; ?>">
        <!-- Layout -->
        <div class="panel-body Layout">
            <span><h3 class="panel-title">Layout's</h3></span>
             <div class="well">
                <div class="row">
                    <div class="col-sm-12">
                    <?php foreach($layout_arr as $key => $val) { ?>
                        <div class="col-sm-3" style="border: 1px solid #ccc!important; border-radius: 16px;">
                            <span><?php echo $val; ?></span>
                            <?php $layout_cheked = (isset($layout_detail['layout_id']) && !empty($layout_detail['layout_id']) && $layout_detail['layout_id'] == $key)?'checked="checked"':''; ?>
                            <input class="pull-right radio_layout" type="radio" name="radio_layout" value="<?php echo $key; ?>" <?php echo $layout_cheked; ?> >
                        </div>
                    <?php } ?>
                    </div>
                </div><hr>
                <div id="dynamic_layout">
                </div>
            </div>
        </div>
        <!-- Module -->
        <div class="panel-body Module">
            <span><h3 class="panel-title">Module's</h3></span>
             <div class="well">
                <div class="row">
                    <?php foreach($module_arr as $key => $val) { ?>
                        <div class="col-sm-3" style="border: 1px solid #ccc!important; border-radius: 16px;">
                            <span><?php echo $val; ?></span>
                            <?php $module_cheked = (isset($layout_detail['module_id']) && !empty($layout_detail['module_id']) && $layout_detail['module_id'] == $key)?'checked="checked"':''; ?>
                            <input class="pull-right radio_module" type="radio" name="radio_module" value="<?php echo $key; ?>" <?php echo $module_cheked; ?> >
                        </div>
                    <?php } ?>
                </div><hr>
                <div id="dynamic_module">
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
    var layout_index = "<?php echo $layout_index; ?>";
    $(document).ready( function(){
        $('#form_id').submit(function() {
            if($('.radio_layout').is(':checked') == false){
                alert("Please Select layout");
                return false;
            }
            if($('.radio_module').is(':checked') == false){
                alert("Please Select Module");
                return false;
            }
        });

        if(layout_index != 0){
            let layout_name   = "<?php echo $layout_detail['layout_id']; ?>";
            let module_name   = "<?php echo $layout_detail['module_id']; ?>";
            layout_ajax(layout_name);
            module_ajax(module_name);
        }

        $('input:radio[name="radio_layout"]').change( function(){
            let layout_name = $(this).val();
            if ($(this).is(':checked') && layout_name != 0 && layout_name !='' ) {
                layout_ajax(layout_name);
            }
        });

        $('input:radio[name="radio_module"]').change( function(){
            let module_name = $(this).val();
            if ( $(this).is(':checked') && module_name != 0 && module_name != '' ) {
                module_ajax(module_name);
            }
        });
    });

    function layout_ajax(layout_name) {
        $.ajax({
            url: 'index.php?route=app/dynamic_layout/getLayoutTpl&token=<?php echo $token; ?>',
            data: '&layout_name='+ layout_name+'&layout_index='+layout_index,
            type: 'post',
            success: function(json) {
                $('#dynamic_layout').html(json);
            }
        });
    }

    function module_ajax(module_name) {
        $.ajax({
            url: 'index.php?route=app/dynamic_layout/getModuleTpl&token=<?php echo $token; ?>',
            data: '&module_name='+ module_name+'&layout_index='+layout_index,
            type: 'post',
            success: function(json) {
                $('#dynamic_module').html(json);
            }
        });
    }
</script>