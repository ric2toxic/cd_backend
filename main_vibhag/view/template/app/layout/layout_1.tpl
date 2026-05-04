<div class="row">
    <div class="col-sm-12 form-group"> 
        <div class="col-sm-3"> 
            <span> Enter Title: </span> 
            <input class="form-control" type="textbox" name="layout[layout_title]" placeholder="Enter Layout Title" value="<?php echo isset($layout["layout_title"])?$layout["layout_title"]:''; ?>"> 
        </div>
        <div class="col-sm-3"> 
            <span> Is show layout Title: </span> 
            <select class="form-control" name = "layout[is_show_layout_title]">
                <?php $is_show_layout_title = ($layout["is_show_layout_title"] == 0)?'selected="selected"':''; ?>
                <option value="1" >Yes</option>
                <option value="0" <?php echo $is_show_layout_title; ?> >NO</option>
            </select> 
        </div>
        <div class="col-sm-3"> 
            <span> Is single image: </span>
            <select class="form-control" name = "layout[is_single_image]">
                <?php $is_single_image = ($layout["is_single_image"] == 0)?'selected="selected"':''; ?>
                <option value="1" >Yes</option>
                <option value="0" <?php echo $is_single_image; ?> >NO</option>
            </select> 
        </div> 
        <div class="col-sm-3">
            <span> Status: </span>
            <select class="form-control" name = "layout[status]">
                <?php $status = ($layout["status"] == 0)?'selected="selected"':''; ?>
                <option value="1" >Active</option>
                <option value="0" <?php echo $status; ?> >Inactive</option>
            </select>
        </div>
    </div>
</div>