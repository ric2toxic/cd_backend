<?php if(isset($all_filters) && !empty($all_filters)){
	foreach ($all_filters as $value){ 
		foreach ($value['filter'] as $child) { ?>
	    	<option value="<?php echo $child['filter_id']; ?>"><?php echo $value['name']; ?> > <?php echo $child['name']; ?></option>
	<?php } 
	}
} ?>