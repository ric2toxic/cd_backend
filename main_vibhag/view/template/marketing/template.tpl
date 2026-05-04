<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_templates; ?></h3>
      </div>
      <div class="panel-body">
      <form action="<?php echo $form_action; ?>" class="form-horizontal" method="POST">
        <div class="well">
          <div class="row">
            <div class="col-sm-12">
            	<div class="form-group">
                <label class="control-label" for="input-template"><?php echo $template_title; ?></label>
                <input type="text" name="template_title" class="form-control"/>
              </div>
              <?php if ($error_template_title) { ?>
              <div class="text-danger"><?php echo $error_template_title; ?></div>
              <?php } ?>   
              <div class="form-group">
            	  <label class="control-label" for="input-template"><?php echo $template_text; ?></label>
            	    <textarea name="template" rows="5" placeholder="<?php echo $template_placeholder ?>" class="form-control"></textarea>
                  <span class="help-block"><?php echo $template_placeholder ?></span>
            	</div> 
              <?php if ($error_template) { ?>
              <div class="text-danger"><?php echo $error_template; ?></div>
              <?php } ?>   
            <button class="btn btn-primary pull-right" id="button-filter" type="submit"><i class="fa fa-submit"></i>Submit</button>             	      	      
         	  </div>
          </div>
        </div>
        </form>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $column_sr_no; ?></td>
                <td class="text-left"><?php echo $column_template_title; ?></td>
                <td class="text-left"><?php echo $column_template_text; ?></td>
              </tr>

            </thead>
            <tbody>
              <?php $srno=0;foreach ($admin_templates as $admin_template) { $srno++?>
              <tr>
                <td><?php echo $srno; ?></td>
                <td><?php echo $admin_template['title']; ?></td>
                <td>
                  <span id="template_text<?php echo $admin_template['template_id']; ?>" html-data="<?php echo $admin_template['template_id']; ?>" ><?php echo $admin_template['template']; ?>
                  </span>

                  <span style="cursor: pointer;" class="edit_template" html-data="<?php echo $admin_template['template_id']; ?>" id="edit_template_<?php echo $admin_template['template_id']; ?>"><i class="fa fa-pencil"></i>
                  </span>

                  <span style="display:none;" id="template_input_<?php echo $admin_template['template_id']; ?>"><input style="width: 500px;" type="text" id="template_val_<?php echo $admin_template['template_id']; ?>" value="<?php echo $admin_template['template']; ?>" /> 
                    <span style="cursor: pointer;" class="save_template" html-data="<?php echo $admin_template['template_id']; ?>"><i class="fa fa-save"></i>
                    </span> 
                    <span style="cursor: pointer;" class="cancel_template" html-data="<?php echo $admin_template['template_id']; ?>"><i class="fa fa-ban"></i>
                    </span>
                  </span>

                </td>
              </tr>
              <?php } ?>
      </div>
  	</div>
	</div>
 </div>
<script type="text/javascript">
  $(document).ready(function(){

      //Update Quantity
    $("span.edit_template").click(function(){
      var tid = $(this).attr("html-data");
      $(this).hide();
      $("span#template_text"+tid).hide();
      $("span#template_input_"+tid).show();
    });

    $("span.save_template").click(function(){
      $("span#template_input_"+$(this).attr("html-data")).hide();
      $("span#template_text"+$(this).attr("html-data")).html($("input#template_val_"+$(this).attr("html-data")).val());
      $("span#template_text"+$(this).attr("html-data")).show();
      $("span#edit_template_"+$(this).attr("html-data")).show();
      
      $.ajax({
        url : "index.php?route=marketing/template/updateTemplateText&token=<?php echo $this->session->data['token']; ?>",
        type: "post",
        dataType: "json",
        data: "template="+$("input#template_val_"+$(this).attr("html-data")).val()+"&template_id="+$(this).attr("html-data"),
        success: function( data ) {
          if(data.trim() == 'success'){
            //alert("here");
          }else{
            //alert("123");
          }
        }
      });
      
    });

    $("span.cancel_template").click(function(){
        $("span#template_input_"+$(this).attr("html-data")).hide();
        // var preval = $("span#template_text"+$(this).attr("html-data")).html
        // ();
        // $("input#quantity_val_"+$(this).attr("html-data")).attr("value",preval);
        $("span#template_text"+$(this).attr("html-data")).show();
        $("span#edit_template_"+$(this).attr("html-data")).show();
    });
});

</script>