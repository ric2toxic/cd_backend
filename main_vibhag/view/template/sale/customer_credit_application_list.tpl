<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
  	<div class="container-fluid">
      <div class="pull-right">
        
         <a href="javascript:;" data-toggle="tooltip" title="Download CSV" class="btn btn-primary" <?php if(empty($customers)){echo 'disabled="disabled"';}?> id="button-download" style="margin-right: 5px;"><i class="fa fa-print"></i> Download CSV</a>

         <a href="javascript:;" data-toggle="tooltip" title="Add Application" class="btn btn-primary" id="add_application"><i class="fa fa-plus"></i></a>
      </div>
  		<h1><?php echo $heading_title; ?></h1>
      	<ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      	</ul>
  	</div>
    <div class="text-right">
      <span class="small">O.T = Order Total</span> |
      <span class="small">N.P = Net Payable</span> |
      <span class="small">B.A.= Balance Amount</span> |
      <span class="small">O = Orders</span> |
      <span class="small">R = Returns</span> |
      <span class="small">F = COD Failed</span> |
      <span class="small">DI = Delivery Issues</span> |
      <span class="small">C = Cancelled Orders</span> |
      <span class="small">LO = Last Order date</span>
      <span class="small">&nbsp;</span>
    </div>
  </div>
  <div class="container-fluid">
    <!-- <div class="alert alert-danger error_message_status"><i class="fa fa-exclamation-circle"></i> <span class="error_message_status_span"></span>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="alert alert-success success_message_status"><i class="fa fa-check-circle"></i> <span class="success_message_status_span"></span>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div> -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
      <form id="form_filter">
      	<div class="well">
          <div class="row">
          	<div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-step"><?php echo $entry_step; ?></label>
                <input type="text" name="filter_step" value="<?php echo $filter_step; ?>" placeholder="<?php echo $entry_step; ?>" id="input-step" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-application-id">Credit Application Id</label>
                <input type="text" name="filter_application_id" value="<?php echo $filter_application_id; ?>" placeholder="Credit Application Id" id="input-application-id" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
               <div class="form-group">
                <label class="control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
                <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
               <div class="form-group">
                <label class="control-label" for="input-pancard">Pancard</label>
                <input type="text" name="filter_pancard" value="<?php echo $filter_pancard; ?>" placeholder="Pancard" id="filter_pancard" class="form-control" />
              </div>
            </div>

            <div class="col-sm-3">

              <div class="form-group">
                <label class="control-label" for="input-vrsion">Select Document Type</label>
                <select name="filter_document_type" id="filter_document_type" class="form-control">
                  <option value="">--Select Document Type--</option>
                  <option value="six_months_bank_statement" <?php if(!empty($filter_document_type) && $filter_document_type=='six_months_bank_statement') echo 'selected'; ?>>Bank Statement</option>
                  <option value="aadhaar_card" <?php if(!empty($filter_document_type) && $filter_document_type=='aadhaar_card') echo 'selected'; ?>>Aadhaar Card</option>
                  <option value="pancard" <?php if(!empty($filter_document_type) && $filter_document_type=='pancard') echo 'selected'; ?>>PAN Card</option>
                </select>
              </div>
            </div>
            <div class="col-sm-3">

              <div class="form-group">
                <label class="control-label" for="input-vrsion">Document Status</label>
                <select name="filter_document_status" id="filter_document_status" class="form-control">
                    <option value="">--All except duplicates--</option>
                    <option value="no_status" <?php if(!empty($filter_document_status) && $filter_document_status=='no_status') echo 'selected'; ?>>No Status</option>
                    <option value="activated" <?php if(!empty($filter_document_status) && $filter_document_status=='activated') echo 'selected'; ?>>Activated</option>
                    <option value="BLOCKED" <?php if(!empty($filter_document_status) && $filter_document_status=='BLOCKED') echo 'selected'; ?>>Blocked</option>
                    <option value="document_awaited" <?php if(!empty($filter_document_status) && $filter_document_status=='document_awaited') echo 'selected'; ?>>Document Awaited</option>
                    <option value="under_process" <?php if(!empty($filter_document_status) && $filter_document_status=='under_process') echo 'selected'; ?>>Under Process</option>
                    <option value="approved_but_agreement_pending" <?php if(!empty($filter_document_status) && $filter_document_status=='approved_but_agreement_pending') echo 'selected'; ?>>Approved But Agreement Pending</option>
                    <option value="approved_document_received" <?php if(!empty($filter_document_status) && $filter_document_status=='approved_document_received') echo 'selected'; ?>>Approved And Document Received</option>
                    <option value="approved_but_not_interested" <?php if(!empty($filter_document_status) && $filter_document_status=='approved_but_not_interested') echo 'selected'; ?>>Approved But Not Interested</option>
                    <option value="rejected" <?php if(!empty($filter_document_status) && $filter_document_status=='rejected') echo 'selected'; ?>>Rejected</option>
                    <option value="customer_not_interested" <?php if(!empty($filter_document_status) && $filter_document_status=='customer_not_interested') echo 'selected'; ?>>Customer Not Interested</option>
                    <option value="approved_without_bank_statement" <?php if(!empty($filter_document_status) && $filter_document_status=='approved_without_bank_statement') echo 'selected'; ?>>Approved With Bank Statement</option>
                    <option value="document_in_transit" <?php if(!empty($filter_document_status) && $filter_document_status=='document_in_transit') echo 'selected'; ?>>Document In Transit</option>
                    <option value="limit_issue" <?php if(!empty($filter_document_status) && $filter_document_status=='limit_issue') echo 'selected'; ?>>Limit Issue</option>
                    <option value="need_more_information" <?php if(!empty($filter_document_status) && $filter_document_status=='need_more_information') echo 'selected'; ?>>Need More Information</option>
                    <option value="duplicate" <?php if(!empty($filter_document_status) && $filter_document_status=='duplicate') echo 'selected'; ?>>Duplicates</option>

                </select>
              </div>
            </div>
            <div class="col-sm-3">
            	<div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              	</div>

              </div>

              <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_modified; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                </div>

              </div>

              <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_followup_date; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_followup_date" value="<?php echo $filter_followup_date; ?>" placeholder="<?php echo $entry_followup_date; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
                </div>

              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="input-vrsion"><?php echo $entry_rbl_approved?></label>
                  <select name="filter_rbl_approved" id="filter_rbl_approved" class="form-control">
                      <option value="">--Select All--</option>
                      <option value="sent-for-approved" <?php if(!empty($filter_rbl_approved) && $filter_rbl_approved=='sent-for-approved') echo 'selected'; ?>>Sent for Pre-Approval</option>
                      <option value="non-approved" <?php if(!empty($filter_rbl_approved) && $filter_rbl_approved=='non-approved') echo 'selected'; ?>>Not send for Pre-Approval</option>
                      <option value="pre-approved-by-rbl" <?php if(!empty($filter_rbl_approved) && $filter_rbl_approved=='pre-approved-by-rbl') echo 'selected'; ?>>Pre-Approved By RBL</option>
                      <option value="cif-created-by-rbl" <?php if(!empty($filter_rbl_approved) && $filter_rbl_approved=='cif-created-by-rbl') echo 'selected'; ?>>CIF Created By RBL</option>
                      <option value="rbl-journey-completed" <?php if(!empty($filter_rbl_approved) && $filter_rbl_approved=='rbl-journey-completed') echo 'selected'; ?>>RBL Journey Completed</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-3">
                <div class="form-group">
                  <label class="control-label" for="input-vrsion"><?php echo $entry_rbl_discrepency?></label>
                  <select name="filter_discrepency_status" id="filter_discrepency_status" class="form-control">
                      <option value="">--Select All--</option>
                      <option value="yes" <?php if(!empty($filter_discrepency_status) && $filter_discrepency_status=='yes') echo 'selected'; ?>>Yes</option>
                      <option value="no" <?php if(!empty($filter_discrepency_status) && $filter_discrepency_status=='no') echo 'selected'; ?>>No</option>
                  </select>
                </div>
              </div>
               <div class="col-sm-3">               
               <div class="form-group">
                <label class="control-label" for="input-telephone">Customer Id</label>
                <input type="text" name="filter_customer_id" value="<?php echo $filter_customer_id; ?>" placeholder="Customer Id" id="input-telephone" class="form-control" />
              </div>
            </div>

            <div class="col-sm-3">
               <div class="form-group">
                <label class="control-label" for="input-state">State</label>
                <select name="filter_state" id="input-filter_state" class="form-control">
                  <option value=""><?php echo $text_select; ?></option>
                  <?php
                      foreach($all_states as $state){
                          if($state['name'] == $filter_state){
                            $selected = 'selected';
                          }else{
                            $selected = '';
                          }
                  ?>
                    <option value="<?php echo $state['name']; ?>" <?php echo $selected; ?>><?php echo $state['name']; ?></option>
                  <?php
                      }
                  ?>
                </select>
              </div>
            </div>

            <div class="col-sm-3">
               <div class="form-group">
                <label class="control-label" for="input-city">City</label>
                <input type="text" name="filter_city" value="<?php echo $filter_city; ?>" placeholder="City" id="input-city" class="form-control" />
              </div>
            </div>

              <?php if(in_array($khufiya_user_id,SHORT_SMS_PERMISSION) || in_array($khufiya_user_id,SMS_CRITERIA_ICON_PERMISSION)) { ?>

              <div class="col-sm-3">
                  <div class="form-group">
                      <label class="control-label" for="input-vrsion">Select Filter</label>
                      <select name="filter_sms_log_type" id="filter_sms_log_type" class="form-control">
                          <option value="">--Select--</option>
                          <option value="pos" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='pos') echo 'selected'; ?>>POS</option>
                          <option value="udaan" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='udaan') echo 'selected'; ?>>UPL</option>
                          <option value="bounce" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='bounce') echo 'selected'; ?>>Bounce</option>
                          <option value="gst" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='gst') echo 'selected'; ?>>Gst</option>
                          <option value="account" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='account') echo 'selected'; ?>>Bank</option>
                          <option value="paytm" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='paytm') echo 'selected'; ?>>Paytm</option>
                          <option value="lazypay" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='lazypay') echo 'selected'; ?>>LazyPay</option>
                          <option value="loan" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='loan') echo 'selected'; ?>>Loan</option>
                          <option value="mswipe" <?php if(!empty($filter_sms_log_type) && $filter_sms_log_type=='mswipe') echo 'selected'; ?>>Mswipe</option>
                      </select>
                  </div>
              </div>
              <?php } ?>

            <div class="col-sm-3">
               <div class="form-group">
                <label class="control-label" style="width: 100%;margin-top: 6px;"> &nbsp</label>
                <input type="checkbox" name="filter_by_crif" value="1" class="form-control" style="float: left;margin-right: 10px;" <?php if(!empty($filter_by_crif) && $filter_by_crif=='1'){echo 'checked="checked"';}?> /><span style="display: inline-block; max-width: 100%; margin-bottom: 5px; font-weight: bold;">Filter by Crif Score</h4>
              </div>
            </div>

             <div class="col-sm-10">
              <div class="form-group">
              <button type="reset" class="btn btn-default pull-right" style="display: block;margin-top: 22px;"><i class="fa fa-undo"></i> Reset</button>
              </div>
            </div>

              <div class="col-sm-1">
              <div class="form-group">
              <button type="button" id="button-filter" class="btn btn-primary pull-right" style="display: block;margin-top: 22px;"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>
          </div>
        </div>
        </form>
      </div>
      <!-- panel body closes -->
      <form action="" method="post" enctype="multipart/form-data" id="form-customer">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><a><?php echo $column_name; ?></a></td>
                   <td class="text-left">
                    <a><?php echo $column_email; ?></a>
                    <hr class="btn-success">
                    <a><?php echo $column_telephone; ?></a>
                  </td>
                  <td class="text-left"><a> Order History </a></td>
                  <td class="text-left"><a><?php echo $column_date_added; ?></a></td>
                    <td class="text-left"><a><?php echo $column_draft; ?></a></td>
                    <!-- <td class="text-left"><a>ePayLater Verify OTP</a></td> -->
                    <!-- <td class="text-left"><a><?php echo $column_shared; ?></a></td> -->
                   <!--  <td class="text-left"><a><?php echo 'Version'; ?></a></td> -->
                    <td class="text-left">
                    <a href="<?php echo $sort_last_modified; ?>"  class="<?php echo strtolower($order); ?>">Last Modified</a>
                     <hr class="btn-success">
                     <a>Last Comment Date</a>
                    </td>
                  <td class="text-right"  style="width: 150px;"><a><?php echo $column_action; ?></a></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($customers) {?>
                <?php foreach ($customers as $customer) { //echo "<pre>"; print_r($customer); die;?>
                <tr <?php 
                      if(
                          ( !empty($customer['has_active_wsb_credit']) && $customer['has_active_wsb_credit']=='enabled' )
                          ||
                          ( !empty($customer['credit_status']) && $customer['credit_status'] == 1)
                        ) {
                        echo 'style="background-color:#7dce799c"'; 
                      } elseif(
                                ( !empty($customer['has_active_wsb_credit']) && $customer['has_active_wsb_credit']=='blocked' )
                                ||
                                ( !empty($customer['credit_status']) && $customer['credit_status'] == 0)
                              ) 
                      {
                        echo 'style="background-color:#eace009c"'; 
                      }
                ?> >
                  <td class="text-left"><strong><?php echo $customer['name'];?></strong>
                   <br>
                  Credit  Application Id: <?php echo $customer['customer_credit_application_id'];?>
                  <br />
                  <br>
                    <?php if($customer['customer_id']!='0'){ ?>
                      Customer Id: <?php echo $customer['customer_id'];?>
                      <?php if($customer['master_id'] != $customer['customer_id']){ ?>
                      <a href="<?php echo $customer['filter_by_master_id_url'] ?>">Master Id: <?php echo $customer['master_id'];?></a>
                      <?php } ?>
                      <?php } else { ?>
                      Customer Id: 
                      <a id="create_user_icon_<?php echo $customer['customer_credit_application_id'];?>" href="javascript:;" onclick="create_customer(<?php echo $customer['customer_credit_application_id'];?>)"><i class="fa fa-user" aria-hidden="true"></i></a>
                      <?php } ?>
                      <br />

                      <?php if(!empty($customer['company_name'])){ ?>
                      Shop: <?php echo $customer['company_name'];?>
                      <br />
                      <?php } ?>

                      <?php if(!empty($customer['gst_number'])){ ?>
                        Gst Number: 
                        <span id="gst_number"><?php echo $customer['gst_number'];?></span>
                         <a href="javascript:;" onclick="copyText('<?php echo $customer['gst_number'];?>')">  
                          <i class="fa fa-copy"></i>
                         </a>
                      &nbsp;
                          <a href="https://services.gst.gov.in/services/searchtp?GstInNo=<?php echo $customer['gst_number'];?>&PanNum=<?php echo $customer['pan_no']??'';?>" class="float" target="_blank">
                            <i class="fa fa-link" style="font-size:16px;"></i>
                          </a>
                      &nbsp;
                          <a href="javascript:;" class="float" title="GST Verification" onclick="get_gst_verification(<?php echo $customer['customer_credit_application_id']; ?>);">
                            <?php if($customer['gst_verification'] > 0) { ?>
                             <i class="fa fa-pencil" style="font-size:16px; color: #007d00;"></i>
                            <?php } else { ?>
                            <i class="fa fa-pencil" style="font-size:16px;"></i>
                            <?php } ?>
                          </a>
                      <br />
                      <?php } ?>

                    <?php if(!empty($customer['current_city'])){?>
                    City: <?php echo $customer['current_city'];?>
                    <br />
                    <?php } ?>
                    <?php if(!empty($customer['current_state'])){?>
                    State: <?php echo $customer['current_state'];?>
                    <br />
                    <?php } ?>

                    <?php echo !empty($customer['agent_name'])?
                    "Agent: ".$customer['agent_name']:""; ?>
                    <br />
                    <?php echo !empty($customer['tl_name'])?
                    "TL Name: ".$customer['tl_name']:""; ?>

                    <?php echo !empty($customer['home_distance'])?
                    '<h4><span class="label label-default"> Home Distance: <span class="badge">'.round(($customer['home_distance']/1000),2).' Km</span></span></h4>':""; ?>

                    <?php echo !empty($customer['shop_distance'])?
                    '<h4><span class="label label-default"> Shop Distance: <span class="badge">'.round(($customer['shop_distance']/1000),2).' Km</span></span></h4>':""; ?>
                   </span>
                  <?php if(!empty($customer['bank_details']['bank_ac_number'])) { ?>
                    <br/>
                    <a title="Bank Account" style="cursor: pointer;" onclick="get_bank_details(<?php echo $customer['customer_id']; ?>);">
                        <i class="fa fa-university fa-4" aria-hidden="true" style="font-size:16px;"></i>
                    </a>
                  <?php }?>

                  <?php if(!empty($customer['location_count'])) { ?>
                    <a title="Saved location list" style="cursor: pointer;" onclick="get_location_details(<?php echo $customer['customer_id']; ?>);">
                        <i class="fa fa-globe fa-4" aria-hidden="true" style="font-size:18px; margin-left: 15px;"></i>
                    </a>
                  <?php }?>

                  <?php if(!empty($customer['fi_verification'])) { ?>
                      <br/>
                   <div style="border: 1px solid #6ab53b; padding: 2px 5px; border-radius: 5px;">
                    <i class="fa fa-user" aria-hidden="true"></i> &nbsp; FI Availability
                   </div>
                  <?php } ?>

                  </td>
                  <td class="text-left">
                  <?php echo $customer['email'];?>
                  <hr />
                  <?php echo $customer['telephone'];?>
                  <?php
                  if(!empty($customer['lead_contacts'])){
                      echo '<hr />';
                      echo 'Crm-Contacts:</br>';
                      echo implode(',', $customer['lead_contacts']);
                  }
                  ?>

                  <hr/>

                  <a href="https://api.whatsapp.com/send?phone=<?php echo $customer['telephone'];?>&text=Hello." title="Whatsapp" class="float" target="_blank">
                      <img src="<?php echo STATIC_CONTENT_URL_SSL;?>/img/whatsapp_287520.png" style="width:25px;">
                  </a>

                  <a href="https://www.google.com/search?q=<?php echo $customer['telephone'];?>" title="Google Search" class="float" target="_blank">
                    <img src="<?php echo STATIC_CONTENT_URL_SSL;?>/img/goog_940993.png" style="width:20px;">
                  </a>


                  <a href="https://www.truecaller.com/search/in/<?php echo $customer['telephone'];?>" class="float" title="Truecaller" target="_blank">
                      <img src="<?php echo STATIC_CONTENT_URL_SSL;?>/img/phone_1055012.png" style="width:20px;">
                  </a>


                  <?php /* if(!empty($customer['gst_number'])) { ?>
                  &nbsp;
                  <a href="https://services.gst.gov.in/services/searchtp?GstInNo=<?php echo $customer['gst_number'];?>&PanNum=<?php echo $customer['pan_no']??'';?>" class="float" target="_blank">
                      <span class="label label-success">GST</span>
                  </a>
                  <?php } */ ?>

                <?php if(in_array($this->user->getId(),SMS_CRITERIA_ICON_PERMISSION) || in_array($this->user->getId(),SHORT_SMS_PERMISSION)) { ?>
                    <hr class="btn-success">
                    <div id="sms_details_<?php echo $customer['customer_credit_application_id']?>">
                        <span class="click_to_see btn-primary btn-xs" onclick="getSMSDetails(<?php echo $customer['customer_id'];?>, 'sms_details_<?php echo $customer['customer_credit_application_id']?>')">Get Profile</span>
                    </div>
                <?php } ?>

                  </td>

                  <td class="text-left">
                  <?php if($customer['customer_id']!='0'){ ?>
                        <div id="order_stats_<?php echo $customer['customer_credit_application_id']?>">
                            <span class="click_to_see btn-primary btn-xs" onclick="getOrderStats(<?php echo $customer['customer_id'];?>, 'order_stats_<?php echo $customer['customer_credit_application_id']?>')"> Get Stats </span>
                        </div>
                    </label>
                    <?php } else { ?>
                    <label class="order_list_comment" >
                    Not available
                     </label>
                    <?php } ?>
                  </td>

                  <td class="text-left"><?php echo $customer['date_added'];?></td>
                  <td class="text-left">
                      <a data-toggle="modal" title="" class="btn btn-default" onclick="get_document_checklist(<?php echo $customer['customer_credit_application_id']; ?>)">Credit Checklist</a>
                        <hr/>
                       <a data-toggle="modal" title="" class="btn btn-default" onclick="get_questions(<?php echo $customer['customer_credit_application_id']; ?>)">Credit Questions</a>

                      <hr/>
                      <h4><span class="label label-default"> Step: <span class="badge"><?php echo $customer['step'];?></span></span></h4>
                    <?php
                          $show_crif_icon = 0;
                          if(!empty($customer['get_all_document'])){
                            foreach($customer['get_all_document'] as $key=>$application_type){
                              foreach($application_type as $key1=>$document){

                              if($document['name'] == 'pancard')
                              { $show_crif_icon = 1; }

                              echo '<p><a href="'.STATIC_CONTENT_URL_SSL . $document['file_path'].'" target="_blank">'.ucfirst(str_replace('_',' ',$document['name'])).'</a></p>';
                              }
                            }

                            ?>
                            <p> <a href="javascript:;" onclick="openModal(<?php echo $customer['customer_credit_application_id']; ?>);currentSlide(1)"> View All </a> </p>
                                   <!-- The Modal/Lightbox -->
                              <div id="documentModal_<?php echo $customer['customer_credit_application_id']; ?>" class=" documentModal modal" role="dialog">

                                   <span class="close cursor" onclick="closeModal(<?php echo $customer['customer_credit_application_id']; ?>)">&times;</span>
                                    <div class="modal-dialog" style="margin-left: 13%;">
                                      <!-- Modal content-->
                                    <div class="modal-content" style="width: 1000px;">
                                    <div class="modal-body" id="slides_<?php echo $customer['customer_credit_application_id']; ?>" style=" height: 700px;">
                                       
                                     <?php
                                     foreach($customer['get_all_document'] as $key=> $application_type){
                                      foreach($application_type as $key1=>$document){
                                     ?>  
                                      <div class="mySlides">
                                      <div class="row">
                                          <div class="col-sm-4">
                                             <div class="form-group">
                                               <select class="form-control" id="document_status_change_<?php echo $document['id']; ?>">
                                               <option value="pancard" <?php if($document['name'] == 'pancard') { echo "selected"; } ?>>Pancard</option>
                                               <option value="aadhaar_card" <?php if($document['name'] == 'aadhaar_card') { echo "selected"; } ?>>Aadhaar Card</option>
                                               <option value="photo" <?php if($document['name'] == 'photo') { echo "selected"; } ?>>Photo</option>
                                               <option value="voter_id" <?php if($document['name'] == 'voter_id') { echo "selected"; } ?>>Voter Id</option>
                                               <option value="driving_license" <?php if($document['name'] == 'driving_license') { echo "selected"; } ?>>Driving License</option>
                                               <option value="passport" <?php if($document['name'] == 'passport') { echo "selected"; } ?>>Passport</option>
                                               <option value="six_months_bank_statement" <?php if($document['name'] == 'six_months_bank_statement') { echo "selected"; } ?>>Six months bank statement</option>
                                               <option value="shop_photo" <?php if($document['name'] == 'shop_photo') { echo "selected"; } ?>>Shop photo</option>
                                               <option value="selfie_with_shop" <?php if($document['name'] == 'selfie_with_shop') { echo "selected"; } ?>>Selfie with shop</option>
                                               </select>
                                            </div>
                                         </div>
                                          <div class="col-sm-2">
                                           <button type="button" onClick="change_document(<?php echo $document['id']; ?>)" class="btn btn-primary pull-left"><i class="fa fa-save"></i> Update</button>
                                          </div>
                                      </div> 

                                      <?php  if(pathinfo($document['file_path'], PATHINFO_EXTENSION) == 'jpg' || pathinfo($document['file_path'], PATHINFO_EXTENSION) == 'jpeg' || pathinfo($document['file_path'], PATHINFO_EXTENSION) == 'png') { ?>  
                                        <img src="<?php echo STATIC_CONTENT_URL_SSL . $document['file_path']; ?>">
                                     <?php } else{ ?>
                                         <iframe src="<?php echo STATIC_CONTENT_URL_SSL . $document['file_path']; ?>" width="100%" height="640px"></iframe>
                                     <?php  }   ?>

                                      </div>
                                    <?php }}  ?>
                                      <!-- Next/previous controls -->
                                      <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                                      <a class="next" onclick="plusSlides(1)">&#10095;</a>
                                      <!-- Caption text -->
                                      </div>
                                      </div>
                                    </div>

                                    </div>

                            <?php
                          }
                    ?>
                  </td>
                 <!--  <td class="text-left">
                    <?php if(!empty($customer['applicationId'])) { ?>
                    <input type="checkbox" name="epayLater_onboard_otp" class="verify_otp_confirm" id="onboard" data-id="<?php echo $customer['customer_credit_application_id']; ?>" <?php echo $customer['epaylater_otp_flag'] ? 'checked' : ''; ?> > Onboard <br/>
                    <?php } ?>
                    <?php if($customer['kyc_done']) { ?>
                    <input type="checkbox" name="epayLater_kyc_otp" class="verify_otp_confirm" id="kyc" data-id = "<?php echo $customer['customer_credit_application_id']; ?>" <?php echo $customer['epaylater_kyc_otp_flag'] ? 'checked' : ''; ?>> KYC Upload
                    <?php } ?>
                  </td> -->
                  <!-- <td class="text-left"><a data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="tooltip" title="<?php echo 'ApplicationId:'.$customer['applicationId'];?>" class="application_status_chk"><span style="text-decoration
:underline"><?php echo $customer['shared_with'];?></span>
                  </a></td> -->
                 <!--  <td class="text-left"><?php //echo $customer['version']=='1'?'Wizard Form':'Short Form';?></td> -->
                  <td class="text-left">
                  <a href="<?php echo $sort_last_modified; ?>">
                  <?php echo date('d-m-Y H:i:s', strtotime($customer['last_modified'])); ?></a>
                 
                  <?php 
                  if(isset($customer['last_note']['date_added']))
                  { ?>
                   <hr class="btn-success">
                  <?php echo $customer['last_note']['date_added']; ?>
                 <?php }
                  ?>

                  <?php
                   if(!empty($customer['fi_verification']) && ($customer['document_status'] == 'approved_but_agreement_pending' || $customer['document_status'] == 'approved_without_bank_statement' || $customer['document_status'] == 'approved_document_received' || $customer['document_status'] == 'approved_but_not_interested')) 
                  { ?>
                   <hr class="btn-success">
                    <a  class="btn btn-default fi_document" data-id="<?php echo $customer['customer_credit_application_id']; ?>" id="fi_document_<?php echo $customer['customer_credit_application_id']; ?>">Upload FI Document</a>
                    <span id="fi_document_loading_<?php echo $customer['customer_credit_application_id']; ?>" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span>
                  <?php } ?>

                  <?php if(!empty($customer['fi_document'])) { ?>
                   <hr class="btn-success">
                    <a href="<?php echo HTTPS_CATALOG.'image/'.$customer['fi_document']; ?>" class="btn btn-default" target="_blank">View FI Document</a>
                  <?php } ?>

                   <?php if(!empty($customer['fi_document'])) { ?>
                   <hr class="btn-success">
                   <a href="javascript:;" onclick="fi_document_mail(this);" class="btn btn-default" data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-file="<?php echo $customer['fi_document']; ?>">Document Mail</a>
                  <?php } ?>
                  </td>
                  <td class="text-right"  style="width: 150px;">
                   <?php if(isset($customer['customer_id']) && !empty($customer['customer_id'])){ ?>
                    <a data-id="<?php echo $customer['customer_id']; ?>" data-customer_credit_application_id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="modal" title="Send Notification" href="javascript:void(0)" class="send_credit_application_notifiction btn btn-info"><i class="fa fa-bell"></i></a>

      <?php } ?>
                   <?php if(isset($customer['lead_images']) && !empty($customer['lead_images'])){ ?>
                    <a data-id="<?php echo $customer['customer_id']; ?>" data-customer-images="<?php echo implode(',', $customer['lead_images'])  ?>" data-toggle="modal" title="Customer Images in Crm" href="javascript:void(0)" class="credit_application_customer_images btn btn-info"><i class="fa fa-file-image-o"></i></a>

      <?php } ?>
                    <a data-id="<?php echo $customer['customer_id']; ?>" data-customer_credit_application_id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="modal" title="Edit Application" class="edit_application btn btn-success"><i class="fa fa-pencil"></i></a>
                    <a data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="modal" class="history_popup btn btn-info" title="Credit Application Log"><i class="fa fa-info"></i></a>
                    <?php if ($customer['step'] >= '2' && empty($customer['applicationId'])) { ?>
                  	<a data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="modal" class="share_popup btn btn-primary" title="Share Application" ><i class="fa fa-share-alt"></i></a>
                    <?php } ?>
                    <?php if (!empty($customer['redirect_url']) && $customer['epaylater_otp_flag'] == '0') { ?>
                    <a target="_blank" href="<?php echo $customer['redirect_url']; ?>" class=" btn btn-info" title="ePayLater Verify OTP"><i class="fa fa-external-link"></i></a>
                    <?php } ?>
                    <?php if($customer['step'] == '4') {
                    ?>
                    <a data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-toggle="modal" class="kyc_docs btn btn-success" title="ePayLater Kyc Upload"><i class="fa fa-upload"></i></a>
                    <?php } ?>

                    <a class="btn btn-warning" title="note" style="cursor: pointer;" onclick="get_notes(<?php echo $customer['customer_credit_application_id']; ?>, <?php echo $customer['telephone']; ?>);">
                         <i class="fa fa-comment-o" aria-hidden="true"></i></a>

                    <a href="<?php echo $customer['credit_tab']; ?>" style="padding: 7px 11px;" data-toggle="tooltip" title="Credit Tab" class="btn btn-primary margin-top" target="_blank"><i class="fa fa-credit-card"></i></a>
                    
                    <?php if($customer['customer_id']!='0' && $show_crif_icon) { ?>
                    <a class="btn btn-success" title="Crif" style="cursor: pointer;" onclick="get_pan_detail(<?php echo $customer['customer_credit_application_id']; ?>);">
                         <i class="fa fa-users" aria-hidden="true"></i>
                    </a>
                    <?php } ?>

                    <?php 

                    if(isset($customer['rbl_credit_status']) && $customer['rbl_credit_status'])
                    {

                      $btn_style = '';
                      $rbl_btn_title = 'Send To RBL For Credit Pre-Approval';
                        if(!empty($customer['rbl_status']))
                        { 
                          if($customer['rbl_status'] == 'on_boarding') 
                          {
                            $btn_style = 'background-color:#ffff00; color:#0000ff';
                            $rbl_btn_title = 'RBL Pre-On-Boarding Status';
                          }
                          else if($customer['rbl_status'] == 'pre_approved_status') 
                          {
                            $btn_style = 'background-color:#f38733; color:#ffffff; border-color:#f38733;';
                            $rbl_btn_title = 'RBL Pre-Approved Status';
                          }
                          else if($customer['rbl_status'] == 'discrepency_status') 
                          {
                            $btn_style = 'background-color:#ff0000; color:#ffffff';
                            $rbl_btn_title = 'RBL Discrepency Status';
                          }
                          else if($customer['rbl_status'] == 'cif_created' || $customer['rbl_status'] == 'activated_by_rbl')
                          {
                            $btn_style = 'background-color:#75a74d; color:#ffffff; border-color:#75a74d;';
                            $rbl_btn_title = 'RBL Credit Approved';
                          }
                        }
                    ?>
                      <a  data-customer_credit_application_id="<?php echo $customer['customer_credit_application_id']; ?>" 
                          data-id="<?php echo $customer['customer_id']; ?>" 
                          data-toggle="modal" 
                          title="<?php echo $rbl_btn_title;?>" 
                          href="javascript:void(0)" 
                          class="send_customer_credit_preapproved btn btn-info"
                          style="padding:4px; <?php echo $btn_style;?>">
                          <span style="font-size: 15px; font-weight: bold">RBL</span>
                      </a>

                  <?php } ?>
                 
                    <a class="btn btn-info" title="Crif Json" style="cursor: pointer;" onclick="get_crif_json(<?php echo $customer['customer_credit_application_id']; ?>, <?php echo $customer['customer_id']; ?>);">
                         <i class="fa fa-file-code-o" aria-hidden="true"></i>
                    </a>

                     <?php if($customer['customer_email_count']!='0') { ?>
                    <a class="btn btn-info" title="Email list" style="cursor: pointer;" onclick="get_email_list(<?php echo $customer['customer_id']; ?>);">
                         <i class="fa fa-envelope" aria-hidden="true"></i>
                    </a>
                    <?php } ?>


                   <span style="margin-top: 10px; display: block;">
                        
                    <a data-id="<?php echo $customer['customer_credit_application_id']; ?>" data-telephone="<?php echo $customer['telephone']; ?>"
                    data-status="<?php echo $customer['document_status'];
                    ?>"
                     id="filter_document_status_change_<?php echo $customer['customer_credit_application_id']; ?>"
                     class="filter_document_status_change btn btn-default">
                      <?php
                        if($customer['document_status'] != '')
                        {
                           echo ucwords(str_replace('_', ' ', $customer['document_status']));
                        }
                        else
                        {
                          echo "Update Status";
                        }
                       ?>

                    </a>
                    </span>

                    <hr />
                    Note: <span id="last_note_<?php echo $customer['customer_credit_application_id']; ?>">
                     <?php
                     if(isset($customer['crif_score']))
                       {
                     ?>
                     <hr />
                     Crif score: <?php  echo $customer['crif_score']; }  ?> 

                    <?php
                     if(isset($customer['last_note']['remark']))
                       {
                     ?>  
                     <hr />
                      Note: <span id="last_note_<?php echo $customer['customer_credit_application_id']; ?>">
                     <?php 
                        echo substr($customer['last_note']['remark'],0,25).'...'; 
                      ?> 
                       </span>
                      <?php } ?> 

       
                      <?php
                     if(isset($customer['credit_limit']) && $customer['credit_limit'] > 0)
                       {
                       ?>
                        <hr />
                        Credit Limit: <span id="last_note_credit_limit_<?php echo $customer['customer_credit_application_id']; ?>">

                      <?php  echo $customer['credit_limit'];  ?>

                       </span>
                     <?php } ?> 
                     
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>

    </div>
   <!-- panel closed -->
  </div>
  <!-- container closed -->
<!-- </div> -->
<div id="share_credit_application_popup" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 600px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Please Click to share the application</h4>
    </div>
    <div class="modal-body" style=" height: 400px;">
    <div class="alert alert-danger error_message"><i class="fa fa-exclamation-circle"></i><span class="error_message_span"></span>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <div class="alert alert-success success_message"><i class="fa fa-check-circle"></i><span class="success_message_span"></span>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
        <span class="credit_partners_list_div">
        </span>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>

<div id="credit_application_status_popup" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 400px;height:200px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title">ePayLater Credit Application Status</h3>
    </div>
    <div class="modal-body" style="height:120px">
        <div class="credit_status_div" style="font-size:14px">
        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>

<div id="credit_application_add" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 850px;height:700px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">X</button>
      <h3 class="modal-title">Add Credit Application</h3>
    </div>
    <div class="modal-body">
        <div class="credit_application_iframe">
        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>


<div id="credit_application_edit" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 850px;height:700px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">X</button>
      <h3 class="modal-title">Edit Credit Application</h3>
    </div>
    <div class="modal-body">
        <div class="credit_application_iframe">
        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>


    <div id="read_sms_pos" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog  modal-lg" >
            <!-- Modal content-->
            <div class="modal-content" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">X</button>
                    <h3 class="modal-title">POS SMS</h3>
                </div>
                <div class="modal-body">
                    <div id="response_text"> </div>
                    <div id="sms_pos_logs">  </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>



<div id="credit_application_notification" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" >
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title">Send Notification To Customer</h3>
    </div>
    <div class="modal-body">

        <div id="notificationLogs">

        </div>


        <div class="credit_application_notification">
            <form id="creditApplicationForm" method="post" action="" >
                <input type="hidden" name="credit_application_customer_id" id="credit_application_customer_id" value="" >
                <input type="hidden" name="credit_application_form_id" id="credit_application_form_id"  value="">
                <label>Message:</label>

                  <div class="md-form mb-5">
                    <textarea  name="notification_message" id='credit_application_notification_message' class='form-control'></textarea>
                </div>

         </form>


      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button class="btn btn-deep-orange" id="send_notification_to_credit_custmer">Send Notification</button>
      </div>



        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

<div id="credit_application_cusotmer_images" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" >
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title">Customer Images In CRM</h3>
    </div>
    <div class="modal-body" style="
    position: relative;
    padding: 15px;
    float: left;
    margin: auto;
    width: 100%;
    height: 400px;
    overflow: auto;
    background-color: #fff;
" >

        <div id="lead_customer_images">

        </div>




        </div>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>




<div id="credit_application_log" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Credit Application Change Log</h2>
    </div>
    <div class="modal-body" style=" height: 500px;">
        <span class="credit_application_log_div">
        </span>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>


<div id="email_list_model" class="modal fade" role="dialog">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h2 class="modal-title">Customer Email List</h2>
    </div>
    <div class="modal-body" style=" height: 500px;">
        <span class="email_list_div">
        </span>
    </div>
    <div class="modal-footer">
    </div>
  </div>
</div>

</div>

<div id="credit_application_status_remarks" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="margin-left: 13%;">
  <!-- Modal content-->
  <div class="modal-content" style="width: 1000px;">


<?php
  if(count($approved_but_agreement_pending_status)) { ?>
       <div class="row" id="approved_but_agreement_pending_reason" style="display: none;">
         <label class="col-sm-2 control-label" for="input-customer-group">Reason</label>
            <div class="col-sm-9">
              <select name="reason[]" class="form-control edit_track"  data-block-name="general"  data-change="false" multiple="multiple">
                <?php
                 $i=0;
                 foreach($approved_but_agreement_pending_status as $status) { ?>
                <option value="<?php echo $status; ?>" <?php if($status=='SRNG'){ echo "selected"; } ?>><?php echo $status; ?></option>
                <?php $i++; } ?>
            </select>
          </div>
        </div>

      <?php } ?>

      <?php if(count($customer_not_interested_status)) { ?>
       <div class="row" id="customer_not_interested_reason" style="display: none;">
         <label class="col-sm-2 control-label" for="input-customer-group">Reason</label>
            <div class="col-sm-9">
              <select name="reason[]" class="form-control edit_track"  data-block-name="general"  data-change="false">
                <?php
                $i=0; 
                foreach($customer_not_interested_status as $status) { ?>
                <option value="<?php echo $status; ?>" <?php if($i==0){ echo "selected"; } ?>><?php echo $status; ?></option>
                <?php $i++; } ?>
            </select>
          </div>
        </div>

      <?php } ?>

      <?php if(count($rejected_status)) { ?>
       <div class="row" id="rejected_reason"  style="display: none;">
         <label class="col-sm-2 control-label" for="input-customer-group">Reason</label>
            <div class="col-sm-9">
              <select name="reason[]" class="form-control edit_track"  data-block-name="general"  data-change="false">
                <?php
                 $i=0;
                 foreach($rejected_status as $status) { ?>
                <option value="<?php echo $status; ?>" <?php if($i==0){ echo "selected"; } ?>><?php echo $status; ?></option>
                <?php $i++; } ?>
            </select>
          </div>
        </div>

      <?php } ?>

      <?php if(count($approved_without_bank_statement)) { ?>
       <div class="row" id="approved_without_bank_statement_reason"  style="display: none;">
         <label class="col-sm-2 control-label" for="input-customer-group">Company</label>
            <div class="col-sm-9">
              <select name="reason[]" class="form-control edit_track"  data-block-name="general"  data-change="false">
                <?php
                 $i=0;
                 foreach($approved_without_bank_statement as $status) { ?>
                <option value="<?php echo $status; ?>" <?php if($status=='SRNG'){ echo "selected"; } ?>><?php echo $status; ?></option>
                <?php $i++; } ?>
            </select>
          </div>
        </div>

      <?php } ?>

      <?php if(count($approved_but_not_interested)) { ?>
       <div class="row" id="approved_but_not_interested_reason"  style="display: none;">
         <label class="col-sm-2 control-label" for="input-customer-group">Company</label>
            <div class="col-sm-9">
              <select name="reason[]" class="form-control edit_track"  data-block-name="general"  data-change="false">
                <?php
                 $i=0;
                 foreach($approved_but_not_interested as $status) { ?>
                <option value="<?php echo $status; ?>" <?php if($status=='SRNG'){ echo "selected"; } ?>><?php echo $status; ?></option>
                <?php $i++; } ?>
            </select>
          </div>
        </div>

      <?php } ?>


    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Update Status</h3>
    </div>
    <div class="modal-body">
     <div id="document_status_data"></div>
    <form action="index.php?route=sale/customer_credit_application/updateDocumentStatus&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="form_status_remarks" class="form-horizontal">
       <input type="hidden" name="credit_application_id" />
       <input type="hidden" name="telephone" />


      <div class="form-group">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Followup Date</label>
       <div class="col-sm-9">
      <div class="input-group date">
                  <input type="text" name="followup_date" value="" placeholder="Followup Date" data-date-format="YYYY-MM-DD" id="followup_date" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
      </div>
        </div>
         </div>

      <div class="form-group">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Status</label>
       <div class="col-sm-9">
      <select name="document_status" id="document_status" class="form-control">
                          <option value="">--Document Status--</option>
                          <option value="document_awaited">Document Awaited</option>
                          <option value="under_process">Under Process</option>
                          <option value="approved_document_received">Approved And Document Received</option>
                          <option value="approved_but_agreement_pending">Approved But Agreement Pending</option>
                          <option value="rejected">Rejected</option>
                          <option value="customer_not_interested">Customer Not Interested</option>
                          <option value="limit_issue">Limit Issue</option>
                          <option value="document_in_transit">Document In Transit</option>
                          <option value="approved_without_bank_statement">Approved Without Bank statement</option>
                          <option value="approved_but_not_interested">Approved But Not Interested</option>
                          <option value="need_more_information">Need More Information</option>
                          <option value="duplicate">Mark Duplicate</option>
                          
                        </select>
         </div>
        </div>
         </div>

       <div class="form-group">
         <div class="row reason_select">
         </div>
       </div>

      
        <div id="approved_credit_limit"  style="display: none;">
        <div class="form-group">
         <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">Credit Limit</label>
            <div class="col-sm-9">
               <input type="text" name="credit_limit" placeholder="Credit Limit" class="form-control" />
          </div>
        </div>
        </div>

      <div class="form-group" id="payment_cycle_div" style="display: none;">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Payment Cycle</label>
       <div class="col-sm-9">
            <select name="payment_cycle" id="payment_cycle" class="form-control">
                  <option value="">--Payment Cycle--</option>
                  <?php foreach($payment_cycle_options as $key=>$payment_cycle_option){?>
                  <option value="<?php echo $payment_cycle_option; ?>"><?php echo ucfirst(str_replace('_', ' ', $payment_cycle_option));?></option>
                  <?php } ?>
            </select>
         </div>
        </div>
         </div>

        <div class="form-group">
         <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">Credit Expire Date</label>
            <div class="col-sm-9">
               <div class="input-group date">
                  <input type="text" name="credit_expire" value="2021-03-31" placeholder="Credit Expire Date" data-date-format="YYYY-MM-DD" id="credit_expire" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
          </div>
        </div>
        </div>

        <div class="form-group">
         <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">Bank Account Last 4 Digit</label>
            <div class="col-sm-9">
               <input type="text" name="bank_account_last_digit" id="bank_account_last_digit" placeholder="Bank Account Last 4 Digit" class="form-control" />
          </div>
        </div>
        </div>

        <div class="form-group">
         <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">IFSC Code</label>
            <div class="col-sm-9">
               <input type="text" name="ifsc_code" placeholder="IFSC Code" class="form-control" />
          </div>
        </div>
        </div>

        <div class="form-group">
          <div class="row">
             <label class="col-sm-2 control-label" for="input-customer-group"></label>
            <div class="col-sm-1" style="width: 3%;">
              <input name="customer_mail" class="form-control" type="checkbox" checked="checked" value="1" />
          </div>
          <div class="col-sm-8">
              Send Mail To Customer
          </div>
        </div>
         </div>  

        </div>

 

     <div class="form-group">
       <div class="row">
             <label class="col-sm-2 control-label" for="input-customer-group"></label>
            <div class="col-sm-1" style="width: 3%;">
              <input name="show_comment" class="form-control" type="checkbox" checked="checked" value="1" />
          </div>
          <div class="col-sm-8">
              Show Comment In CRM
          </div>
        </div>
      </div>
  
      <div class="form-group">
       <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">Comment</label>
            <div class="col-sm-9">
              <textarea name="remark" rows="5" placeholder="Comment" class="form-control"></textarea>
          </div>
        </div>
      </div>
      

        </form>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="change_status_remarks()"  class="btn btn-default"> Submit </button>
    </div>

  </div>
</div>
</div>

<div id="credit_application_notes" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Notes</h3>
    </div>
    <div class="modal-body">
    <div id="note_data">
    </div>

    <form action="index.php?route=sale/customer_credit_application/saveNotes&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="form_notes" class="form-horizontal">
       <input type="hidden" name="credit_application_id" />
        <input type="hidden" name="telephone" />

     <div class="form-group">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Followup Date</label>
       <div class="col-sm-9">
      <div class="input-group date">
                  <input type="text" name="followup_date" value="" placeholder="Followup Date" data-date-format="YYYY-MM-DD" id="followup_date" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
      </div>
        </div>
         </div>

      <div class="form-group">
       <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">Note</label>
            <div class="col-sm-9">
              <textarea name="note" rows="5" placeholder="Note" class="form-control"></textarea>
          </div>
        </div>
      </div>

        </form>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="save_note()"  class="btn btn-default"> Submit </button>
    </div>

  </div>
</div>
</div>



<div id="document_checklist_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Credit Checklist Status</h3>
    </div>
    <div class="modal-body">

    <form action="index.php?route=sale/customer_credit_application/saveDocumentChecklist&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="form_checklist" class="form-horizontal">
      <input type="hidden" name="credit_application_id" />
        <div class="form-group" style="margin-left: 5px;">
            <div class="row">
       <?php foreach($checklist_data as $checklist) { ?>

          <div class="col-sm-6">
           <input type="checkbox" class="credit_checklist" id="credit_checklist_id<?php echo $checklist['credit_checklist_id']; ?>" name="credit_checklist_id[]" value="<?php echo $checklist['credit_checklist_id']; ?>" />
             <?php echo $checklist['name']; ?>
           </div>

       <?php }?>
            </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="save_document_checklist()"  class="btn btn-default"> Submit </button>
    </div>
  </div>
</div>
</div>

<div id="document_questions_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Questionnaire for Telephonic Call</h3>
    </div>
    <div class="modal-body">

    <form action="index.php?route=sale/customer_credit_application/saveDocumentChecklist&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="form_questions" class="form-horizontal">
      <input type="hidden" name="credit_application_id" />

     <?php foreach($questions_data as $questions) { ?>
     <div class="form-group" style="margin-left: 15px;">
      <div class="row">
            <div class="col-sm-11"><?php echo $questions['question']; ?></div>
            <div class="col-sm-11">
             <?php if($questions['options'] != '') { 
              $options = explode(",", $questions['options']);
              foreach($options as $option)
              { 
              ?>
             <input type="checkbox" class="answer<?php echo $questions['credit_questionnaire_id']; ?>" id="answer<?php echo $questions['credit_questionnaire_id']; ?>" name="answer[<?php echo $questions['credit_questionnaire_id']; ?>][]" value="<?php echo $option; ?>" />
             <?php echo $option; ?> &nbsp; 

             <?php } } else { ?>
              <input type="text" id="answer<?php echo $questions['credit_questionnaire_id']; ?>" name="answer[<?php echo $questions['credit_questionnaire_id']; ?>]" placeholder="Answer" class="form-control" />
             <?php } ?>  
            </div>
        </div>
      </div>
      <?php } ?>

      </form>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="save_questions()"  class="btn btn-default"> Submit </button>
    </div>
  </div>
</div>
</div>


<div id="gst_verification_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">GST Verification</h3>
    </div>
    <div class="modal-body">
    <div id="gst_data">
    </div>

    <form action="index.php?route=sale/customer_credit_application/saveGst&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="form_gst" class="form-horizontal">
       <input type="hidden" name="credit_application_id" />

     <div class="form-group">
      <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">GST Type</label>
            <div class="col-sm-9">
              <select name="gst_type" class="form-control edit_track"  data-block-name="general"  data-change="false">
                <option value="">-- select --</option>
                <option value="composition">Composition</option>
                <option value="Regular">Regular</option>
            </select>
          </div>
        </div>
      </div>

     <div class="form-group">
      <div class="row">
         <label class="col-sm-2 control-label" for="input-customer-group">GST Active</label>
            <div class="col-sm-9">
              <select name="gst_active" class="form-control edit_track"  data-block-name="general"  data-change="false">
                  <option value="">-- select --</option>
                  <option value="1">Active</option>
                <option value="0">Deactive</option>
            </select>
          </div>
        </div>
      </div>

     <div class="form-group">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Last Filling Date</label>
       <div class="col-sm-9">
      <div class="input-group date">
                  <input type="text" name="last_filling_date" value="" placeholder="Last Filling Date" data-date-format="YYYY-MM-DD" id="last_filling_date" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
      </div>
      </div>
      </div>

      <div class="form-group">
      <div class="row">
       <label class="col-sm-2 control-label" for="input-customer-group">Registration Date</label>
       <div class="col-sm-9">
      <div class="input-group date">
                  <input type="text" name="registration_date" value="" placeholder="Registration Date" data-date-format="YYYY-MM-DD" id="registration_date" class="form-control" />
                  <span class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
      </div>
      </div>
      </div>


        </form>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="save_gst_verification()"  class="btn btn-default"> Submit </button>
    </div>

  </div>
</div>
</div>


<div id="bank_details_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Customer Bank Detail</h3>
    </div>
    <div class="modal-body" id="bank_details">

    </div>
  </div>
</div>
</div>
<div id="location_details_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content" style="width: 800px;">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Location Detail</h3>
    </div>
    <div class="modal-body" id="location_details">

    </div>
  </div>
</div>
</div>


<div id="crif_json_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" style="margin-left: 8%">
  <!-- Modal content-->
  <div class="modal-content" style="width: 1130px;overflow: scroll;height: 800px;">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title">Credit Crif</h3>
    </div>
    <div class="modal-body" id="crif_json">
        <form id="form_crif_json" enctype="multipart/form-data" method="post">
        <input type="hidden" name="credit_application_id" />
         <input type="hidden" name="customer_id" />
        <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group">
                        <label class="control-label" for="input-name">Upload Json File</label>
                         <input type="file" name="filename" id="crif_json_file" class="form-control" />
                       </div>
                     </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                       <button type="button" id="crif_json_btn" class="btn btn-primary" style="display: block;margin-top: 22px;">Upload</button>
                       </div>
                     </div>
                     
                </div>
        </form>
        <div id="crif_json_content" style="text-align: center;">
           
        </div>
    </div>
  </div>
</div>
</div>


<div id="crif_modal" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
<div class="modal-dialog" >
  <!-- Modal content-->
   <div id="crif_loading" style="position: absolute;
    width: 800px;
    padding-left: 50%;
    padding-top: 40%;
    font-size: 30px;
    min-height: 544px;
    display: none;
    z-index: 9;"><i class="fa fa-circle-o-notch fa-spin"></i></div>
  <div class="modal-content" style="width: 800px;">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h3 class="modal-title" id="status_title">Customer Pancard Detail</h3>
    </div>
    <div class="modal-body" id="pancard_details">
        <div class="row">

              <div class="col-sm-6">
               <img id="pan_img" src="<?php echo STATIC_CONTENT_URL_SSL .'placeholder.jpg'; ?>" style="width: 100%;" />
              </div>

              <div class="col-sm-6">
              <form action="index.php?route=sale/customer_credit_application/crifScore&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data" id="crif_score" class="form-horizontal">

               <input type="hidden" name="credit_application_id" id="pan_credit_application_id" />

                <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">First name</label>
                   <div class="col-sm-9">
                      <input type="text" name="first_name" id="pan_first_name" placeholder="First name" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">Middle name</label>
                   <div class="col-sm-9">
                      <input type="text" name="middle_name" id="pan_middle_name" placeholder="Middle name" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">Last name</label>
                   <div class="col-sm-9">
                      <input type="text" name="last_name" id="pan_last_name" placeholder="Last name" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">Mobile</label>
                   <div class="col-sm-9">
                      <input type="text" name="mobile" id="pan_mobile" placeholder="Mobile" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">Email</label>
                   <div class="col-sm-9">
                      <input type="text" name="email" id="pan_email" placeholder="Email" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">DOB</label>
                   <div class="col-sm-9">
                       <div class="input-group date">
                        <input type="text" name="dob" value="" placeholder="DOB" data-date-format="DD-MM-YYYY" id="pan_dob" class="form-control" />
                        <span class="input-group-btn">
                         <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                         </span>
                        </div>
                    </div>
                  </div>
                 </div>

                 <div class="form-group">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">PAN Number</label>
                   <div class="col-sm-9">
                      <input type="text" name="pan_number" id="pan_number" placeholder="PAN Number" class="form-control" />
                    </div>
                  </div>
                 </div>

                 <div class="form-group" style="display: none;">
                 <div class="row">
                    <label class="col-sm-2 control-label" for="input-customer-group">Customer ID</label>
                   <div class="col-sm-9">
                      <input type="text" name="customer_id" id="pan_customer_id" placeholder="Customer ID" class="form-control" />
                    </div>
                  </div>
                 </div>
                 </form>
              </div>
          </div>
    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
    <button type="button" onclick="add_Crif_score()"  class="btn btn-default"> Get Crif Score </button>
    </div>
  </div>
</div>
</div>


<form enctype="multipart/form-data" method="post" class="fi_document_form"> 
 <input type="file" name="fi_document_file" class="fi_document_file" style="display: none;" />
 <input type="hidden" name="credit_application_id" value="" class="fi_document_id" />
</form> 

<!-- Customer Credit Pre-Approved -->
<div id="send_customer_credit_preapproved" class="modal fade in " role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" >
    <!-- Modal content-->
    <div class="modal-content" style="width: 800px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title" id="rbl_status_title">Send To RBL Bank For Credit Pre-Approval</h3>
      </div>
      <form id="creditPreApprovalForm" method="post" action="" >
        <div class="modal-body" id="pre_approved_credit">
            <!--Customer Credit PreApproval Data -->
        </div>
        <input type="hidden" name="pre_approval_customer_id" id="pre_approval_customer_id" value="" >
        <input type="hidden" name="pre_approval_credit_application_id" id="pre_approval_credit_application_id"  value="">
      </form>
      <div id="pre-approval-status" style="text-align: center"></div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-default"> Cancel </button>
        <button type="button" class="btn btn-default send-customer-pre-approval"> Submit </button>
      </div>
    </div>
  </div>
</div>
<!-- Customer Credit Pre-Approved -->


<style>
.table-wrapper-scroll-y {
  display: block;
  max-height: 470px;
  overflow-y: auto;
  -ms-overflow-style: -ms-autohiding-scrollbar;
}
.short_sms_data {height: 500px;
    overflow: hidden;
    overflow-y: scroll;}
#response_text > div{ float: left; }
#response_text .alert{ padding : 0px 15px; margin-bottom: 10px;}

</style>
<script type="text/javascript"><!--


$("body").delegate(".send_customer_credit_preapproved", "click", function(){
  $('#send_customer_credit_preapproved').modal();
    var customer_id = $(this).data('id');
    var credit_application_id = $(this).data('customer_credit_application_id');
    $('#pre_approval_customer_id').val(customer_id);
    $('#pre_approval_credit_application_id').val(credit_application_id);
    $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getPreApprovedCreditData&token=<?php echo $token; ?>&customer_id=' + customer_id+'&credit_application_id='+credit_application_id,
            dataType: "html",
            success: function(data) {
               $('#pre_approved_credit').html(data);
               var rbl_status = $('#rbl_status').val();
               if(rbl_status !=''){
                  $(".send-customer-pre-approval").hide();
               }else{
                  $(".send-customer-pre-approval").show();
               }
               var pop_up_title = 'Send To RBL For Credit Pre-Approval';
               if(rbl_status == 'on_boarding'){
                  pop_up_title = 'RBL Pre-On-Boarding Status';
               }else if(rbl_status == 'pre_approved_status'){
                  pop_up_title = 'RBL Pre-Approved Status';
               }else if(rbl_status == 'discrepency_status'){
                  pop_up_title = 'RBL Discrepency Status';
               }else if(rbl_status == 'cif_created' || rbl_status == 'activated_by_rbl') {
                  pop_up_title = 'RBL Credit Approved';
               }
               $("#rbl_status_title").html(pop_up_title);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
});

$(document).on('click','.send-customer-pre-approval',function(){
  
  var is_delivery_address = $('#is_delivery_address').val();
  var alert_msg = "Are you sure!!";
  if(is_delivery_address == 0) {
      alert_msg = "We do not have a delivery address for this customer so sending the permanent address in the delivery address.";
  }

  if(window.confirm(alert_msg))
  {
    var customer_id = $('#pre_approval_customer_id').val();
    var credit_application_id = $('#pre_approval_credit_application_id').val();
    var ajax_loader = '<img src="<?php echo HTTPS_CATALOG?>khufiya_vibhag/view/image/ajax-loader.gif">';
    $.ajax({
            type: 'GET',
            url: 'index.php?route=sale/customer_credit_application/sendPreApprovedCreditData&token=<?php echo $token; ?>&customer_id=' + customer_id+'&credit_application_id='+credit_application_id,
            dataType: "json",
            beforeSend: function() {
              $('#pre-approval-status').html(ajax_loader);
            },
            success: function(response) {
              //console.log(response);
              $.each(response,function(index,item){
                  if(index == 'success') {
                    $('#pre-approval-status').html('<p style="color:#07ad2b">'+item+'</p>');
                    location.reload();
                  }else if(index == 'error') {
                    $('#pre-approval-status').html('<p style="color:#ff0000">'+item+'</p>');  
                  }
              });
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var error = thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText;
                $('#pre-approval-status').html(error); 
            }
        });
  }

  
})


$('#share_credit_application_popup').on('hidden.bs.modal', function () {
 location.reload();
});
$('#credit_application_edit').on('hidden.bs.modal', function () {
 location.reload();
});

$('#button-filter').on('click', function() {
  url = 'index.php?route=sale/customer_credit_application/index&token=<?php echo $token; ?>';

  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_step = $('input[name=\'filter_step\']').val();

  if (filter_step) {
    url += '&filter_step=' + encodeURIComponent(filter_step);
  }

  var filter_email = $('input[name=\'filter_email\']').val();

  if (filter_email) {
    url += '&filter_email=' + encodeURIComponent(filter_email);
  }

    var filter_telephone = $('input[name=\'filter_telephone\']').val();

  if (filter_telephone) {
    url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
  }

  var filter_date_added = $('input[name=\'filter_date_added\']').val();

  if (filter_date_added) {
    url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
  }

   var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

  if (filter_date_modified) {
    url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
  }

  var filter_followup_date = $('input[name=\'filter_followup_date\']').val();

  if (filter_followup_date) {
    url += '&filter_followup_date=' + encodeURIComponent(filter_followup_date);
  }

var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

  if (filter_customer_id) {
    url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
  }

  var filter_city = $('input[name=\'filter_city\']').val();

  if (filter_city) {
    url += '&filter_city=' + encodeURIComponent(filter_city);
  }

  var filter_state = $('select[name=\'filter_state\']').val();

  if (filter_state) {
    url += '&filter_state=' + encodeURIComponent(filter_state);
  }


  var filter_application_id = $('input[name=\'filter_application_id\']').val();

  if (filter_application_id) {
    url += '&filter_application_id=' + encodeURIComponent(filter_application_id);
  }
    var filter_pancard = $('#filter_pancard').val();
  if (filter_pancard) {
    url += '&filter_pancard=' + encodeURIComponent(filter_pancard);
  }

      var filter_document_type = $('#filter_document_type').val();
  if (filter_document_type) {
    url += '&filter_document_type=' + encodeURIComponent(filter_document_type);
  }

      var filter_document_status = $('#filter_document_status').val();
  if (filter_document_status) {
    url += '&filter_document_status=' + encodeURIComponent(filter_document_status);
  }

  var filter_rbl_approved = $('#filter_rbl_approved').val();
  if (filter_rbl_approved) {
    url += '&filter_rbl_approved=' + encodeURIComponent(filter_rbl_approved);
  }

  var filter_discrepency_status = $('#filter_discrepency_status').val();
  if (filter_discrepency_status) {
    url += '&filter_discrepency_status=' + encodeURIComponent(filter_discrepency_status);
  }

  var filter_sms_log_type = $('#filter_sms_log_type').val();
   if (filter_sms_log_type) {
     url += '&filter_sms_log_type=' + encodeURIComponent(filter_sms_log_type);
   }



  if($('input[name=\'filter_by_crif\']').prop("checked") == true){
           url += '&filter_by_crif=1';
  }


  location = url;
});

$('#button-download').on('click', function() {
  url = 'index.php?route=sale/customer_credit_application/download_csv&token=<?php echo $token; ?>';

  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_step = $('input[name=\'filter_step\']').val();

  if (filter_step) {
    url += '&filter_step=' + encodeURIComponent(filter_step);
  }

  var filter_email = $('input[name=\'filter_email\']').val();

  if (filter_email) {
    url += '&filter_email=' + encodeURIComponent(filter_email);
  }

    var filter_telephone = $('input[name=\'filter_telephone\']').val();

  if (filter_telephone) {
    url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
  }

  var filter_date_added = $('input[name=\'filter_date_added\']').val();

  if (filter_date_added) {
    url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
  }

   var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

  if (filter_date_modified) {
    url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
  }

  var filter_followup_date = $('input[name=\'filter_followup_date\']').val();

  if (filter_followup_date) {
    url += '&filter_followup_date=' + encodeURIComponent(filter_followup_date);
  }

var filter_customer_id = $('input[name=\'filter_customer_id\']').val();

  if (filter_customer_id) {
    url += '&filter_customer_id=' + encodeURIComponent(filter_customer_id);
  }

  var filter_city = $('input[name=\'filter_city\']').val();

  if (filter_city) {
    url += '&filter_city=' + encodeURIComponent(filter_city);
  }

  var filter_state = $('select[name=\'filter_state\']').val();

  if (filter_state) {
    url += '&filter_state=' + encodeURIComponent(filter_state);
  }


  var filter_application_id = $('input[name=\'filter_application_id\']').val();

  if (filter_application_id) {
    url += '&filter_application_id=' + encodeURIComponent(filter_application_id);
  }
    var filter_pancard = $('#filter_pancard').val();
  if (filter_pancard) {
    url += '&filter_pancard=' + encodeURIComponent(filter_pancard);
  }

      var filter_document_type = $('#filter_document_type').val();
  if (filter_document_type) {
    url += '&filter_document_type=' + encodeURIComponent(filter_document_type);
  }

      var filter_document_status = $('#filter_document_status').val();
  if (filter_document_status) {
    url += '&filter_document_status=' + encodeURIComponent(filter_document_status);
  }

  var filter_sms_log_type = $('#filter_sms_log_type').val();
   if (filter_sms_log_type) {
     url += '&filter_sms_log_type=' + encodeURIComponent(filter_sms_log_type);
   }

  if($('input[name=\'filter_by_crif\']').prop("checked") == true){
           url += '&filter_by_crif=1';
  }

  location = url;
});
//--></script>

  <script type="text/javascript">
$('.date').datetimepicker({
  pickTime: false
});

$(document).ready(function(){

   $("#form_filter input, #form_filter select").keypress(function(e) {
    if(e.which == 13) {

        $("#button-filter").click();
    }
  });

   $('#form_filter button[type=\'reset\']').click(function(e) {
     $('#form_filter input[type=\'text\']').each(function (){
               $(this).attr('value','');
      });
     $('#form_filter select').each(function (){
      $(this).find("option").attr('selected', false);
      $(this).find("option[value='']").attr('selected', true);
     });
     $('#form_filter input[name=\'filter_by_crif\']').removeAttr("checked");
    });

    $("body").delegate(".send_credit_application_notifiction", "click", function(){
        var customer_id = $(this).data('id');
        var credit_application_id = $(this).data('customer_credit_application_id');

        $('#credit_application_form_id').val(credit_application_id);
        $('#credit_application_customer_id').val(customer_id);

        getCreditApplicationSentNotification(credit_application_id);

        $('#credit_application_notification').modal();

    });

    $("body").delegate(".credit_application_customer_images", "click", function(){
        var customer_id = $(this).data('id');
        var images = $(this).attr('data-customer-images');

        logs = '';
         $(images.split(',')).each(function( index, value ) {

                   logs +=  '<div class="col-sm-4"><img src='+ value +' alt="lead_images" width="150" height="150" style="margin:10px;"></div>';



                    });

          $('#lead_customer_images').html(logs);
        $('#credit_application_cusotmer_images').modal();

    });




    $('#send_notification_to_credit_custmer').on('click',function(e){
        e.preventDefault();

        credit_application_id = $('#credit_application_form_id').val();
        credit_customer_id = $('#credit_application_customer_id').val();
        message = $('#credit_application_notification_message').val();

        if($.trim(credit_application_id) == '' || $.trim(credit_customer_id) == '') {
            alert("Oops Developer Fault, Please Try Again");
            $('#credit_application_notification').modal('hide');
        }

        if($.trim(message) == ''){
            alert("Please Enter Text to send Notification to Customer");
            return false;
        }


          $.ajax({
            url: 'index.php?route=sale/customer_credit_application/sendNotificationToCustomer&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            type: "POST",
            data: $('#creditApplicationForm').serialize(),
            success: function(data){
                alert(data.message);
                $('#creditApplicationForm').trigger("reset");
                 $('#credit_application_notification').modal('hide');
            }

        });

    });



	$('.error_message').hide();
    $('.success_message').hide();
    $('.error_message_status').hide();
    $('.success_message_status').hide();
    $('.share_popup').click(function(){
    	var credit_application_id = $(this).data('id');
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/shareApplication&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(json) {
                        $('.credit_partners_list_div').html(json);
                        $('#share_credit_application_popup').modal('show');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $('.history_popup').click(function(){
      var credit_application_id = $(this).data('id');
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/creditApplicationLog&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(json) {
                        $('.credit_application_log_div').html(json);
                        $('#credit_application_log').modal('show');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    //
    $("body").delegate(".share_link", "click", function(){
    	var share_url = $(this).data('id');
    	$('.error_message').hide();
    	$('.success_message').hide();
        $.ajax({
            url: share_url,
            dataType: "json",
            success: function(data) {
            	if(data['status'] == '0'){
            		$('.error_message_span').html(data['message']);
            		$('.error_message').show();
            		$('.success_message').hide();
            	}else{
            		$('.success_message_span').html(data['message']);
            		$('.success_message').show();
            		$('.error_message').hide();
                // alert(data['redirect_url']);
                if(data['redirect_url']){
                  window.open(data['redirect_url'],'_blank');
                }
                // location.href = data['redirect_url'];
            	}
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });
    $("body").delegate(".kyc_docs", "click", function(){
      var credit_application_id = $(this).data('id');
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/shareKycDocumentstoEpaylater&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {
                alert(data['message']);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });
    $("body").delegate(".application_status_chk", "click", function(){
    	var credit_application_id = $(this).data('id');
    	$('.error_message_status').hide();
    	$('.success_message_status').hide();
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/checkApplicationStatus&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {
              if(data['status'] == '1'){
            	$('.credit_status_div').html(data['message']);
              $('#credit_application_status_popup').modal('show');
              } else{
                alert(data['message']);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });

    $("body").delegate(".edit_application", "click", function(){
      var customer_id = $(this).data('id');
      var credit_application_id = $(this).data('customer_credit_application_id');
      if(customer_id > 0){
          $.ajax({
            url: 'index.php?route=sale/customer_credit_application/edit&token=<?php echo $token; ?>&customer_id=' + customer_id,
            dataType: "json",
            success: function(data) {
              if(data['status'] == '1'){
                $('.credit_application_iframe').html("<iframe src='<?php echo HTTPS_CATALOG; ?>index.php?route=account/credit_application&customer_id=" + customer_id + "&credit_application_id=" + credit_application_id + "&token="+ data['token']+"&khufiya_user_id="+ data['user_id']+"' style='height:600px;width:800px;'></iframe>");
                $('#credit_application_edit').modal('show');
              }else{

              }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
      }else{
         $('.credit_application_iframe').html("<iframe src='<?php echo HTTPS_CATALOG; ?>index.php?route=account/credit_application&customer_id=" + customer_id + "&token=<?php echo $token; ?>&khufiya_user_id=<?php echo $khufiya_user_id;?>&credit_application_id="+ credit_application_id+"' style='height:600px;width:800px;'></iframe>");
                $('#credit_application_edit').modal('show');

      }


    });

     $(document).on("click","#add_application", function(){
        // $.ajax({
        //     url: 'index.php?route=sale/customer_credit_application/edit&token=<?php echo $token; ?>',
        //     dataType: "json",
        //     success: function(data) {
        //       if(data['status'] == '1'){
        //         $('.credit_application_iframe').html("<iframe src='<?php echo HTTPS_CATALOG; ?>index.php?route=account/credit_application&token="+ data['token']+"&khufiya_user_id="+ data['user_id']+"' style='height:600px;width:800px;'></iframe>");
        //         $('#credit_application_add').modal('show');
        //       }

        //     },
        //     error: function(xhr, ajaxOptions, thrownError) {
        //         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        //     }
        // });
        $('.credit_application_iframe').html("<iframe src='<?php echo HTTPS_CATALOG; ?>index.php?route=account/credit_application&token="+ '<?php echo $token; ?>'+"&khufiya_user_id=<?php echo $khufiya_user_id;?>&customer_id=0' style='height:600px;width:800px;'></iframe>");
                $('#credit_application_add').modal('show');

    });

    $("body").delegate(".verify_otp_confirm", "click", function(){
      var type = this.id;
      var credit_application_id = $(this).data('id');
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/ePayLaterVerifyOTP&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id + '&flag_type='+ type + "&is_checked=" + this.checked,
            dataType: "json",
            success: function(data) {
             alert(data['message']);

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    });


    $(document).on('click','.filter_document_status_change',function(){
          $("#form_status_remarks input[name='credit_application_id']").val($(this).data('id'));
          $("#form_status_remarks input[name='telephone']").val($(this).data('telephone'));
          $("#form_status_remarks input[type='text']").val('');
          $("#form_status_remarks textarea").val('');
          $("#form_status_remarks #document_status").val('');
          $("#form_status_remarks #document_status").trigger("change");
          $('#credit_application_status_remarks').modal('show');
          get_document_status($(this).data('id'));

    });

    $(document).on('click','.view_home_loc_table',function(){
      $('.shop_loc_table').hide();
      $('.home_loc_table').toggle();
      
    });

    $(document).on('click','.view_shop_loc_table',function(){
      $('.home_loc_table').hide();
      $('.shop_loc_table').toggle();
    });


        $(document).on('change','#document_status',function(){
          var document_status = $(this).val();
          $(".reason_select").html('');
          $(".reason_select").html($("#"+$(this).val()+"_reason").html());
          if(document_status == 'approved_document_received' || document_status== 'approved_but_agreement_pending' || document_status== 'approved_without_bank_statement' || document_status== 'approved_but_not_interested')
          {
             $("#approved_credit_limit").show();
          }
          else
          {
            $("#form_status_remarks input[name='credit_limit']").val('');
             $("#approved_credit_limit").hide();
          }

          if(document_status == 'approved_document_received'){
            $("#payment_cycle_div").show();
          }else{
            $("#payment_cycle_div").hide();
          }

        });

$(document).on('click','.fi_document', function() {
  var id = $(this).data('id');
  $(".fi_document_id").val(id);
  $(".fi_document_file").click();

}); 

$(document).on('change','.fi_document_file', function() {
  $(".fi_document_form").submit();
});  


$(".fi_document_form").submit(function(e) {
    e.preventDefault();    

    var formData = new FormData(this);
    var id = $(".fi_document_id").val();
    $("#fi_document_"+id).hide();
    $("#fi_document_loading_"+id).show();

    $.ajax({
        url: 'index.php?route=sale/customer_credit_application/uploadFIDocument&token=<?php echo $token; ?>',
        type: 'post',
        data: formData,
        dataType: "json",
        success: function (data) {
            location.reload();
        },
        cache: false,
        contentType: false,
        processData: false
    });
})      

       (function($) {
        $.ucfirst = function(str) {
        var text = str;
        var parts = text.split(' '),
            len = parts.length,
            i, words = [];
        for (i = 0; i < len; i++) {
            var part = parts[i];
            var first = part[0].toUpperCase();
            var rest = part.substring(1, part.length);
            var word = first + rest;
            words.push(word);
        }
        return words.join(' '); 
      };})(jQuery);

    });
     
     function fi_document_mail(data)
     {
       var file_name             = $(data).data('file');
       var credit_application_id = $(data).data('id');

       $.ajax({
          url: 'index.php?route=sale/customer_credit_application/sendDocumentAttachmentMail&token=<?php echo $token; ?>&file_name='+file_name+'&credit_application_id='+credit_application_id,
                 type: 'get',
                 dataType: "json",
                 success: function (data) 
                 {
                   alert(data.message);
                 },
                 cache: false,
                 contentType: false,
                 processData: false
                });
       }

     function change_status_remarks()
     {
          var credit_application_id = $("#form_status_remarks input[name='credit_application_id']").val();

          var document_status =  $("#document_status").val();
          document_status =  document_status.replace("_", " ");
          document_status =  document_status.replace("_", " ");
          document_status =  document_status.charAt(0).toUpperCase() + document_status.slice(1);

          if($("#document_status").val() == '')
          {
           alert("please select a status");
          }
          else if($.trim($("#form_status_remarks textarea[name='remark']").val()) == '')
          {
           alert("please enter comment");
          }
          else
          {
            if(confirm("Are you sure?"))
            {
              var form_data = $("#form_status_remarks").serialize();
              $('#credit_application_status_remarks').modal('hide');
              $.ajax({
              url: 'index.php?route=sale/customer_credit_application/updateDocumentStatus&token=<?php echo $token; ?>',
              dataType: "json",
              data: form_data,
              success: function(data) {
              $("#filter_document_status_change_"+credit_application_id).html(document_status);
              alert(data['message']);
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
              });
           }
        }
     }

    function get_email_list(customer_id){
        $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getEmailList&token=<?php echo $token; ?>&customer_id=' + customer_id,
            dataType: "html",
            success: function(json) {
                        $('.email_list_div').html(json);
                        $('#email_list_model').modal('show');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    }

    function get_bank_details(customer_id)
    {
       $.ajax({
           url: 'index.php?route=sale/customer_credit_application/get_bank_details&token=<?php echo $token; ?>&customer_id='+customer_id,
           dataType: "html",
          success: function(data) {
              $("#bank_details").html(data);
              $('#bank_details_modal').modal('show');
              },
           error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
              });
    }

  function get_location_details(customer_id)
    {
       $.ajax({
           url: 'index.php?route=sale/customer_credit_application/get_location_details&token=<?php echo $token; ?>&customer_id='+customer_id,
           dataType: "html",
          success: function(data) {
              $("#location_details").html(data);
              $('#location_details_modal').modal('show');
              },
           error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
              });
    }

    function get_notes(credit_application_id, telephone)
    {

        $("#form_notes input[name='credit_application_id']").val(credit_application_id);
        $("#form_notes input[name='telephone']").val(telephone);
        $('#credit_application_notes').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getDocumentStatus&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(data) {
             $("#note_data").html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    function get_gst_verification(credit_application_id)
    {

        $("#form_gst input[name='credit_application_id']").val(credit_application_id);
        $('#gst_verification_modal').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getGstVerification&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(data) {
             $("#gst_data").html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

     function save_gst_verification()
     {

            var last_filling_date = $("#form_gst input[name='last_filling_date']").val();
            var registration_date = $("#form_gst input[name='registration_date']").val();
           if(last_filling_date == '')
           {
             alert("please enter last filling date");
           }
           else if(registration_date == '')
           {
             alert("please enter registration date");
           }
           else
           {
            var form_data = $("#form_gst").serialize();
             $.ajax({
            url: 'index.php?route=sale/customer_credit_application/saveGstVerification&token=<?php echo $token; ?>',
            dataType: "json",
            data: form_data,
            success: function(data) {
             alert(data['message']);
             $('#gst_verification_modal').modal('hide');
             $("#form_gst input[name='last_filling_date']").val('');
             $("#form_gst input[name='registration_date']").val('');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
        }

     }

    function get_document_status(credit_application_id)
    {

         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getDocumentStatus&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(data) {
             $("#document_status_data").html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

    }

    function get_document_checklist(credit_application_id)
    {
         $("#form_checklist input[name='credit_application_id']").val(credit_application_id);
         $('#document_checklist_modal').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getDocumentChecklist&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {

              $('#form_checklist input[type=checkbox]').each(function ()
              {
                 var checkbox = $(this).attr('id');
                 var checklist_id = $(this).val();
                 $("#"+checkbox).prop('checked', false);
                  for(i=0; i< data.length; i++)
                  {
                     if(data[i].credit_checklist_id == checklist_id)
                     {
                       $("#"+checkbox).prop('checked', true);
                     }
                  }

             });

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    function save_document_checklist()
    {
          var form_data = $("#form_checklist").serialize();
             $.ajax({
            url: 'index.php?route=sale/customer_credit_application/saveDocumentChecklist&token=<?php echo $token; ?>',
            dataType: "json",
            type: "POST",
            data: form_data,
            success: function(data) {
              alert(data['message']);
              $('#document_checklist_modal').modal('hide');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    function get_questions(credit_application_id)
    {
         $("#form_questions input[name='credit_application_id']").val(credit_application_id);
         $('#document_questions_modal').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getQuestionsAndAnswer&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {
              
              $("#form_questions input[type='text']").val('');
               $("#form_questions input[type='checkbox']").prop('checked', false);
              for(i=0; i< data.length; i++)
              {
                if($("#answer"+data[i].credit_questionnaire_id).attr("type") == 'checkbox')
                {
                   var answer = data[i].answer.split(",");

                    $('.answer'+data[i].credit_questionnaire_id).each(function () {  
                     
                        if(answer.indexOf($(this).val()) == -1)
                        {   
                          $(this).prop('checked', false);
                        }
                        else
                        {
                           $(this).prop('checked', true); 
                        }
                    });
                }
                else
                {
                  $("#answer"+data[i].credit_questionnaire_id).val(data[i].answer);
                }
              }

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    function save_questions()
    {
          var form_data = $("#form_questions").serialize();
            $.ajax({
            url: 'index.php?route=sale/customer_credit_application/saveQuestions&token=<?php echo $token; ?>',
            dataType: "json",
            type: "POST",
            data: form_data,
            success: function(data) {
              alert(data['message']);
              $('#document_questions_modal').modal('hide');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }


    function save_note()
     {
          var credit_application_id = $("#form_notes input[name='credit_application_id']").val();
          if($.trim($("#form_notes textarea[name='note']").val()) == '')
          {
           alert("please enter a note");
          }
          else
          {
            var form_data = $("#form_notes").serialize();
             $.ajax({
            url: 'index.php?route=sale/customer_credit_application/addNote&token=<?php echo $token; ?>',
            dataType: "json",
            data: form_data,
            success: function(data) {

              var last_note = $("#form_notes textarea").val();
              $("#last_note_"+credit_application_id).html(last_note.substr(0, 25)+'...');

              $("#form_notes textarea").val('');
              get_notes(credit_application_id)
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
        }
     }


     function getCreditApplicationSentNotification(credit_application_id) {

         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/getCreditApplicationSentNotification&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "html",
            success: function(data) {
             $("#notificationLogs").html(data);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

 function get_pan_detail(credit_application_id)
 {       
         $("#pan_credit_application_id").val(credit_application_id);
         $('#crif_modal').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/get_pan_detail&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {
              $("#pan_first_name").val(data.first_name);
              $("#pan_middle_name").val(data.middle_name);
              $("#pan_last_name").val(data.last_name);
              $("#pan_email").val(data.email);
              $("#pan_mobile").val(data.phone_no);
              $("#pan_number").val(data.pan_no);
              $("#pan_customer_id").val(data.customer_id);
              $("#pan_dob").val(data.dob);
              $("#pan_img").attr('src',data.file_path);
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
 }  

 function add_Crif_score()
 {
   if(confirm('Make sure all details are matched with PAN'))
   {
     $("#crif_loading").show();
     $('#pancard_details').addClass('disable');
     var form_data = $("#crif_score").serialize();
    $.ajax({
      url: 'index.php?route=sale/customer_credit_application/crif_score&token=<?php echo $token; ?>',
      dataType: "json",
      type: "POST",
      data: form_data,
       success: function(data) 
        {
           $("#crif_loading").hide();
           $('#pancard_details').removeClass('disable');
           if(data.errors)
           {
             alert(data.errors[0]);
           }
           else if(data.status == 'Success')
           {
             alert("Credit Score: "+data.crif_score);
             location.reload();
           } 
           else if (data.eplErrorCode) {
             alert("EPayLater Error: " + data.message);
           }
           else
           {
             alert(data.message);
           }

        },
       error: function(xhr, ajaxOptions, thrownError) 
       {
           $("#crif_loading").hide();
           $('#pancard_details').removeClass('disable');
         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }
 } 

function copyText(Content) 
{
   var dummy = $('<input>').val(Content).appendTo('body').select();
   document.execCommand('copy');
}

function create_customer(credit_application_id)
{
 
  $("#create_user_icon_"+credit_application_id).html('<i class="fa fa-circle-o-notch fa-spin"></i>');

      $.ajax({
      url: 'index.php?route=sale/customer_credit_application/create_customer&token=<?php echo $token; ?>&credit_application_id='+credit_application_id,
      dataType: "json",
      type: "POST",
       success: function(data) 
        {
           if(data.customer_id > 0)
           {
             $("#create_user_icon_"+credit_application_id).html(data.customer_id);
           }
           else
           {
            alert("unable to create customer");
            $("#create_user_icon_"+credit_application_id).html('<i class="fa fa-user" aria-hidden="true"></i>');
           }
        },
       error: function(xhr, ajaxOptions, thrownError) 
       {
         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });

}

function change_document(document_id)
{
  var name = $("#document_status_change_"+document_id).val();
  $.ajax({
      url: 'index.php?route=sale/customer_credit_application/change_document_type&token=<?php echo $token; ?>&document_id='+document_id+'&name='+name,
      dataType: "json",
      type: "POST",
       success: function(data) 
        {
           alert(data.message);
        },
       error: function(xhr, ajaxOptions, thrownError) 
       {
         alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
}

function get_crif_json(credit_application_id, customer_id)
{        
         $("#form_crif_json input[name='credit_application_id']").val(credit_application_id); 
         $("#form_crif_json input[name='customer_id']").val(customer_id); 
         $("#crif_json_content").html('<i class="fa fa-refresh fa-spin" style="font-size:24px"></i>');
         $('#crif_json_modal').modal('show');
         $.ajax({
            url: 'index.php?route=sale/customer_credit_application/get_crif_json&token=<?php echo $token; ?>&credit_application_id=' + credit_application_id,
            dataType: "json",
            success: function(data) {
              if(data.success == 0)
              {
                $("#form_crif_json").show();
                $("#crif_json_content").html('');
              } 
              else
              {
                 $("#form_crif_json").hide();
                 $("#crif_json_content").html(data.content);
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

}



$(document).on('click','#crif_json_btn', function() {
 if($("#crif_json_file").val() != '')
  {
    $("#crif_json_content").html('<i class="fa fa-refresh fa-spin" style="font-size:24px"></i>');
    $("#form_crif_json").submit();
   }
  else
   {
     alert("please select json file");
   }
});  

$("#form_crif_json").submit(function(e) {
    e.preventDefault();    
    var formData = new FormData(this);
     $("#form_crif_json").hide();
    $.ajax({
        url: 'index.php?route=sale/customer_credit_application/uploadCrifDocument&token=<?php echo $token; ?>',
        type: 'post',
        data: formData,
        dataType: "json",
        success: function (data) {
          $("#crif_json_file").val('');
          if(data.success == 1)
          {
            $("#form_crif_json").hide();
            $("#crif_json_content").html(data.content);
          }
          else
          {  
              alert(data.message); 
              $("#form_crif_json").show();
              $("#crif_json_content").html(''); 
          }  
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$('#bank_account_last_digit').on('keypress', function (event) {
    var regex = new RegExp("^[a-zA-Z0-9]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
       event.preventDefault();
       return false;
    }
}); 
           
</script>

<script>
var customer_credit_application_id = 0;
// Open the Modal
function openModal(id) {
  customer_credit_application_id = id;
  document.getElementById('documentModal_'+id).style.display = "block";
}

// Close the Modal
function closeModal(id) {
  document.getElementById('documentModal_'+id).style.display = "none";
}

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = $("#slides_"+customer_credit_application_id+" .mySlides");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex-1].style.display = "block";
}
</script>

</div>
<?php echo $footer; ?>

