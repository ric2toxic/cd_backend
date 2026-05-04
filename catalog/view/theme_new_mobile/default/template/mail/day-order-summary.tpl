<div style="width:600px;margin:0 auto;border:#dfdfdf solid 1px;background:#f1f1f1">
    <div style="width:560px;margin:0 auto;text-align:center;padding:34px 0">
        WHOLESALEBOX
    </div>
    <img src="http://www.wholesalebox.in/image/blank_600x1.png" alt="WholesaleBox"></img>

    <div style="background:#f8f8f8">
            <div style="width:560px;margin:0 auto;text-align:center;padding:10px 0">
                <?php echo date("l jS \of F Y h:i:s A");?>
            </div>

        <div style="padding:18px 20px 25px 34px;border-bottom:#ececec solid 1px">

            <div style="float:left;color:#333333;font:normal 14px Arial,Helvetica,sans-serif;width:100%">
                <div style="font-size:14px;width:100%">
                    <p>This is an auto-generated email</p>
                    <p>Details of orders are as below</p>
                </div>
            </div>


            <div style="clear:both"></div>
        </div>

         <div style="font:bold 18px Arial,Helvetica,sans-serif;margin-top:0px;margin-bottom:0px;">
                <?php $j=1; foreach ($main_data_mail as $orders) {?>
                <?php //echo'<pre>';print_r($orders);?>
                <div style="float:left;font:bold 16px Arial,Helvetica,sans-serif;margin-top:10px;color:#042e6f;margin-bottom:0px;margin-left:30px">
                <?php echo "".$j.". #" . $orders['order_no']; ?></div>
                <div style="float:right;font:bold 14px Arial,Helvetica,sans-serif;margin-top:10px;color:#042e6f;margin-bottom:0px;margin-right:20px">
                <?php echo $orders['order_status']; ?></div>
                <div style = "clear:both"></div><div style="font:14px Arial,Helvetica,sans-serif;margin-left:34px;color:grey;margin-bottom:10px"><?php echo $orders['order_date'];?></div>
               <table class="table table-bordered " border = "0"  style="font-size:14px; margin-bottom:10px; margin-top:20px; width :100% ;text-align:center;" align ="center">
                <thead style="font-size:14px">
                <tr>
                <th>Sr No</th>
                <th>Seller code</th> 
                <th>Seller name</th>
                <th>Amount</th>
                <th>Status</th>
                </tr></thead><tbody style="font-size:12px;font-weight:normal;">
                
                <?php foreach ($orders['suborder_data'] as $order) {?>
                <tr><td col-span=all;><img src="http://www.wholesalebox.in/image/blank_600x1.png" alt="WholesaleBox"/></td></tr>
                 <tr>   
                    <td align = center><?php echo $order['srno']; ?></td>
                    <td align = center><?php echo $order['sellercode']; ?></td>
                    <td align = center><?php echo $order['sellername']; ?></td>
                    <td align = center><?php echo $order['total']; ?></td>
                    <td align = center><?php echo $order['suborder_status']; ?></td>
                </tr>

                <?php }$j++;?>
                 </tbody></table> <div style="padding:2px 1px 2px 1px;border-bottom:#ececec solid 1px"></div>
                  <div style = "clear:both"></div>
                <?php }?>  
                           
            </div>

        <div style="clear:both;border-bottom:#ececec solid 1px"></div>

        <div>

            <div style="margin:20px 0 0 38px;">
             </div>
 
        </div>




    </div>
    <div style="margin:0 auto;text-align:center;margin-top:20px;color:#9c9c9c;font:normal 13px Arial,Helvetica,sans-serif">
        <p> &copy; Copyright WholesaleBox Internet Private Llimited. <br></p>
    </div>


    </div></div>