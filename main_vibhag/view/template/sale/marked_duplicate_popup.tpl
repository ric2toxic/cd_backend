<div class="modal-dialog" >
  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Customer Filter Form</h4>
    </div>
    <div class="modal-body" style=" height: 167px;">
      <form id="duplicate_customer_search" method="post" class="form-horizontal">
          <div class="form-group col-md-12">
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_name" placeholder="Name" />
              </div>
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_email" placeholder="Email" />
              </div>
               <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_phone" placeholder="Phone" />
              </div>
          </div>
          <div class="form-group col-md-12">
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="company_name" placeholder="Company Name" />
              </div>             
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_city" placeholder="City" />
              </div>
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_cid" placeholder="Customer CID" />
              </div>              
          </div>
          <div class="form-group col-md-12">
              <div class="col-xs-4">
                  <input type="text" class="form-control" name="customer_postcode" placeholder="Postcode" />
              </div>
              <div class="col-xs-8">
                  <div style="float: right;">
                    <button id="set_marked_as_master" data-ordercustomerid="" style="display: none;" type="button" class="btn btn-primary">Mark as MasterID for this Customer</button>
                  </div>
                  <div style="float: right;">
                    <button style="margin-right: 5px;" id="get_duplicate_record" type="button" class="btn btn-primary">Get Record</button>
                  </div>
                  <div style="float: right;">
                    <img class="hide" style="padding-top: 10px;" id="get_record_loading" src="view/image/ajax-loader.gif" />
                  </div>
              </div>
          </div>
      </form>
    </div>
    <div class="" id="duplicate_customer_search_modal"></div>
    <div class="modal-footer">     
    </div>
  </div>
</div>