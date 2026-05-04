<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Book a new Order No:<?php // echo $order_no; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;">
    <p style="margin-top: 0px; margin-bottom: 20px;">

        Dear <b>[SELLERNAME]</b>, <br><br>

        Greetings from Wholesale Box ! <br><br>

        Please book a new Order No: <b><?php // echo $order_no; ?></b>.<br><br>

        Invoice should be billed to: <br>
        <b>Wholesalebox Internet Pvt Ltd, B-1 Crystal Mall, Bani park, Jaipur-302016 <br>
            GSTIN: 08195900085</b><br>
        Invoice should be dated <b><?php // echo $process_date; ?></b>. Please also mention the type of item such as kurti, dupatta etc in the invoice besides the SKU code.<br><br>

        Please do a through QC to avoid unnecessary returns hassles. Kindly keep the stock ready for pick-up as soon as possible, so that out pick-up team can complete all pick-ups lined up. Kindly do not delay the shipment and keep invoice ready. <br><br>

        Order details are below:

    </p>
    <!-- SKU breakup -->

    <table border="1" cellpadding="10" cellspacing="0" >
        <tr>
            <th>S.no</th>
            <th>Product</th>
            <th>SKU</th>
            <th>Comment</th>
            <th>Returned Sets</th>
            <th>Transfer Price / Piece</th>
        </tr>
        <tr>PRODUCTS</tr>
    </table>

    <br><br><b>We are again emphasizing that you need to send products which have been checked thoroughly. Defects, delay in handing over and incorrect product sets will affect your ratings and sales on <a href="www.wholesalebox.in" target="_blank">Wholesale Box</a></b>. <br><br>

    Please regularly maintain inventory by logging into seller panel on daily basis. If an item has gone out-of-stock, then quantity should be updated to zero, on a priority basis. Similarly, items which are showing quantity as zero, should be updated if they are in-stock. Items which have quantity zero on your panel are not shown to customers, and you would be losing on potential orders, if they are not updated regularly. <br><br>

    Looking forward to a long term business. <br><br>

    Thanks,<br>
    Wholesalebox Team<br>
    0141-4049163<br>

</div>
</body>
</html>
