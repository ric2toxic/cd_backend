

<div class="bg_color_white category-title_update">

    <h2 class="" id="search_result_heading">
        <?php echo $search_result_heading; ?>
    </h2>
    <div class="col-md-12  text-right desktop_only">
        <div class="range_sort">
            <ul id="input-sort" class="sort_panel_bg" style="display:inline-block;">
                <li class="sortBy">Sort By:</li>
                <?php foreach ($sorts as $sort) { ?>

                 <li class="<?php echo $sort['selected']; ?>"><a href="javascript:void(0);" data-value="<?php echo $sort['query_string']; ?>" ><?php echo $sort['text']; ?></a></li>

                <?php } ?>

                <?php if (isset($filter_facets['stock_filters'])
                        && count($filter_facets['stock_filters']) > 0) { ?>

                <?php } ?>
            </ul>
        </div>
    </div>

        <div class="dropp">
            <?php

                if ($arr_selected_filter['stock_filter'] == 1) {
                    $selected = ' All Stock';
                } else {
                    $selected = 'In Stock';
                }
            ?>
            <div class="dropp-header"> <span class="dropp-header__title js-value"><?php echo $selected; ?></span> <a href="#" class="dropp-header__btn js-dropp-action"><i class="icon"></i></a> </div>
            <div class="dropp-body">
                <?php foreach($stock_filters as $stock_filter_label=>$stock_filter) {

                ?>
                <label for="opt<?php echo $stock_filter; ?>"><?php echo $stock_filter_label; ?>
                    <input type="radio" id="opt<?php echo $stock_filter; ?>" name="stock_filter[]" value="<?php echo $stock_filter; ?>"/>
                </label>

                <?php } //end foreach ?>
            </div>
        </div>

    </div>
