<?php echo $header; ?>
<div class="container">

    <div class="big-heading">
        <h1><?php echo $ms_account_register_seller; ?></h1>
    </div>
    <div class="stepwizard col-md-offset-3">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step">
                <a href="#step-1" type="button" class="btn btn-primary btn-circle" disabled="disabled">1</a>
                <p>Create Account</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-2" type="button" class="btn btn-primary btn-circle" disabled="disabled" >2</a>
                <p>Seller Information</p>
            </div>
            <div class="stepwizard-step">
                <a href="#step-3" type="button" class="btn btn-primary btn-circle" >3</a>
                <p>Congratulations!</p>
            </div>
        </div>
    </div>


    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="ms-product <?php echo $class; ?> ms-account-profile card_box"><?php echo $content_top; ?>
            <div class="signup-form-wrapper">
                <div class="form-block-heading">Congratulations!!</div>

                <div class="row contentblock">
                    <div class="col-md-7">

                        <h3>Your seller account has been created but it is under review!</h3>

                        <p>
                            Please email the following documents on <a href="mailto:info@wholesalebox.in">info@wholesalebox.in</a> to enable us to approve your seller account through which you can wholesale on this platform WHOLESALEBOX:
                        </p>
                        <ol>
                            <li>
                             Your PAN Card copy
                            </li>

                            <li>
                                Your VAT / CST registration no. copy
                            </li>
                            <li>
                                Cancelled cheque copy of your business account
                            </li>
                        </ol>
                        <p>
                            Once we receive and review these documents, your account will go live within 24 hours.
                        </p>
                        <p>
                        If you have ANY questions about seller registration, you can call on <a href="tel:+917221063279" >+91 72210 63279</a>.
                        </p>
                        <p>
                            A confirmation will be sent to the provided e-mail address once your account is approved.
                        </p>


                    </div>
                    <div class="col-md-5">
                        <div class="calltobox">
                            <?php echo $seller_faq; ?>
                        </div>
                    </div>

                </div>
            </div>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
</div>



<?php echo $footer; ?>