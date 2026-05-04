<!-- indiaStoreOtherCountryAlert Modal -->
<div class="modal fade" id="indiaStoreOtherCountryAlert" role="dialog">
    <div class="modal-dialog modal-dialog-alert modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-danger">
                <?php if ($logo) { ?>
                    <img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive redirect_popup_logo" />
                <?php } else { ?>
                    <h3><?php echo $name; ?></h3>
                <?php } ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="col-sm-12">
                            <h3>Visiting from outside India? Looking to buy for your business?</h3>
                            <h3>Please visit our exclusive International WholesaleBox website.</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger btn-redirect" href="http://<?php echo INTERNATIONAL_STORE_HOST;?>"><strong>Go to Wholesalebox.co</strong></a>
            </div>
        </div>
    </div>
</div>