<div class="modal-dialog">
  <!-- Pop-up content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title"><b>Add Custom Party Details</b></h4>
    </div>
   
    <div class="modal-body">
     <div id="msg" style="display: none;"></div>
            <label for="firm_name">Firm Name<span class="required_field"> *</span> : </label>
            <div class="">
                <input type="text" name="firm_name" class="form-control" id="firm_name" placeholder="Firm Name">
            </div>

          

            <label for="address1">Address1<span class="required_field"> *</span> : </label>
            <div class="">
                <input type="text" class="form-control" id="address1" placeholder="Address1" name="address1">
            </div>
        
          
        
            <label for="address2">Address2 : </label>
            <div class="">
                <input type="text" class="form-control" id="address2" placeholder="Address2" name="address2">
            </div>
       
            <label for="country">Country<span class="required_field"> *</span> : </label>
            <div>
                <select name="country" id="country">
                    <option value="0"><?php echo $text_select; ?></option>
                    <?php foreach($countries as $country){ ?>
                    <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <label for="zone">State / Zone<span class="required_field"> *</span> : </label>
            <div>
                <select name="zone" id="zone">
                    <option value="0"><?php echo $text_select; ?></option>
                </select>
            </div>

       
            <label for="city">City<span class="required_field"> *</span> : </label>
            <div class="">
                <input type="text" class="form-control" id="city" placeholder="City"  name="city">
            </div>
  
         
            
       
            <label for="postcode">Post Code<span class="required_field"> *</span> : </label>
            <div class="">
                <input type="text" class="form-control" id="postcode" placeholder="Post Code" name="postcode">
            </div>
       

       
            <label for="gst_num">GST Number<span class="required_field"> *</span> : </label>
            <div class="">
                <input type="text" class="form-control" id="gst_num" placeholder="GST Number" name="gst_num">
            </div>
        
          
    </div>
    <div class="modal-footer">
      <button type="button" id="save_custom_party" name="save_custom_party" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-primary" data-dismiss="modal" id="close-btn">Close</button>
    </div>
  </div>
</div>
