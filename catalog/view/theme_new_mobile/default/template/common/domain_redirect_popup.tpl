<!-- indiaStoreOtherCountryAlert Modal -->
<div class="modal fade" id="indiaStoreOtherCountryAlert" role="dialog">
    <div class="modal-dialog modal-dialog-alert modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-danger">
                <?php if ($logo) { ?>
                    <img src="<?php echo $logo; ?>" class="img-responsive logo" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" width="130" style="margin:5px 0px;" />
                <?php } else { ?>
                    <h3><?php echo $name; ?></h3>
                <?php } ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="col-sm-12">
                            <p>Visiting from outside India? Looking to buy for your business?</p>
                            <p>Please visit our exclusive International WholesaleBox website.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger btn-redirect" href="http://<?php echo INTERNATIONAL_STORE_HOST;?>">Go to Wholesalebox.co</a>
            </div>
        </div>
    </div>
</div>