<?php if (isset($arr_selected_filter) && count($arr_selected_filter) > 0) { ?>
<div class="col-md-8 selected_filters">

    <?php

            if(isset($arr_selected_filter['filters']) && is_array($arr_selected_filter['filters']) && !empty($arr_selected_filter['filters'])){

                foreach($arr_selected_filter['filters'] as $filter){
    ?>
                    <span class="filter" data-id="<?php echo $filter['filter_id'];?>"><?php echo $filter['name']; ?><i class="fa fa-times"></i>
</span>

    <?php
                }
    ?>
    <?php
    if(count($arr_selected_filter['filters']) > 2 ){
    ?>
    <span class="filter clearall" data-id="all">Clear All&nbsp;<i class="fa fa-times"></i>
        <?php

            }
        ?>
    <?php
            }
        ?>
</div>
<?php } ?>