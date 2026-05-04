<html xmlns="http://www.w3.org/1999/xhtml"><head>

</head>
<body id="body-layout">
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <div style="width:560px;margin:0 auto;text-align:center;padding:22px 0; height: 65px;">     <img src="http://www.wholesalebox.in/image/catalog/rsz_wsb_tmp_logo_286.png" class="CToWUd">
        </div>
    </tbody>
</table>
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
    <tbody>
        <tr>
            <td align="center" valign="top" style="background: #f6f6f6;">
                <div style="margin:18px 0px 0px 38px; margin-bottom: 20px; color:darkred;font:normal 13px Arial,Helvetica,sans-serif;font-size:24px"> <b>Delivered Order</b> </div>
            </td>   
        </tr>
    </tbody>
</table>

<table align="center" border="0" cellpadding="5" cellspacing="0" style="font-size:14px;font-family:Georgia,'Times New Roman',Times,serif;background: #f6f6f6;" width="100%">
    <thead>
            <tr>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; ">Sr No.</th>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; ">Order No</th>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; ">Customer</th>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; " >Amount</th>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; " >Delivered Date</th>
                <th style="height: 35px; width: 10%; background-color:#06396e;  color: white; font-family: Arial,Helvetica,sans-serif; " >Days</th>
            </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach ($results as $data){ 
				$data['date_history'] = date('Y-m-d', strtotime($data['date_history']));
                
                $datetime = new DateTime($data['date_history']);
                $current_date = new DateTime("now");
                $interval = date_diff($datetime,$current_date);
                $date_difference= $interval->format('%a');

        ?>
        <tr style="text-align: center;background-color: <?php echo $bgcolor;?>;">
            <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><?php echo $i;?></td>
            <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><?php echo $data['order_no'];?></td>
            <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><br /><?php echo $data['firstname']; echo " "; echo $data['lastname'];?> <br /><br />
                <?php echo $data['telephone'];?> <br />
                <?php echo $data['payment_company'];?><br /><br />
                </td>
            <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><?php
            $var = number_format($data['total'], 2, '.', ''); 
                echo $var;?></td>
             <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><?php 
            $date_history = date("d-m-Y", strtotime($data['date_history']));
            echo $date_history;?></td>
            <td style="height: 35px; width: 10%; color: <?php echo $font_color; ?>; font-family: Arial,Helvetica,sans-serif; border-bottom: 1px solid #B4B5B0;"><?php echo $date_difference;?></td>
        </tr>
        <?php   
        $i++;
            }
        ?>          
    </tbody>
</table>

</body>
</html>