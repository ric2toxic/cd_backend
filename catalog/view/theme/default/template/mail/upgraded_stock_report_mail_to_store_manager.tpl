<html>
<div style="width:90%;margin:0 auto;border:#dfdfdf solid 1px; background-color:#f8f8f8;">
    <div style="width:90%; margin:0 auto;text-align:center;padding:22px 0; height: 65px; border-bottom: #ececec solid 1px; background-color:#f1f1f1"> <img src="http://www.wholesalebox.in/image/catalog/rsz_wsb_tmp_logo_286.png" class="CToWUd"> </div>
    <div style="float:left;color:#333333;font:normal 14px Arial,Helvetica,sans-serif;width:100%;">
        <div style="margin-left:62px; font:bold 18px Arial,Helvetica,sans-serif;margin-top:10px;color:#042e6f;margin-bottom:10px"> Dear <?php echo $company;?>, <br> <br> Greetings from WholesaleBox!   </div>

        <div style="font-size:14px; width:90%; margin-left:65px; margin-top:20px; margin-bottom:12px;"> There are some items in your inventory which are updated today. Please have a look on those below and check their current stock.</div>
    </div>
    
    <table align="center" style="width:90%; text-align: center; margin-top:20px;">
    <tr>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>
         <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Product Name</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Image</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">SKU</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Product Option</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Piece in Set</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Set(s) Added</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Set(s)</th>
        <th style=" padding: 0 20px; height: 40px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Piece(s)</th>
    </tr>
    
      <?php
      $i = 1;
      foreach($data as $value){
        if ($i%2==0) {
          $color = "#f8f8f8";
        }else{
          $color = "#EFEFEF";
        }
      ?>
      <tr style="background-color: <?php echo $color;?>">
      <td> <?php echo $i;?> </td>
      <td> <?php echo $value['product_title'];?> </td>
      <td> <img src="<?php echo $value['image'];?>" > </td>
      <td> <?php echo $value['sku'];?> </td>
      <td> 
        <?php
        if ($value['option_name']!='') {
          echo $value['option_name']. '('.$value['option_sub_name'].')';
        }
        ?> 
      </td>
      <td> <?php echo $value['piece_in_set'];?> </td>
      <td> <?php echo $value['qty_added'];?> </td>
      <td> <?php echo $value['new_stock'];?> </td>
      <td> <?php echo $value['total_pieces'];?> </td>
      <?php
      $i++;
      }
      ?>      
    </tr>
    </table>
</html>