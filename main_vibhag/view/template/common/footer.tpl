<footer id="footer"><?php echo $text_footer; ?></footer></div>
<!--Overlay Screen -->
  <div class="overlay-screen" style="text-align:center; display: none;" >
    <i class="fa fa-refresh fa-spin" style="font-size:40px; color:#000; margin-top:25%"></i>
  </div>
<!-- Modal -->
<div id="pickupIssueModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pickup Issue</h4>
      </div>
      <div class="modal-body">
            <div class="row">
            <div class="col-sm-6">
              <h4>Pickup/Seller Detail</h4>
              <table class="table table-bordered">
                <tbody>
                  <td>Pickup Name: </td>
                  <td class="pickup_name"></td>
                </tr>
                 <tr>
                  <td>Pickup Number: </td>
                  <td class="pickup_number"></td>
                </tr>                
                 <tr>
                  <td>Pickup Address:</td>
                  <td class="pickup_address"></td>
                 </tr>
                 <tr>
                  <td>Pickup City:</td>
                  <td class="pickup_city"></td>
                 </tr>
                 <tr>
                  <td>Pickup Pincode:</td>
                  <td class="pickup_pincode"></td>
                 </tr>
                 <tr>
                  <td>Seller Id: </td>
                  <td class="seller_id"></td>
                </tr>
                <tr>
                  <td>Seller Name:</td>
                  <td class="seller"></td>
                </tr>
                 <tr>
                  <td>Company:</td>
                  <td class="company"></td>
                 </tr>
                 <tr>
              </tbody></table>
            </div>

            <div class="col-sm-6">
              <h4>Order Detail</h4>
              <table class="table table-bordered">
                <tbody>
                <tr>
                  <td>Order No.: </td>
                  <td class="order_no"></td>
                </tr>
                 <tr>
                  <td>Order Product Id: </td>
                  <td class="order_product_id"></td>
                </tr>
                <tr>
                  <td>Product Name: </td>
                  <td class="product_name"></td>
                </tr>
                 <tr>
                  <td>Product Model: </td>
                  <td class="product_model"></td>
                </tr>
                <tr>
                  <td>Product Pieces: </td>
                  <td class="total_pieces"></td>
                </tr>
                <tr>
                  <td>Product Total: </td>
                  <td class="total_price"></td>
                </tr>
                 <tr>
                  <td>Total Set: </td>
                  <td class="total_set"></td>
                </tr>
                <tr>
                  <td>Comment: </td>
                  <td class="product_comment"></td>
                </tr>
              </tbody></table>
            </div>
 
       </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-order_product_id="0" id="pickup_issue_slove" onclick="slove_pickup_issue();">Solve</button>
      </div>
    </div>

  </div>
</div>

<?php if(KEYBOARD_DISABLED){ ?>
	<script type="text/javascript">
			$(document).ready(function(){
				//Disable cut copy paste
			    $("body").bind("cut copy", function (e) {
			        e.preventDefault();
			    });
			   
			    //Disable mouse right click
			    $("body").on("contextmenu",function(e){
			        return false;
			    });
			});

			$(document).keydown(function(event){
			    if(event.keyCode==123){
			   	 return false;
			   	}
			   	if(event.ctrlKey) {
			      if(event.keyCode==85) 
			        return false;
			    }
			});
	</script>
	<style type="text/css">
		body{
		    -webkit-touch-callout: none;
		    -webkit-user-select: none;
		    -khtml-user-select: none;
		    -moz-user-select: none;
		    -ms-user-select: none;
		    user-select: none;
		}
	</style>
<?php }?>
</body></html>

<?php if(isset($_REQUEST['profiling']) && $_REQUEST['profiling'] == DEBUG_SQL_PROFILE) { ?>
    <iframe src='index.php?route=download/download/debugSql&token=<?php echo $_GET['token']; ?>&download_sql=<?php echo DEBUG_SQL_PROFILE ; ?>'  width="0" height="0"></iframe>
<?php } ?>