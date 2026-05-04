<?php
echo $header;?>
<script src="view/javascript/jquery/jquery-ui.min_sortable.js"></script>
<?php
echo $column_left;
?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="form-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
        
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="merge_diffrent_design_product" class="form-horizontal">
          <input type="hidden" name="updateDesign" value="update">
              <div class="table-responsive">
                <ul id="sortable1" class="droptrue leftDiv">
                   <?php if(!empty($product_images)){
                    foreach($product_images as $key=>$productImage){
                    if(isset($productImage['product_image_id'])){
                      $productImageVal=$productImage['product_image_id'];
                      $productImageId=$productImage['product_image_id'];
                    }else{
                      $productImageVal='';
                      $productImageId='0';
                    }
                      if(empty($productImage['design_group_title']) || $productImage['design_group_title']==''){
                    ?> 
                    <li class="ui-state-default">
<img src="<?php echo $productImage['thumb'];?>" alt="" title="" data-placeholder="http://cdnimages.net/img/dw=100,dh=100,q=80/no_image.png">
<input name="product_image_id" value="<?php echo $productImageVal;?>" type="hidden">
<div class="imageGroupShortOrder">
<input type="checkbox" name="imageorder[<?php echo $productImageId;?>]" value="<?php echo $productImageId;?>"> <span><?php echo $front_image;?></span>
</div>
                    </li>                                    
                    <?php 
                  }
                      }
                  }?>
</ul>
 
<div class="rightDiv">
  <?php if(!empty($product_data['piece_in_set'])){
                    $j=$product_data['piece_in_set'];
                    for($i=1;$i<=$j;$i++){
                      $titleValue='';
                      $curentVals=$i-1;
                      if(isset($groupTitleArray[$curentVals])){
                        $titleValue=$groupTitleArray[$curentVals];
                      }
                      ?> 
                     <div class="ui-state-highlight">
                        <input type="text" name="product[<?php echo $i?>][design_group]" class="designGroupInput" rel="<?php echo $i;?>" value="<?php echo $titleValue;?>" placeholder="<?php echo $design_group_title;?>">
                        <div id="inputVals<?php echo $i;?>" class="inputValsDiv">
                          <?php 
                           $htmlInput='';
                            $t='0';
                          foreach($product_images as $key=>$productImage){
                          $curentVals=$i-1;
                          if(!empty($groupTitleArray) &&  !empty($groupTitleArray[$curentVals])  && !empty($productImage['design_group_title']) && $productImage['design_group_title']==$groupTitleArray[$curentVals]){
                          $htmlInput .='<input name="image['.$i.']['.$productImage['group_sort_order'].']" value="'.$productImage['product_image_id'].'" type="hidden">';
                          }
                          $t++;
                          }
                          echo $htmlInput;
                          ?>

                        </div>
                        <ul id="dragableArea<?php echo $i;?>" class="dragableArea">
                         <?php  
                          foreach($product_images as $key=>$productImage){
                          $curentVals=$i-1;
                          if(!empty($groupTitleArray) && !empty($groupTitleArray[$curentVals]) && !empty($productImage['design_group_title']) &&$productImage['design_group_title']==$groupTitleArray[$curentVals]){
                          //echo $productImage['design_group_title'].'<br>'.$groupTitleArray[$curentVals];
                            ?>
<li>
<img src="<?php echo $productImage['thumb'];?>" alt="" title="" data-placeholder="http://cdnimages.net/img/dw=100,dh=100,q=80/no_image.png">
<input name="product_image_id" value="<?php echo $productImage['product_image_id'];?>" type="hidden">
<div class="imageGroupShortOrder">
<input name="imageorder[<?php echo $productImage['product_image_id'];?>]" value="<?php echo $productImage['product_image_id'];?>" type="checkbox" <?php echo $productImage['front_image']=='1'?"checked":'';?>> <span><?php echo $front_image;?></span>
</div>
</li>
<?php }
}?>
                    

                        </ul>
                        <div class="clearboth"></div>
                     </div>
                    <?php 
                      }
                  }?>
 
