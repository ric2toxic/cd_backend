<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $return_page_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li>
          <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <i class="fa fa-list"></i> <?php echo $return_heading_title; ?>
        </h3>
      </div>
      <div class="panel-body">
        <form method="post" 
          enctype="multipart/form-data" 
          class="form-return" 
          name="form-return"
        >
          <input type="hidden" name="purchase_id" value="<?php echo $purchase_id?>">
          <input type="hidden" id="hidden_token" name="hidden_token" value="<?php echo $token;?>">
          <div class="table-responsive">
            <div class="btn-toolbar">
              <?php if(!empty($all_debit_notes)){ ?>
                <div class="col-lg-3 col-md-3">
                <div class="debit_note_dropdown">
                    <div class="selectme">---Select DebitNote---</div>
                    <div class="debit_note_dropdown-content">
                     <ul>
                       <?php foreach($all_debit_notes as $dn){?>
                          <li>
                            <a href="<?php echo $dn['download_dn'];?>">
                              <?php echo $dn['debit_note_prefix'].$dn['debit_note_no'];?>
                              <i class="fa fa-download" aria-hidden="true"></i>
                            </a>
                          <?php 
                           if(!empty($dn['debit_note_status'])) { ?>  
                            <span class="cancel_dn" data-id="<?php echo $dn['debit_note_id'] ?>">
                              &nbsp;|&nbsp;
                              <a href="javascript:void(0);"> 
                                Cancel
                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                              </a>
                            <span>
                            <?php } ?>
                        </li>
                      <?php } ?>
                    </ul>
                    </div>
                  </div>
                </div>
              <?php } ?>
              <button type="button" name="generate_btn" class="btn btn-primary pull-right dn_generate">
              <i class="fa fa-plus"></i> <?php echo $text_generate;?></button>
              <button type="button" name="preview_btn" class="btn btn-primary pull-right dn_preview">
              <i class="fa fa-file-pdf-o"></i> <?php echo $text_preview;?></button>
            </div><br>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td style="width: 4%;"><?php echo $column_sr_no; ?></td>
                  <td><?php echo $column_product; ?></td>
                  <td><?php echo $column_model; ?></td>
                  <td>Seller</td>
                  <td><?php echo $column_purchase_firm; ?></td>
                  <td><?php echo $column_seller_firm; ?></td>
                  <td><?php echo $column_purchase_price; ?></td>
                  <td><?php echo $column_purchase_qty; ?></td>
                  <td><?php echo $column_dn_qty; ?></td>
                  <td><?php echo $column_remaining_qty; ?></td>
                  <td><?php echo $column_dn_generate; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if($wsb_products){ ?>
                <?php $row = 0;?>
                <?php foreach ($wsb_products as $product) { ?>
                  <?php $product_id = $product['product_id'];?>
                  <?php $row++;?>
                  <?php
                    $str_class = '';
                    if(!empty($product['dn_qty']) && $product['dn_qty'] > 0){
                      $str_class = "bg-danger";
                    }
                  ?>
                  <tr class="parent <?php echo $str_class;?>" data-product_id="<?php echo $product_id; ?>" >
                    <td style="width: 4%;" class="text-right"><?php echo $row; ?></td>
                    <td class="text-left">
                      <img
                        src="<?php echo
                        $pid_to_imgs[$product['product_id']]; ?>"
                        width="<?php echo $image_width; ?>px"
                        height="<?php echo $image_height; ?>px"
                      /><br/>
                      (<?php echo $text_product_id.': <b>'.$product_id.'</b>';?>)<br>
                      (<?php echo $text_breakup_id.': <b>'.$product['breakup_id'].'</b>';?>)
                    </td>
                    <td class="text-left">
                      <?php echo $product['model']; ?><br/>
                      (<?php echo $product['sku']; ?>)
                    </td>
                    <td class="text-left"><?php echo $product['nickname']; ?></td>
                    <td class="text-left"><?php echo $product['purchase_firm']; ?></td>
                    <td class="text-left"><?php echo $product['seller_firm']; ?></td>
                    <td class="text-left"><?php echo $product['transfer_price_per_piece']; ?></td>
                    <td class="text-left"><?php echo $product['total_qty']; ?></td>
                    <td class="text-left"><?php echo !empty($product['dn_qty'])? '<b>'.$product['dn_qty'].'</b>' : 0; ?></td>
                    <td class="text-left">
                      Total Sets = <?php echo $product['product_qty']; ?>, <br>
                      Pieces In Set = <?php echo $product['piece_in_set']; ?> <br><br>
                      Total Pieces: <b><?php echo $product['remaining_qty']; ?></b>
                    </td>
                    <td class="text-left">
                     <?php if(!empty($product['is_returnable']) && $product['returnable_qty'] > 0 ){ ?>
                      <input type="checkbox" name="dn_check[<?php echo $product['breakup_id'];?>]">
                      <input type="number" 
                        name="dn_qty[<?php echo $product['breakup_id'];?>]" 
                        min="0" 
                        max="<?php echo $product['returnable_qty'];?>" 
                        value="<?php echo $product['returnable_qty'];?>"
                        data-previous="<?php echo $product['returnable_qty'];?>" 
                        class="form-control check_qty">
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" id="show_preview" style="display: none;" data-target="#dn_preview">Preview</button>
<!-- WSB Purchase Debit Note Preview -->
<div id="dn_preview" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">DebitNote Preview</h4>
      </div>
      <div class="modal-body" id="preview_body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?php echo $footer; ?>
<script type="text/javascript">
  var token = $('#hidden_token').val();
  $('.dn_preview').click(function(){
    var data = $(".form-return").serialize();
    $.ajax({
      type: 'POST',
      data: {
              'data' : data
            },
      url: 'index.php?route=wsb_purchase/purchase_return/getDebitNotePreview&token='+token,
      dataType: 'json',
      async: false,
      complete: function(json) {
        $('#preview_body').html(json.responseText);
        $('#show_preview').click();
      },
    });
  });

  $('.dn_generate').click(function(){
    alert('Update Product Quantity manually after generating DebitNote.');
    var data = $(".form-return").serialize();
    $.ajax({
      type: 'POST',
      data: {
              'data' : data
            },
      url: 'index.php?route=wsb_purchase/purchase_return/getGenerateDebitNote&token='+token,
      async: false,
      complete: function(json) {
        var responseText = $.trim(json.responseText);
        if(responseText.length > 0){
          $('#preview_body').html(json.responseText);
          $('#show_preview').click();
        }else{
          location.reload();
        }
      },
    });
  });

  $('.dn_list').change(function(){
    var dn_id = $(this).val();
    if(dn_id > 0){
      $.ajax({
        type: 'POST',
        data: {
                'dn_id' : dn_id
              },
        url: 'index.php?route=wsb_purchase/purchase_return/downloadDebitNote&token='+token,
        dataType: 'json',
        async: false,
        complete: function(json) {
          window.location.assign(json.responseText);
        },
      });
    }
  });

  $('.cancel_dn').click(function(){
    var dn_id = $(this).data('id');
    var obj = $(this);
    if(dn_id > 0){
      $.ajax({
        type: 'GET',
        data: {
                'dn_id' : dn_id
              },
        url: 'index.php?route=wsb_purchase/purchase_return/cancelDebitNote&token='+token,
        dataType: 'json',
        async: false,
        success:function(json){
          window.location.assign(json);
          obj.hide();
          //reload page after 2 second
          setTimeout(function(){
            location.reload();
          },2000);
        },
        complete: function(json) {
          //location.reload();
        },
        error:function(xhr, ajaxOptions, thrownError){
          var string = xhr.responseText.replace(/<b>/g,'');
            string = string.replace(/<\/b>/g,'');
            alert("Error::\n"+string);
        }
      });
    }
  })

$('.check_qty').on('blur',function(){
  var qty = $(this).val();
  var previous = $(this).data('previous');
  if(qty <= 0) {
     alert('Wrong quantity entered');
     $(this).val(previous);
  }
})



</script>