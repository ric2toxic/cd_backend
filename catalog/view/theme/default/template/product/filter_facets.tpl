<column id="column-left" class="col-sm-2">
  <div class="colume_left_shadow">
    <div class="panel-default desktop_only" id="filter_bar">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-6">
                <div class="discount_box">
                    <div class="discount_text"><i class="fa fa-tag"></i> <?php echo $text_discount_on_order;?></div>
                    <div class="discount_text" style="padding-top:5px"><i class="fa fa-tag"></i> <?php echo $text_discount_above_order;?></div>
                </div>
                <?php
                    $arr_filter_ids = array();
                    $arr_rating_filter = array();

                    if (isset($filter_data['filter_filter']) && $filter_data['filter_filter'] != '') {
                        $arr_filter_ids = explode(",", $filter_data['filter_filter']);
                    }
                    if (isset($filter_data['rating_filter']) && $filter_data['rating_filter'] != '') {
                        $arr_rating_filter = explode(",", $filter_data['rating_filter']);
                    }


                ?>
                <?php if (isset($filter_facets['rating']) && count($filter_facets['rating']) > 0 ) { ?>
                <div class="filter_box">
                    <div class="list-group" id="list-group-filter">
                        <a class="list-group_title ">Rating</a>
                        <div class="list-group-item_new">
                            <div class="filter-group">
                                <div class="rating radio rating_filter">
                                    <!--<?php foreach($filter_facets['rating'] as $rating) {
                                        if (in_array($rating, $arr_rating_filter)) {
                                            $selected = ' checked = "checked" ';
                                        } else {
                                            $selected = '';
                                        }
                                    ?>
                                    <label>
                                        <input <?php echo $selected; ?> id="rating_filter_<?php echo $rating; ?>" type="radio" name="rating_filter[]" value="<?php echo $rating; ?>" />
                                        <?php if ($rating != 'all') { ?>
                                            <?php for ($j = 1; $j <= (int)$rating; $j++) { ?>
                                            <span class="fa fa-stack">
                                            <i class="fa fa-star fa-stack-1x"></i>
                                            <i class="fa fa-star-o fa-stack-1x"></i>
                                            </span>
                                            <?php } ?>
                                        <?php } ?>
                                    </label> <br/>
                                    <?php } ?>-->

                                    <?php
                                            if (in_array("5", $arr_rating_filter)) {
                                                $selected_five = ' checked = "checked" ';
                                            } else {
                                                $selected_five = '';
                                            }

                                             if (in_array("4", $arr_rating_filter)) {
                                                $selected_four = ' checked = "checked" ';
                                            } else {
                                                $selected_four = '';
                                            }

                                             if (in_array("3", $arr_rating_filter)) {
                                                $selected_three = ' checked = "checked" ';
                                            } else {
                                                $selected_three = '';
                                            }
                                    ?>
                                    <label>
                                        <input <?php echo $selected_five; ?> id="rating_filter_5" type="radio" name="rating_filter[]" value="5" style="top:2px"/>
                                        <span class='label label-success' style='padding:4px; margin:3px 0px; font-size: 12px;'>Excellent Quality</span>
                                    </label>
                                    <label>
                                        <input <?php echo $selected_four; ?> id="rating_filter_4" type="radio" name="rating_filter[]" value="4" style="top:2px"/>
                                        <span class='label label-warning' style='padding:4px; margin:3px 0px; font-size: 12px;'>Good Quality</span>
                                    </label>
                                    <label>
                                        <input <?php echo $selected_three; ?> id="rating_filter_3" type="radio" name="rating_filter[]" value="3" style="top:2px"/>
                                        <span class='label label-danger' style='padding:4px; margin:3px 0px; font-size: 12px;'>Average Quality</span>
                                    </label>
                                    </br>
                                    <label><input id="rating_filter_all" type="radio" name="rating_filter[]" value="all" style="top:0px"/> <strong>All</strong></label>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="filter_box">
                <?php if (isset($filter_facets['filters']) && count($filter_facets['filters']) > 0) { ?>
                <?php foreach ($filter_facets['filters'] as $filter_group) { ?>

                    <div class="filter_box">
                        <div class="list-group" id="list-group-filter">
                            <a class="list-group_title list-group_titlefg_title_<?php echo $filter_group['filter_group_id'];?>"><?php echo $filter_group['group_label'];?></a>
                            <div class="list-group-item_new lgi_<?php echo $filter_group['filter_group_id'];?>">
                                <div id="filter-group<?php echo $filter_group['filter_group_id'];?>">
                                    <div class="filter_group_description">
                                        <?php echo $filter_group['group_description'];?>
                                    </div>
                                    <?php foreach($filter_group['filter'] as $filter) { ?>
                                    <div class="checkbox">
                                        <label>
                                            <?php

                                                if (in_array($filter['filter_id'], $arr_filter_ids)) {
                                                    $selected = ' checked = "checked" ';
                                                } else {
                                                    $selected = '';
                                                }
                                            ?>
                                            <input <?php echo $selected; ?> id="filter<?php echo $filter['filter_id']; ?>" name="filter[]" value="<?php echo $filter['filter_id']; ?>" type="checkbox">
                                            <?php echo $filter['name']; ?>
                                        </label>
                                    </div>
                                    <?php } //end foreach of filter ?>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
                <?php } ?>

                <?php if (isset($filter_facets['stock_filters']) && count($filter_facets['stock_filters']) > 0) { ?>
                    <div class="filter_box">
                        <div class="list-group" id="list-group-filter">
                            <a class="list-group_title ">Stock</a>
                            <div class="list-group-item_new">
                                <div class="filter-group">
                                    <div class="stock radio stock_filter">
                                        <?php foreach($filter_facets['stock_filters'] as $stock_filter_label=>$stock_filter) {
                                        if ($arr_selected_filter['stock_filter'] == $stock_filter) {
                                            $selected = ' checked = "checked" ';
                                        } else {
                                            $selected = '';
                                        }
                                    ?>
                                        <label>
                                            <input <?php echo $selected; ?> id="stock_filter_<?php echo $stock_filter; ?>" type="radio" name="stock_filter[]" value="<?php echo $stock_filter; ?>" />

                                            <span class="">
                                                <?php echo $stock_filter_label; ?>
                                            </span>

                                        </label> <br/>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                </div>
            </div>
        </div>
    </div>
 </div>
</column>