</div>
            
             </div>
              <!-- <div class="multiple_image_button">
                <button class="multi_img_button btn btn-primary" type="Submit">Submit</button>
              </div> -->
            </form>
            
      </div>
    </div>
  </div>







  <script type="text/javascript">
    function updateInputValue(){
        $('.inputValsDiv').html('');
        $('.designGroupInput').each(function () {
          var divParentId=$(this).attr('rel');
          var event='0';
          var htmlAppend='';
          var checkedNum = $(this).parent().find('input[type="checkbox"]:checked').length;
          var checkedName = $(this).parent().find('input[type="checkbox"]:checked').val();
          var array_serial='1';
          var selectedItem=0;
          $(this).parent().find('li').each(function (index) {
            var curentNumber=index+parseInt('1');
            var imageId=$(this).find('input[name="product_image_id"]').val();
            if(checkedNum>0){
              if(checkedName==imageId || (checkedName=='0' && imageId=='')){
                var aarayNum='1';
                    selectedItem=curentNumber;
              }else{
                if(selectedItem=='0'){
                  var aarayNum=curentNumber+parseInt('1');
                }else{
                  var aarayNum=curentNumber;
                }
              }
            }else{
               var aarayNum=curentNumber;
            }
            htmlAppend +='<input type="hidden" name="image['+divParentId+']['+aarayNum+']" value="'+imageId+'" />';
          });
          $('#inputVals'+divParentId).html(htmlAppend);
        });
        //inputVals
    }

    $( function() {
    $( "ul.droptrue" ).sortable({
      connectWith: "ul"
    });
 
    $( "ul.dropfalse" ).sortable({
      connectWith: "ul",
      dropOnEmpty: false
    });
 
    $( "#sortable1, .dragableArea" ).disableSelection();
    $( ".dragableArea" ).sortable({
      connectWith: "ul",
      update: function( event, ui ) {
        ui.item.find('input[type="checkbox"]').removeAttr('checked');
        updateInputValue();
      }
    });

    $('input[type="checkbox"]').click(function(){
      var self=$(this);
      if($(this).is(':checked')){
      $(this).parent().parent().parent().find('input[type="checkbox"]').each(function () {
        if(self.attr('name')!=$(this).attr('name')){
          $(this).removeAttr('checked');
        }
      });
    }
    updateInputValue();
    });
    $('#form-button').click(function(){
      var error=0;
      $('.designGroupInput').each(function(index) {
        var currentVal=$.trim($(this).val());
        var cureentLiCount=$(this).parent().find('.dragableArea').find('li').length;
          if(currentVal =='' && cureentLiCount>0){
             error++;
          }
      });
      if($('#sortable1').find('li').length >0){
            alert('All images must be grouped before submit !!!');
            return false;
        }
      $('.dragableArea').each(function(index){
        var checked='0';
        var isCheckbox='0';
            $(this).find('input[type="checkbox"]').each(function () {
                  isCheckbox++;
                  if ($(this).is(":checked")) {
                    checked++;
                  }
            });
                if(isCheckbox > 0 && checked=='0'){
                  alert('Please select front image');
                  return false;
                }
      });

      if(error == 0){
      $('#merge_diffrent_design_product').submit();
      }else{
           alert('Please fill design group title field where add image');
      }
    });

    $('.designGroupInput').blur(function(){
      var this_val=$.trim($(this).val());
      var valueCount=0;
      $('.designGroupInput').each(function(index) {
        var currentVal=$.trim($(this).val());
          if(currentVal !=''){
             if(currentVal==this_val){
              valueCount++;
             }
          }
      });
      if(valueCount > 1){
        alert('please use diffrent title for design group');
        $(this).val('');
      }
    })
  });

</script>

 