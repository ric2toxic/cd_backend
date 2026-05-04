<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
<h1><?php echo $heading_title; ?></h1>
            </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
</div>

            <div class="well">
                <div class="row">
                   <!-- <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="input-date"><?php echo $entry_date; ?></label>
                            <div class="input-group date">
                                <input type="text" name="filter_date" value="<?php echo $filter_date; ?>" placeholder="<?php echo $entry_date; ?>" data-date-format="YYYY-MM-DD" id="input-date" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
                        </div>
                    </div> -->
                    <div class="col-sm-12">
                        <div class="form-group col-sm-6">
                            <label class="control-label" for="input-seller"><?php echo $entry_seller; ?></label>
                            <input type="text" placeholder="<?php echo $entry_seller; ?>" name="filter_seller" value="<?php echo $filter_seller; ?>" id="input-seller" class="form-control" />
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="control-label" for="input-product"><?php echo $entry_product; ?></label>
                            <input type="text" placeholder="<?php echo $entry_product; ?>" name="filter_product" value="<?php echo $filter_product; ?>" id="input-product" class="form-control" />
                        </div>
                        <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td class="text-left"><?php echo $column_nickname; ?></td>
                        <td class="text-left"><?php echo $column_changed; ?></td>
                        <td class="text-left"><?php echo $column_product; ?></td>
                        <td class="text-left"><?php echo $column_modified; ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($activities) { ?>
                    <?php foreach ($activities as $activity) { ?>
                    <tr>
                        <td class="text-left"><?php echo $activity['nickname']; ?></td>
                        <td class="text-left"><?php echo $activity['updated_type']; ?></td>
                        <td class="text-left"><?php echo $activity['product_id']; ?></td>
                        <td class="text-left"><?php echo $activity['modified']; ?></td>
                    </tr>
                    <?php } ?>
                    <?php } else { ?>
                    <tr>
                        <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
        </div>
    </div>
    </div>

<script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        url = 'index.php?route=report/seller_activity&token=<?php echo $token; ?>';
        var filter_seller = $('input[name=\'filter_seller\']').val();

        if (filter_seller) {
            url += '&filter_seller=' + encodeURIComponent(filter_seller);
        }
        var filter_product = $('input[name=\'filter_product\']').val();

        if (filter_product) {
            url += '&filter_product=' + encodeURIComponent(filter_product);
        }

        var filter_date = $('input[name=\'filter_date\']').val();

        if (filter_date) {
            url += '&filter_date=' + encodeURIComponent(filter_date);
        }

        location = url;
    });
    //--></script>

<?php echo $footer; ?>