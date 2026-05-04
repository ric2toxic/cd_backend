<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-location" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-location" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-location_type"><?php echo $entry_location_type; ?></label>
            <div class="col-sm-10">
              <!--<input type="text" name="location_type" maxlength="2" value="<?php echo $location_type; ?>" placeholder="<?php echo $entry_location_type; ?>" id="input-location_type" class="form-control" />-->
              <select name="location_type" class="form-control">  
                  <option <?php if($location_type == 'store') echo 'selected'; ?> value="store"><?php echo "Store"; ?></option>
                  <option <?php if($location_type == 'office') echo 'selected'; ?> value="office"><?php echo "Office"; ?></option>
                  <option <?php if($location_type == 'franchise') echo 'selected'; ?> value="franchise"><?php echo "Franchise"; ?></option>
              </select>
            </div>
          </div>    
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-address"><?php echo $entry_address; ?></label>
            <div class="col-sm-10">
              <textarea type="text" name="address" placeholder="<?php echo $entry_address; ?>" rows="5" id="input-address" class="form-control"><?php echo $address; ?></textarea>
              <?php if ($error_address) { ?>
              <div class="text-danger"><?php echo $error_address; ?></div>
              <?php } ?>
            </div>
          </div>
            
          <div class="form-group">  
            <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
            <div class="col-sm-10">
              <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
              <?php if ($error_postcode) { ?>
              <div class="text-danger"><?php echo $error_postcode; ?></div>
              <?php } ?>
            </div>
          </div>   
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
            <div class="col-sm-10">
              <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
              <?php if ($error_city) { ?>
              <div class="text-danger"><?php echo $error_city; ?></div>
              <?php } ?>
            </div>
          </div>    
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-state"><?php echo $entry_state; ?></label>
            <div class="col-sm-10">
              <input type="text" name="state" value="<?php echo $state; ?>" placeholder="<?php echo $entry_state; ?>" id="input-state" class="form-control" />  
              <?php if ($error_state) { ?>
              <div class="text-danger"><?php echo $error_state; ?></div>
              <?php } ?>
            </div>
          </div>  
            
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="country" value="<?php echo $country; ?>" placeholder="<?php echo $entry_country; ?>" id="input-country" class="form-control" />  
                    <?php if ($error_country) { ?>
                    <div class="text-danger"><?php echo $error_country; ?></div>
                    <?php } ?>
                </div>
            </div>
           
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
            <div class="col-sm-10">
              <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
              <?php if ($error_telephone) { ?>
              <div class="text-danger"><?php echo $error_telephone; ?></div>
              <?php  } ?>
            </div>
          </div>
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-show-on"><?php echo $entry_show_on; ?></label>
            <div class="col-sm-10">
              <select name="show_on" class="form-control">  
                  <option <?php if($show_on == 'all') echo 'selected'; ?> value="all"><?php echo "All"; ?></option>
                  <option <?php if($show_on == 'storelocator') echo 'selected'; ?> value="storelocator"><?php echo "Storelocator"; ?></option>
                  <option <?php if($show_on == 'contact') echo 'selected'; ?> value="contact"><?php echo "Contact"; ?></option>
              </select>
              <?php if ($error_show_on) { ?>
              <div class="text-danger"><?php echo $error_show_on; ?></div> 
              <?php } ?>
            </div>
          </div> 
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geocode"><span data-toggle="tooltip" data-container="#content" title="<?php echo $help_geocode; ?>"><?php echo $entry_geocode; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="geocode" value="<?php echo $geocode; ?>" placeholder="<?php echo $entry_geocode; ?>" id="input-geocode" class="form-control" />
            </div>
          </div>  
          
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-dir_url"><span data-toggle="tooltip" data-container="#content" title="<?php echo $help_direction_url; ?>"><?php echo $entry_direction_url; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="direction_url" value="<?php echo $direction_url; ?>" placeholder="<?php echo $entry_direction_url; ?>" id="input-direction_url" class="form-control" />
              <?php if ($error_direction_url) { ?>
              <div class="text-danger"><?php echo $error_direction_url; ?></div>  
              <?php } ?>
            </div>
          </div>
            
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><span data-toggle="tooltip" data-container="#content"><?php echo $entry_status; ?></span></label>
            <div class="col-sm-10">
                <select name="status" class="form-control">  
                  <option <?php if($status == '1') echo 'selected'; ?> value="1"><?php echo "Enable"; ?></option>
                  <option <?php if($status == '0') echo 'selected'; ?> value="0"><?php echo "Disable"; ?></option>
                </select>
            </div>
          </div>  
            
          
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-image"><?php echo $entry_image; ?></label>
            <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
              <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-timing"><span data-toggle="tooltip" data-container="#content" title="<?php echo $help_timing; ?>"><?php echo $entry_timing; ?></span></label>
            <div class="col-sm-10">
              <textarea name="timing" rows="5" placeholder="<?php echo $entry_ph_timing; ?>" id="input-timing" class="form-control"><?php echo $timing; ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort_order"><span data-toggle="tooltip" data-container="#content" title="<?php echo $help_sort_order; ?>"><?php echo $entry_sort_order; ?></span></label>
            <div class="col-sm-10">
                <input type="text" name="sort_order" rows="5" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort_order" class="form-control" value="<?php echo $sort_order; ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-comment"><span data-toggle="tooltip" data-container="#content" title="<?php echo $help_comment; ?>"><?php echo $entry_comment; ?></span></label>
            <div class="col-sm-10">
              <textarea name="comment" rows="5" placeholder="<?php echo $entry_comment; ?>" id="input-comment" class="form-control"><?php echo $comment; ?></textarea>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
