 <div class="panel-body">

   
    <input type="hidden" name="pickup_id" value="<?php echo $pickup_id; ?>">
    <div class="form-group required">
       <div class="selected_filtar col-sm-12">
          <ul>
              <?php foreach($seller_list as $seller) { ?>
                <li class="filter" data-id="<?php echo $seller['seller_id']; ?>">
                   <input type="hidden" name="sellers[]" value="<?php echo $seller['seller_id']; ?>">
                  <?php echo $seller['company']; ?> 
                  (<?php echo $seller['nickname']; ?>)<label class="filtar_close_icon"></label>
                </li>
              <?php } ?>
          </ul>
       </div>
    </div>

    <div class="form-group required">
     <label class="col-sm-4 control-label">Select Pickup Associate</label>
      <div class="col-sm-8">
        <select name="assign_id"  class="form-control edit_track">
          <option value="">Select pickup associate</option>
          <?php foreach($pickup_list as $pickup) { ?>
            <option value="<?php echo $pickup['id']; ?>"><?php echo $pickup['first_name'].' '.$pickup['last_name']; ?></option>
          <?php } ?>
         </select>
      </div>
    </div>

    <div class="clear"></div>
    
    <div class="form-group required">
     <label class="col-sm-4 control-label">Start Date</label>
      <div class="col-sm-8">
         <div class="input-group date">
            <input type="text" name="start_date" value="" placeholder="Start Date" data-date-format="YYYY-MM-DD" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
            </span>
         </div>
      </div>
   </div>

    <div class="clear"></div>
   
   <div class="form-group required">
     <label class="col-sm-4 control-label">End Date</label>
      <div class="col-sm-8">
         <div class="input-group date">
            <input type="text" name="end_date" value="" placeholder="End Date" data-date-format="YYYY-MM-DD" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
            </span>
         </div>
      </div>
    </div> 


</div>        