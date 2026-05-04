<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_inventoryreports; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_inventoryreports_list; ?></h3>
            </div>
            <div class="panel-body">
                <div class="well">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="input-invoice-date"><?php echo $entry_filter_date; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date" value="<?php echo $filter_date; ?>" placeholder="<?php echo $entry_filter_date; ?>" data-date-format="YYYY-MM-DD" id="input-invoice-date" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>

                        </div>
                        <br>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>

                                <button type="button" id="button-show" class="btn btn-primary pull-right" style="margin-right:10px"><i class="fa fa-search"></i> Show Result</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>






        <div class="table-responsive ajax-div hidden">
            
        </div>





    </div>


    <script type="text/javascript">
        $('#button-filter').on('click', function () {
        base_url = 'index.php?route=accounts/inventoryreports&token=<?php echo $token; ?>';
        var url = base_url;
        var filter_date_from = $('input[name=\'filter_date_from\']').val();
        if (filter_date_from) {
        url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
        }

        var filter_date_to = $('input[name=\'filter_date_to\']').val();
        if (filter_date_to) {
        url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
        }

        var filter_date = $('input[name=\'filter_date\']').val();
        if (filter_date) {
        url += '&filter_date=' + encodeURIComponent(filter_date);
        }

        var filter_invoice_date_to = $('input[name=\'filter_invoice_date_to\']').val();
        if (filter_invoice_date_to) {
        url += '&filter_invoice_date_to=' + encodeURIComponent(filter_invoice_date_to);
        }
        if (base_url == url) {
        alert(<?php echo "'" . $error_warning . "'"; ?>);
        } else {
        location = url;
        }

        });

        $(document).ready(function(){
            $('#button-show').click(function(){
                $('.ajax-div').html('');
                $('.ajax-div').addClass('hidden');
                var filter_date = $('#input-invoice-date').val();
                if(filter_date =='') {
                    alert("Please select atleast one filter.");
                    $('.ajax-div').addClass('hidden');
                    return false;
                } else {
                    $.ajax({
                        url: 'index.php?route=accounts/inventoryreports/getTotalAmountForOnlineInventoryReport&token=<?php echo $token; ?>&filter_date=' + filter_date,
                        dataType: 'json',
                        beforeSend:function(){
                            $('#button-show').button('loading');
                        },
                        complete:function(){
                            $('#button-show').button('reset');
                        },
                        success: function (json) {
                            
                            dataArr = jQuery.parseJSON(JSON.stringify(json));
                            console.log(dataArr);

                            $('.ajax-div').removeClass('hidden');
                            var htmlText = '';
                            htmlText += '<table class="table table-bordered table-hover" align="center"><thead><tr><td>Inventory</td><td>Date</td><td>Stock Total Taxable Amount</td><td>Stock Total Amount</td></tr></thead><tbody><tr><td>Online</td><td>'+ filter_date +'</td><td>'+ dataArr.total_taxable_value +'</td><td>'+ dataArr.total_amount +'</td></tr></tbody></table>';
                            
                            $('.ajax-div').html(htmlText);
                        }
                    });
                }
                
            });
        });
    </script>

    <!-- Auto complete scripts -->
    <script type="text/javascript">
        $('input[name=\'filter_order_no\']').autocomplete({
        'source': function (request, response) {
        $.ajax({
        url: 'index.php?route=accounts/inventoryreports/autocomplete&token=<?php echo $token; ?>&filter_order_no=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                response($.map(json, function (item) {
                return {
                label: item['order_no']
                }
                }));
                }
        });
        },
                'select': function (item) {
                $('input[name=\'filter_order_no\']').val(item['label']);
                }
        });
    </script>

    <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript">
    </script>
    <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
    <script type="text/javascript">
        $('.date').datetimepicker({
        pickTime: false
        });
    </script>
    <?php echo $footer; ?>
