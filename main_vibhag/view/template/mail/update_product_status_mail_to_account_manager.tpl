<html>
<div style="width:90%;margin:0 auto;border:#dfdfdf solid 1px; background-color:#f8f8f8;">
    <div style="width:90%; margin:0 auto;text-align:center;padding:22px 0; height: 65px; border-bottom: #ececec solid 1px; background-color:#f1f1f1"> <img src="http://www.wholesalebox.in/image/catalog/rsz_wsb_tmp_logo_286.png" class="CToWUd"> </div>
    <div style="float:left;color:#333333;font:normal 14px Arial,Helvetica,sans-serif;width:100%;">
        <div style="margin-left:80px; font:bold 18px Arial,Helvetica,sans-serif;margin-top:10px;color:#042e6f;margin-bottom:10px"> Hi Account, </div>

        <div style="font-size:14px; width:90%; margin-left:80px; margin-top:20px;margin-bottom: 10px;">
         There are some items in your invoice no. <?php echo $invoice_no;?> and dated <?php echo $invoice_date;?> which are changed Inactive status. Please check.
         </div>
    </div>
    
    <table align="center" style="width:90%; text-align: center; margin-top:20px;">
    <tr>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>       
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">SKU</th>
    </tr>
    
      <?php
      $i = 1;
      foreach($datas as $value){
      ?>
      <tr>
      <td> <?php echo $i;?> </td>
      <td> <?php echo $value['sku'];?> </td>
      </tr>
      <?php
      $i++;
      }
      ?>
    </table>
</html>