 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="container">
  <div class="col-md-12" style="text-align:center;margin-bottom:10px">
      <h2>Shipping Estimator</h2>
  </div>  
  <div class="col-md-12">
  <form action="" id="shipping_estimator" style="width:50%;margin:auto">
    <div class="form-group">
      <label>Country</label>
      <select class="form-control" id="country"  name="country" onchange="get_state_list();">
        <?php
          foreach($countries as $values){ 
            if( $values['country_id'] == 99){ ?>
              <option value="<?php echo $values['country_id'];  ?>" selected ><?php echo $values['name']; ?></option>
        
        <?php }else{ ?>      
            <option value="<?php echo $values['country_id'];  ?>" ><?php echo $values['name']; ?></option>
        <?php
            }    
         }
        ?> 
      </select>  
    </div>
    <div class="form-group">
      <label>Pincode</label>
      <input type="text" class="form-control" id="pincode" placeholder="Enter Pincode" name="pincode" onblur="get_state();">
    </div>
    <div class="form-group">
      <label>Region/State</label>
      <select class="form-control" id="zone"  name="zone">
        <?php
          foreach($states as $values){ ?>
              <option value="<?php echo $values['zone_id'];  ?>" ><?php echo $values['name']; ?></option>
        <?php
            }    
        ?> 
      </select>  
    </div>
    <div class="form-group">
      <label>Weight(Kg)</label>
      <input type="select" class="form-control" id="weight"  name="weight" value='1'>
    </div>
    <button type="button" class="btn btn-default" onclick="get_shipping_estimator()">Submit</button>
  </form>
</div>
</ditext-align: -webkit-center>

<style>
  .shiiping-est{
      margin-bottom: 10px;
  }
</style>

<script>
  function get_state_list(){
    var country_id = $("#country").val();
    
    $.ajax({
        url:"<?php echo $get_state_list; ?>&country_id="+country_id,
        dataType:'json',
        success: function(json) {
                  html = '<option value="">--Select--</option>';

                  if (json['zone'] && json['zone'] != '') {
                    for (i = 0; i < json['zone'].length; i++) {
                      html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                      if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                        html += ' selected="selected"';
                      }

                      html += '>' + json['zone'][i]['name'] + '</option>';
                    }
                  } 
                  $('#zone').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

    });
  }

  function get_state(){
      var pincode = $("#pincode").val();
      
      $.ajax({
          type:"POST",
          data:"pincode="+pincode, 
          async : false,
          url:"<?php echo $get_state_through_pincode; ?>",
          dataType:'json',
          success:function(response){
            if(response.zone_id != ''){
              $("#zone").val(response.zone_id);
            }
          },
      });
  }

  function get_shipping_estimator(){
      var form = $("#shipping_estimator").serialize();
      $.ajax({
        type:'post',
        data:form,
        url:"<?php echo $shipping_estimator_url; ?>",
        success:function(json){
          var json =  $.parseJSON(json);
          if (!json['error']) {
				        $('#modal-shipping').remove();
				html  = '<div id="modal-shipping" class="modal">';
				html += '  <div class="modal-dialog">';
				html += '    <div class="modal-content">';
				html += '      <div class="modal-header">';
				html += '        <h4 class="modal-title">Shipping Estimator</h4>';
				html += '      </div>';
				html += '      <div class="modal-body" style="min-height: 100px;">';
        
       $.map(json['quote'], function(value, index) {
        html += '         <div class="col-sm-12 nopadding shiiping-est">'; 
         html +=  '<span class="col-sm-6">'+value.title+'</span>';                 
       
       
         html +=  '<span class="col-sm-6">'+value.text+'</span>';                 
         html += '      </div>';         
       });     
      
      html += '      </div>';
      html += '      <div class="modal-footer">';
      html += '        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
      html += '      </div>';
      html += '    </div>';
      html += '  </div>';
      html += '</div> ';    

    
				$('body').append(html);

				$('#modal-shipping').modal('show');

				
			}
        },
      });
      return false;
  }

</script>