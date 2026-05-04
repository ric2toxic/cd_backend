
    <div class="panel panel-default desktop_only" id="filter_bar">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-6">
                <div class="panel-heading filter_click">Refine Search</div>
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
                        <a class="list-group-item ">Rating</a>
                        <div class="list-group-item">
                            <div class="filter-group">
                                <div class="rating radio rating_filter">
                                    <?php foreach($filter_facets['rating'] as $rating) {
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
                                    <?php } ?>
                                    <label><input id="rating_filter_all" type="radio" name="rating_filter[]" value="all"/> All</label>
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
                            <a class="list-group-item fg_title_<?php echo $filter_group['filter_group_id'];?>"><?php echo $filter_group['group_label'];?></a>
                            <div class="list-group-item lgi_<?php echo $filter_group['filter_group_id'];?>">
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
                            <a class="list-group-item ">Stock</a>
                            <div class="list-group-item">
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
</column>

