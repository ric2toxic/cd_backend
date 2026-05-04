<?php
class MailTemplate {
  /**
   * [applyGeneralHeader - This email returns header of email template
   * @return [string] [String of html]
   */
  public static function getGeneralHeader() {
    $header_html =
        '<div>
            <table style="font-family:Arial,Helvetica,sans-serif;max-width:680px" width="100%" cellspacing="0" cellpadding="0" border="0">
              <tbody><tr>
            <td width="100%" bgcolor="#ffffff">

              <table class="m_-8542841405683368401max-width" style="max-width:680px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                  <tr>
                    <td style="border-top:6px solid #F03140;padding:0 0px">
                      <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody><tr>
                          <td style="padding:20px 0;border-bottom:5px solid #f0f0f0;line-height:0" align="center"><a href="https://www.wholesalebox.in/" style="display:inline-table;outline:none;line-height:0" target="_blank">
                          <img src="'. STATIC_CONTENT_URL .'catalog/rsz_wsb_tmp_logo_286.png" alt="" style="border:0;width: 286px;" class="CToWUd" width="auto" height="auto"></a></td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>
                </tbody>
              </table>


              <table class="m_-8542841405683368401max-width" style="max-width:680px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                <tbody>
                  <tr style="background-color: #12339C; height: 50px; border-collapse: collapse;">
            <td style="border: 1px solid #949494; border-collapse: collapse;">
            <a style="display: inline-block;text-decoration: none;width: 100%;">
            <div style="width: 120px; margin: auto; text-align: center;">
              <span style="margin-right: 10px; text-align: center; color:#fff; ">Factory Price </span>
              </div>
            </a>
            </td>
            <td style="border: 1px solid #949494; border-collapse: collapse;">
            <a style="display: inline-block;text-decoration: none;width: 100%;">
            <div style="width: 120px; margin: auto; text-align: center;">
              <span style="text-align: center; margin-right: 10px; color:#fff; ">Easy Return </span>
              </div>
            </a>
            </td>
            <td style="border: 1px solid #949494; border-collapse: collapse;">
            <a style="display: inline-block;text-decoration: none;width: 100%;">
            <div style="width: 135px; margin: auto; text-align: center;">
              <span style="text-align: center; float:right; margin-right: 10px; color:#fff; ">Cash on Delivery </span>
              </div>

            </a>
            </td>
            <td style="border: 1px solid #949494; border-collapse: collapse;">
            <a style="display: inline-block;text-decoration: none;width: 100%;">
            <div style="width: 135px; margin: auto; text-align: center;">
              <span style=" text-align: center; float:right; margin-right: 10px; color:#fff; ">Door Delivery </span>
              </div>

            </a>
            </td>
            </tr>
                </tbody>
              </table>

            </td>
            </tr>
              </tbody>
              </table>
            </div>';
    return $header_html;
  }
  /**
   * [applyGeneralFooter - This function returns footer of mail template]
   * @return [string] [string of html]
   */
  public static function getGeneralFooter(){
    $footer_html = '<div>
    <table style="font-family:Arial,Helvetica,sans-serif;max-width:680px" width="100%" cellspacing="0" cellpadding="0" border="0">
          <tbody><tr>
        <td style="background-image:url(../image/wholesalebox.png);background-position:right top;background-repeat:no-repeat;background-position:fixed;background-size:100%;background-size:cover;padding:58px 0px 0 0px" width="100%" bgcolor="#ffffff">

          <table class="m_-8542841405683368401max-width" style="max-width:680px" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>
              <tr>
                <td style="padding:17px 20px 10px 20px" bgcolor="#F03140">
                  <table width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tbody><tr>
                      <td style="color:#ffffff;font:20px Arial">Thank you,</td>
                    </tr>
                    <tr>
                      <td style="padding-top:5px;font:14px Arial;color:#ffffff">Team Wholesalebox</td>
                    </tr>
                    <tr>
                      <td style="padding:20px 0 0px;font:14px Arial;line-height:150%;color:#ffffff">Browse top FAQs, please go to <u><a style="text-decoration:none; color:#fff;" href="https://www.wholesalebox.in/">wholesalebox</u></a></td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody>
          </table>


          <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
            <tbody>
              <tr>
                <td style="padding-bottom:5px" nowrap="" bgcolor="#F03140" align="center">
                  <table cellspacing="0" cellpadding="0" border="0" align="left">
                    <tbody><tr>
                      <td style="padding:5px 3px 5px 20px;height:30px" align="left"><a href="https://www.facebook.com/wholesalebox1/?fref=ts" style="display:inline-block;outline:none" target="_blank"><img src="https://ci4.googleusercontent.com/proxy/f4Xxz1oyFR4OL5soeGKwZYIKsKOMIduMjTqQWTAbulZKh7KkmZQko8s09EPAhZPEOSik_2cU4h7ltXyXcL20d8YheDtmp_51QoZgSH7M4XTKK36tuIugjPxQUzmc_6_4aAGAoDUaRjw=s0-d-e1-ft#https://i.sdlcdn.com/static/img/eventImage/comm-mailers/imagesv2/facebook-icon.png" alt="" style="border:0" class="CToWUd" width="26" height="26"></a></td>
                      <td style="padding:5px 3px;height:30px" align="left"><a href="https://twitter.com/WholesaleBox_in" style="display:inline-block;outline:none" target="_blank"><img src="https://ci5.googleusercontent.com/proxy/sJDC0WvJjhprk0qbDo_xmo3Y8BVLJ6Xo64dGSDyKZskiwCxh2qyl6QdS3AMSIwKZ7PwffjWWyhQhUyUaYj19r4MMTvvCfi5ev2Z6OeWs4QxdFv4z7yowyLpub3XCjFWz35sWjC5D_A=s0-d-e1-ft#https://i.sdlcdn.com/static/img/eventImage/comm-mailers/imagesv2/twitter-icon.png" alt="" style="border:0" class="CToWUd" width="27" height="26"></a></td>
                      <td style="padding:5px 3px;height:30px" align="left"><a href="https://plus.google.com/s/wholesalebox/top" target="_blank"><img src="https://ci4.googleusercontent.com/proxy/TNY4nYe85zCE0ID8niK5IYPEYybRDHMpSnGodgq-6yLG4qQ8eirPi8fcmEPHymmRrwr2c3SrEAEcGSjcgItr3VBcc8lvjanj1Vfgr7K4oU78XcM8-6a78guqbkyaVGzRMJPRGY1ebq_M_hs=s0-d-e1-ft#https://i.sdlcdn.com/static/img/eventImage/comm-mailers/imagesv2/google-plus-icon.png" alt="" style="border:0" class="CToWUd" width="26" height="26"></a></td>
                    </tr>
                  </tbody></table>
                  <table cellspacing="0" cellpadding="0" border="0" align="right">
                    <tbody><tr>
                      <td style="padding:3px 3px 5px 0px;height:30px" align="right"><a href="https://www.wholesalebox.in/index.php?route=information/applanding/index" style="display:inline-block;outline:none;margin-right: 10px;" target="_blank"><img src="'. STATIC_CONTENT_URL .'android_app_image.png" alt="" style="border:0" class="CToWUd" width="87" height="30"></a></td>
                    </tr>
                  </tbody></table>
                </td>
              </tr>
            </tbody>
          </table>

        </td>
      </tr>
          </tbody>
      </table>
    </div>';
    return $footer_html;
  }

  public static function generateOrderLink($order){
    // To Do
  }

  public static function getMailBodyMessage(){
    // To Do
  }
  /**
   * [getPaymentRecieveMail This function returns html string for payment recieved email]
   * @param  [string] $customer_name [name of customer]
   * @param  [int] $order_id      [order id]
   * @return [string]             [html string]
   */
  public static function getPaymentRecieveMail($customer_name,$order_id){
    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 680px;"><table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .= '<tbody>';
    $html .=   '<tr>';
    $html .=     '<td style="font-size: 16px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; text-align: left; padding: 7px; color: #222222;">';
    $html .=     "<b>Hi $customer_name,</b><br><p>We have recieved you payment against order id #$order_id</p>";
    $html .=     '</td>';
    $html .=   '</tr>';
    $html .= '</tbody>';
    $html .= '</table></div>';
    $html .= self::getGeneralFooter();
    return $html;
  }

  /**
   * [getPayentReminderMail This function string conating html for payment reminder mail]
   * @param  [string] $customer_name [customer name]
   * @param  [int] $order_id      [order id]
   * @param  [string] $payment_link  [payment link]
   * @return [string]                [string conating html]
   */
  public static function getPayentReminderMail($customer_name,$order_id,$payment_link){
    $html  = self::getGeneralHeader();
    $html .= '<div style="width:680px;"><table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .= '<tbody>';
    $html .=   '<tr>';
    $html .=     '<td style="font-size: 16px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; text-align: left; padding: 7px; color: #222222;">';
    $html .=     "<b>Hi $customer_name,</b><p>Your payment of order id #$order_id is pending. You can make payment by this link <a href='$payment_link'>$payment_link</a></p>";
    $html .=     '</td>';
    $html .=   '</tr>';
    $html .= '</tbody>';
    $html .= '</table></div>';
    $html .= self::getGeneralFooter();
    return $html;
  }

  // public static function getOrderEdittedMail($customer_name,$order_id){
  //   $html  = self::getGeneralHeader();
  //   $html .= '<div style="width: 680px;"><table>';
  //   $html .= '<tbody>';
  //   $html .=   '<tr>';
  //   $html .=     '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">';
  //   $html .=     "";
  //   $html .=     '</td>';
  //   $html .=   '</tr>';
  //   $html .= '</tbody>';
  //   $html .= '</table></div>';
  //   $html .= self::getGeneralFooter();
  // }
  //
  /**
   * [getOrderShippedMail This function string containing html for order shipped]
   * @param  [string] $customer_name [name of customer]
   * @param  [int] $order_id      [order id]
   * @return [string]                [string containg html]
   */
  public static function getOrderShippedMail($customer_name,$order_id){
    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 680px;"><table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .= '<tbody>';
    $html .=   '<tr>';
    $html .=     '<td style="font-size: 16px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF;  text-align: left; padding: 7px; color: #222222;">';
    $html .=     "<b>Hi $customer_name,</b><p>We has shipped your order #$order_id</p>";
    $html .=     '</td>';
    $html .=   '</tr>';
    $html .= '</tbody>';
    $html .= '</table></div>';
    $html .= self::getGeneralFooter();
    return $html;
  }

  public static function getOutForDeliveryMail($customer_name,$order_id,$products){
    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 680px;"><table>';
    $html .= '<tbody>';
    $html .=   '<tr>';
    $html .=     '<td style="font-size: 16px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; text-align: left; padding: 7px; color: #222222;">';
    $html .=     "<p>We would like to inform you that your order #$order_id is/are out for delivery. You will receive your shipment by the end of the day.</p>";
    $html .=     '</td>';
    $html .=   '</tr>';
    $html .= '</tbody>';
    $html .= '</table></div>';
    $html .= self::getMailBodyProducts($products);
    $html .= self::getGeneralFooter();
    return $html;
  }
 /**
  * [applyCommonMailTemplate -- This method is apply common header and footer to a template]
  * @param  [string] $message [string conating html]
  * @return [string]          [string conating html]
  */
  public static function applyCommonMailTemplate($message){
    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 680px;"><table>';
    $html .= '<tbody>';
    $html .=   '<tr>';
    $html .=     '<td style="font-size: 16px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; text-align: left; padding: 7px; color: #222222;">';
    $html .=      $message;
    $html .=     '</td>';
    $html .=   '</tr>';
    $html .= '</tbody>';
    $html .= '</table></div>';
    $html .= self::getGeneralFooter();
    return $html;
  }
  /**
  * [codFailedTemplate description]
  * @param  [type] $seller_name [description]
  * @param  [type] $order_no    [description]
  * @return [type]              [description]
  */
  public static function codFailedTemplate($seller_name,$order_no){
    $html = "Dear <b>$seller_name</b>, <br><br>";
    $html .= "Greetings from Wholesale Box ! <br><br>";
    $html .= "We regret to inform you that the COD (Cash on Delivery) for the Order No: <b>$order_no</b> has bounced.";
    $html .= " The goods will be returned to you within 10-15 days in original packaging. <br><br>";
    $html .= "In case, you have any query regarding this COD bounce, please feel free to contact us on 0141-4049163. <br><br>";
    $html .= "Regards,<br>";
    $html .= "Wholesalebox Team<br>";
    $html .= "0141-4049163<br>";
    return self::applyCommonMailTemplate($html);

  }

  public function orderMailTemplate($data){
    if(!empty($data)){
      extract($data);
    }
    $html  = '<div style="width: 680px;"><a href="'.$store_url.'" title="'.$store_name.'"><img src="'.$logo.'" alt="'.$store_name.'" style="margin-bottom: 20px; border: none;" /></a>';
    $html .= '<p style="margin-top: 0px; margin-bottom: 20px;">'.$text_greeting.'</p>';
    if ($customer_id) {
    $html .= '<p style="margin-top: 0px; margin-bottom: 20px;">'.$text_link.'</p>';
    $html .= '<p style="margin-top: 0px; margin-bottom: 20px;"><a href="'.$link.'">'.$link.'</a></p>';
    }
    if ($download) {
    $html .= '<p style="margin-top: 0px; margin-bottom: 20px;">'.$text_download.'</p>';
    $html .= '<p style="margin-top: 0px; margin-bottom: 20px;"><a href="'.$download.'">'.$download.'</a></p>';
    }
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2">';
    $td_close = '</td>';
    $html .= '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .=    '<thead>';
    $html .=      '<tr>';
    $html .=        $td_open . $text_order_detail . $td_close;
    $html .=      '</tr>';
    $html .=    '</thead>';
    $html .=    '<tbody>';
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">';
    $html .=      '<tr>';
    $html .=        $td_open;
    $html .=          '<b>'.$text_order_no.'</b>'.$order_no.'<br />';
    $html .=          '<b>'.$text_date_added.'</b>'.$date_added.'<br />';
    $html .=          '<b>'.$text_payment_method.'</b>'.$payment_method.'<br />';
    if ($shipping_method) {
    $html .=          '<b>'.$text_shipping_method.'</b>'.$shipping_method;
    }
    $html .=        $td_close;
    $html .=        $td_open;
    $html .=          '<b>'.$text_email.'</b>'.$email.'<br />';
    $html .=          '<b>'.$text_telephone.'</b>'.$telephone.'<br />';
    $html .=          '<b>'.$text_ip.'</b>'.$ip.'<br />';
    $html .=          '<b>'.$text_order_status.'</b>'.$order_status.'<br />';
    $html .=        $td_open;
    $html .=      '</tr>';
    $html .=      '</tbody>';
    $html .=    '</table>';
    if ($comment) {
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">';
    $html .=    '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .=      '<thead>';
    $html .=        '<tr>';
    $html .=          $td_open . $text_instruction . $td_close;
    $html .=      '</thead>';
    $html .=        '</tr>';
    $html .=      '<tbody>';
    $html .=        '<tr>';
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">';
    $html .=           $td_open . $comment . $td_close;
    $html .=        '</tr>';
    $html .=      '</tbody>';
    $html .=     '</table>';
    }
    $html .=     '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .=      '<thead>';
    $html .=        '<tr>';
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">';
    $html .=          $td_open . $text_payment_address . $td_close;
    if ($shipping_address) {
    $html .=          $td_open . $text_shipping_address . $td_close;
    }
    $html .=        '</tr>';
    $html .=      '</thead>';
    $html .=      '<tbody>';
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">';
    $html .=       '<tr>';
    $html .=         $td_open . $payment_address . $td_close;
    if ($shipping_address) {
    $html .=         $td_open . $shipping_address . $td_close;
    }
    $html .=       '</tr>';
    $html .=      '</tbody>';
    $html .=     '</table>';


    $html .=   '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">';
    $html .=     '<thead>';
    $html .=       '<tr>';
    $html .=          $td_open . $text_image . $td_close;
    $html .=          $td_open . $text_product . $td_close;
    $html .=          $td_open . $text_quantity . $td_close;
    $html .=          $td_open . $text_pieces . $td_close;
    $html .=          $td_open . $text_price_per_piece . $td_close;
    $html .=          $td_open . $text_price . $td_close;
    $html .=          $td_open . $text_subtotal . $td_close;
    $html .=          $td_open . $text_tax . $td_close;
    $html .=        '</tr>';
    $html .=      '</thead>';
    $html .=      '<tbody>';
    $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;" >';
    foreach ($products as $product) {
      $html .= "<tr>";
      $html .=  $td_open;
      if (!empty($product['thumb'])) {
          $html .= '<a style="text-decoration: none;" href="'.$product['href'].'"><img src="'.$product['thumb'].'" alt="'.$product['name'].'" title="'.$product['name'].'" class="img-thumbnail" /></a>';
      }
      $html .=  $td_close;
      $html .=  $td_open;
      $html .=    '<a href="'.$product['href'].'">'.$product['name'].'</a>';
      $html .=    '<br/> <span style="font-size:11px;">Product Code: '. $product['model'].'</span>';
      if ($product['comment']) {
      $html .=    '<br/> <span style="font-size:11px;">'.$product['comment'].'</span>';
      }
      /* foreach ($product['option'] as $option) { ?>
          $html .=  '<br /><small>'.'$option['name'].':'.$option['value'].'</small>';
          } */
      $html .= $td_close;
      $html .= $td_open . $product['quantity'] . $td_close;
      $html .= $td_open . $product['pieces'] . $td_close;
      $html .= $td_open . $product['price_per_piece'] . $td_close;
      $html .= $td_open . $product['price'] . $td_close;
      $html .= $td_open . $product['total'] . $td_close;
      $html .= $td_open . $product['tax'] . $td_close;
      $html .= '</tr>';
      }
      $td_open = '<td colspan="2" style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">';
      $html .= '<tr>';
      $html .=   $td_open . '<b>' . $text_total_qty . '</b>'.$td_close;
      $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 2px;">';
      $html .=   $td_open . $total_sets . " " . $text_new_quantity . $td_close;
      $html .=   $td_open . $total_pieces . " " . $text_new_pieces . $td_close;
      $html .= '</tr>';
      $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">';
      foreach ($vouchers as $voucher) {
      $html .= '<tr>';
      $html .=    $td_open . $voucher['description'] . $td_close;
      $html .=    $td_open . '' . $td_close;
      $html .=    $td_open . 1 . $td_close;
      $html .=    $td_open . $voucher['amount'] . $td_close;
      $html .=    $td_open . $voucher['amount'] . $td_close;
      $html .= '</tr>';
      }
      $html .= '</tbody>';
      $html .= '<tfoot>';
      $td_open = '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;" colspan="7">';
      foreach ($totals as $total) {
        if ($total['code'] == 'tax' and $cform_submit) {
      $html .=   '<tr>';
      $html .=      $td_open .'<b>'. $text_cst .'</b>'. $td_close;
      $html .=      $td_open .'<b>'. $cst_with_cform .'</b>'. $td_close;
      $html .=   '</tr>';
      $html .=   '<tr>';
      $html .=      $td_open .'<b>'. $text_tax_refund .'</b>'. $td_close;
      $html .=      $td_open .'<b>'. $refundable_cform .'</b>'. $td_close;
      $html .=   '</tr>';
        }
        else{
      $html .=   '<tr>';
      $html .=      $td_open .'<b>'. $total['title'] .'</b>'. $td_close;
      $html .=      $td_open .'<b>'. $total['text'] .'</b>'. $td_close;
      $html .=   '</tr>';
        }
      }
      $html .= '</tfoot>';
      $html .= '</table>';
      if ($cform_submit) {
      $html .= '<strong><p style="margin-top: 0px; margin-bottom: 20px;">' . $text_cform_footer . '</p></strong>';
      }
      $html .= '<p style="margin-top: 0px; margin-bottom: 20px;">'.$text_footer.'</p>';
      $html .= '</div>';
      return self::applyCommonMailTemplate($html);
  }

  /**
  * [sellerVacationModeTemplate description]
  * @param  [type] $seller_info [description]
  * @param  [type] $vacation_value[description]
  * @return [type]              [description]
  */
  public static function sellerVacationModeTemplate($seller_info, $vacation_value){

    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 600px; margin:auto;">';
    $html .= "Dear ". $seller_info['company'] .", <br><br>";

        if( $vacation_value == 1 ) {
          $html .= "Your account has been set in Vacation Mode.  All of your products would be Disabled <br>from WholesaleBox until you reset the Vacation Mode to OFF.";
        } else {
          $html .= "Vacation Mode setting has been turned OFF. All your In Stock products will become <br>available immediately for sale on WholesaleBox. Make sure that you have managed the <br>available quantity of all your inventory.<br>";
        }

    $html .= " <br><br>Feel free to contact us on 0141-4049163 if you have any question. <br>
              <br>Regards,<br>Wholesalebox Team<br>0141-4049163<br>";

    $html .= '</div>';
    $html .= self::getGeneralFooter();
    return $html;
  }

  //create cron for email alerts on return not received
  // vikas , 2017

  public static function mailToOperationsForReturnNotReceived( $return_mail ){

    $html  = self::getGeneralHeader();
    $html .= '<div style="width: 600px;"><br><br>';
    $html .= 'Following Approved Returns has been pending for more than <b>5</b> days after approval. <br>Kindly look into the matter and solve the same.<br><br>';
    $html .= 'Return Details:<br>';
             foreach( $return_mail as $return_action_name => $returns) {
    $html .= '<h4><u>'.$return_action_name.'</u></h4>';
    $html .= '
                <table border="1" align="center">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Order NO.</th>
                      <th>Customer Name</th>
                      <th>Return Request Approval Date</th>
                      <th>Pickup Location</th>
                      <th>Tentative Refund Amount</th>
                      <th>Shipping Method</th>
                      <th>Courier Company</th>
                      <th>Tracking Number</th>
                    </tr>
                  </thead>
                  <tbody> ';
             $i=1;
            foreach( $returns as $records) {
              $html .='<tr>
                        <th>' . $i. '</th>
                        <td>' . $records['order_no'] . '</td>
                        <td>' . $records['customer_name'] . '</td>
                        <td>' . $records['return_request_date'] . '</td>
                        <td>' . $records['shipping_city'] . '</td>
                        <td>' . $records['tentative_refund_amount'] . '</td>
                        <td>' . $records['shipping_method'] . '</td>
                        <td>' . $records['courier_company'] . '</td>
                        <td>' . $records['tracking_no'] . '</td>
                      </tr> ';
              $i++;
             }
    $html .='  </tbody>
                </table>
              ';
            }          
    $html .= '</div>';
    $html .= self::getGeneralFooter();
    return $html;
  }
  
  /*
   * Mail template for change pickup status to sellers and accounts
   */
  public static function changePickupstatusMailTemplate($data) {

        if ($data['new_pickup_status'] == 'Issue') {
            $data['new_qty'] = $data['total_qty'];
        }
        if ($data['new_qty'] == 'N/A') {
            $data['new_qty'] = 0;
        }
        $html = self::getGeneralHeader();
        $html .= '<div style="width: 600px;"><br><br>';
        $html .= 'Dear ' . $data['seller_company'] . ', <br><br>';
        $html .= 'Greetings from Wholesale Box ! <br>';
        $html .= 'There has been a mismatch between the invoice generated by you, and goods actually received by us. <br>
Please find the mismatch details below: <br><br>';

        $html .= '<table border="1">
                    <thead>
                            <th width="20%" align="center">Product Code</th>
                            <th width="20%" align="center">Invoice</th>
                            <th width="20%" align="center">Pieces as per Invoice</th>
                            <th width="20%" align="center">Pieces actually received</th>
                            <th width="20%" align="center">Comment</th>
                    </thead>
                    <tbody>
                            <td align="center">' . $data['sku'] . '</td>
                            <td align="center"> <b>' . $data['seller_invoice_no'] . '</b><br> ' . DATE('d-m-Y', strtotime($data['invoice_dated'])) . '</td>
                            <td align="center">' . ( (empty($data['old_qty']) || $data['old_qty'] == 'N/A') ? $data['total_qty'] : $data['old_qty'] ) . '</td>
                            <td align="center">' . $data['new_qty'] . ' </td>
                            <td align="center"> ' . (($data['new_pickup_status'] == 'Issue' ) ? '<span style="color:red">ISSUE: </span>' . $data['issue_box'] : '' ) . '</td>
                    </tbody>
                </table><br>';
        if ($data['no_items_in_invoice_hence_invalid_marked']) {
            $html .= 'Since no items have been received by us, against the purchase invoice no. <b>' . $data['seller_invoice_no'] . '</b>, dated ' . DATE('d-m-Y', strtotime($data['invoice_dated'])) . ', it has been marked INVALID/CANCELLED.<br>';
        } else {
            $html .= 'We have edited the purchase invoice ' . $data['seller_invoice_no'] . ', dated ' . DATE('d-m-Y', strtotime($data['invoice_dated'])) . ', as per the actual received pieces, tabulated above.<br>';
        }
        $html .= 'Kindly check revised invoice by downloading it from your seller panel. Kindly note that persistent incomplete supply at your end, including poor inventory management, may lead to disabling of your seller account. <br><br>

We hope for a better cooperation in future purchase orders. <br><br>

Thanks.<br><br></div>';
        $html .= self::getGeneralFooter();
        return $html;
    }
    
    /*
     * Mail template to send mail to accounts for splitting of suborder 
     */

    public static function splitSuborderMailTemplate($data) {

        $html = self::getGeneralHeader();
        $html .= '<div style="width: 600px;"><br><br><br>';
        $html .= 'Due to further movement of some of the products of Suborder no ' . $data['suborder_id'] . ' into ' . $data['new_suborder_id'] . ' after its invoice being generated already, Invoice no ' . $data['invoice_prefix'] . $data['invoice_no'] . ', dated ' . $data['invoice_date'] . ', ref: ' . $data['buyer_invoice_id'] . ', has been marked cancelled. <br><br>
New invoice(s) will be generated for the suborder nos ' . $data['suborder_id'] . ' and ' . $data['new_suborder_id'] . '. <br><br>';
        $html .= self::getGeneralFooter();
        return $html;
    }
    
    public static function sendMailsForAddItemInSuborderToCustomer($data) {
        $products = $data['products'];
        $order_no = $data['order_no'];
        $html = self::getGeneralHeader();
        $html .= '<br><br><div style="width:600px">';
        $html .= 'Following new items has been added to your Order No.: '.$order_no.' <br><br>';
        $html .= 'You can find updated invoice in your panel. <br><br><br><br>';
        
        $html .= '<table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
                        <thead>
                          <tr>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 5px; color: #222222;">Image</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 5px; color: #222222;">Product</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Sets</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Pcs.</td>';
        /*
                      $html .= '<td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Rate / pc.</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Dis. / pc.</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Tax Rate</td>
                            <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 5px; color: #222222;">Amount (Ex. Tax)</td>';*/

                          $html .= '</tr>
                        </thead>
                        <tbody>';
        foreach ($products as $product) {
            $html .= '<tr>
                         <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;" >';
            if ($product['thumb']) {
                $html .= '<a style="text-decoration: none;" href="' . $product['href'] . '" target="_blank">
                            <img src="' . $product['thumb'] . '" alt="' . $product['name'] . '" title="' . $product['name'] . '" class="img-thumbnail" /></a>';
            }
            $html .= '</td>
                            <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;">
                            <a href="' . $product['href'] . '" target="_blank">' . $product['name'] . '</a>
                            <br/>
                            <span style="font-size:11px;"> Product Code: ' . $product['model'] . '</span>';
//            if (!empty($product['comment'])) {
//                $html .= '<br/> <span style="font-size:11px;">' . $product['comment'] . '</span>';
//            }
            if ($product['store_pickup']) {
                $html .= '<small>Immediate pickup from Store</small>';
            }
            $html .= '</td>';

            $html .= '<td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['new_qty'] . '</td>
                        <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['piece_in_set'] . '</td>';
           /* $html .= '<td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['selling_price'] . '</td>
                        <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['discount_per_piece'] . '</td>
                        <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . $product['tax_rate'] . '</td>
                        <td style="font-size: 12px;border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: right; padding: 7px;">' . ($product['selling_price'] * $product['new_qty'] * $product['piece_in_set']) . '</td>';
*/
            $html .= '</tr>';
        }
        $html .= '</tbody>
                  </table>';
        $html .= '</div>';
        $html .= self::getGeneralFooter();
        return $html;
    }

  public static function getReturnMailTemplate($data){

      $html  = self::getGeneralHeader();
      $html .= '<div style="width:680px">';
      $html .= '<p><b>Dear ' . $data['buyer_name'] . '</b>,</p>';
      $html .= '<p>Greetings from <b>Wholesalebox.in</b></p>';
      if(isset($data["return_check"])){
        //  $html .= '<p><span style="color:red;">';
        //  $html .=     'Please Note: If the shipment details are not provided by you within 48 hours after receiving this mail then your return request will be automatically cancelled. So kindly send the same immediately to avoid any inconvenience.';
        //  $html .= '</span></p>';
      }
      $returns = array();
      if(isset($data['image_width'])){
         $returns['image_width'] = $data['image_width'];
       }else{
         $returns['image_width'] = 0;
      }
     if(isset($data['image_height'])){
       $returns['image_height'] = $data['image_height'];
     }else{
       $returns['image_height'] = 0;
     }
       $returns['returns'] = array();
      if( (!empty($data['updated']) || !empty($data['new'])) && !empty($data['old']) ){
          $html .= "<p>You have updated your return request for your order # {$data['order_no']} </p>";
          $returns['returns'] = array_merge( $data['new'], $data['updated'] );
          foreach ( $data['old'] as $key => $value) {
              if(empty($returns['returns'][$key])){
                   $returns['returns'][$key] = $value;
              }
          }
               
          $return_data = self::getReturnGrid($returns);
          $html .= $return_data['html'];
      }
      else if( empty($data['old']) ){
        if(isset($data['order_no'])){
              $no = $data['order_no'];
          }else{
              $no = $data['master_return_no'];
          }
          $html .= '<p>' . $data["comment_message"] . $no . '</p>';
          $returns['returns'] = array();
          if( !empty($data['new']) ){
            if(isset($data['defect_images'])){
              $returns['defect_images'] = $data['defect_images'];
            }
            $returns['returns'] = $data['new'];

          }
          if( !empty($data['updated']) ){
              $returns['returns'] = array_merge($data['updated'],$returns['returns']);
          }
          if(!empty($returns['returns'])){
              $return_data = self::getReturnGrid($returns);
              $html .= $return_data['html'];
          }
      }
     if(isset($data['shipping_method'])){
        $html .= '<p>You have selected '. str_replace("_",' ',$data['shipping_method']) . ' as the mode of return shipping.<br>';
      
        if($data['shipping_method'] == 'self_courier'){
            $html .= '<p> Kindly send us the courier receipt within 48 hours either as a reply to this mail or Whatsapp on 9587896265, else your return request will stand cancelled.</p>';
        }
        if(isset($return_data['replacement']) && $return_data['replacement'] != "Replacement"){
                $html .= '<p> Tentative return amount of ' . $data['tentative_refund_amount'] . ' will be credited back to you once we receive the goods in proper condition.</p>';
        }
        if($data['shipping_method'] == 'wsb_pickup'){
            $html .= '<p> Kindly keep the goods ready to be handed over to the courier person.</p>';
        }
        $html .= '<p> Replacements, if any, will be sent only after we receive the goods.</p>';
      }  

      if(!empty($data['updated']) && !empty($data['new']) ){
           if(!empty($data['updated'])){
               $html .= '<p> Your previous return request is as follows: </p>';
               foreach ( $data['updated'] as $pid => $return) {
                   unset($data['updated'][$pid]['return_reason_name']);
                   unset($data['updated'][$pid]['return_type']);
                   unset($data['updated'][$pid]['quantity']);
               }
           }
           $returns['returns'] = $data['updated'];
           if(!empty($data['old'])){
              $returns['returns'] =  array_merge($returns['returns'],$data['old']);
           }

           $html .= self::getReturnGrid($returns);

      }


      if(!isset($data['master_return_no'])){
          $html .= '<p>PLEASE NOTE THAT ANY ITEMS NOT IN ABOVE LIST SHOULD NOT BE SENT BACK AS WE ARE RELEASING THE PAYMENT TO MANUFACTURERS FOR REST OF THE ITEMS BY TOMORROW. </p>';
      }
      $html .= '<p>In case you feel there is a discrepancy in noting down of return/replacement items, please call 9587896265 and get the request corrected in at most 24 hours.</p>';
      $html .= '<p>Hoping to get a repeat order from you soon :)</p>';
      $html .= '<p>Warm regards</p>';
      if(isset($fotter_comment)){
           $html .= '<p>' . $fotter_comment . '</p>';
      }
      $html .= '</div>';
      $html .= self::getGeneralFooter();
      return $html;
  }

  public static function getReturnGrid($product){
      $html  = '<table cellspacing="0" border="1" width="100%" align="center">';
      $html .=   '<thead>';
      $html .=       '<tr>';
      $html .=           '<th>S.No</th>';
      $html .=           '<th>Image</th>';
      $html .=           '<th>Model</th>';
      $html .=           '<th>Reason</th>';
      $html .=           '<th width="100px">Return Type</th>';
      $html .=           '<th>Quantity</th>';
      $html .=           '<th>Comment</th>';
      $html .=       '<tr>';
      $html .=   '</thead>';
      $html .=   '<tbody >';
      $i = 1;
      foreach( $product['returns'] as $pid => $return ) {
        
          if($return['return_type'] == 'Replacement'){
              $data["replacement"] = 'Replacement';
          }
          $images_model[$pid]['model'] = $return['model'];
          $html .=       '<tr>';
          $html .=           '<td align="center">' . $i . '</td>';
          $html .=           '<td align="center">';
          $html .=               '<img src="'.$return["image"] .'" width = "'. $product['image_width'].  '" height = "'. $product['image_height'] . '" />';
          $html .=           '</td>';
          $html .=           '<td align="center">' . $return["model"] . '</td>';
          $html .=           '<td align="center">' . (!empty($return["return_reason_name"]) ? $return["return_reason_name"] : $return["last_return_reason"]) . '</td>';
          $html .=           '<td align="center">' . (!empty($return["return_type"]) ? $return["return_type"] : $return["last_return_type"]) . '</td>';
          $html .=           '<td align="center">' . (!empty($return["quantity"]) ? $return["quantity"] : $return['last_return_quantity']) . '</td>';
           $html .=           '<td align="center">' . $return["comment"] . '</td>';
          $html .=       '</tr>';
          $i++;
      }
     
      $html .=   '</tbody>';
      $html .= '</table>';
      if(isset($product['defect_images']) && !empty($product['defect_images'])){
        $html .= '<h3>Defected Images</h3>'; 
        $html .= '<table cellspacing="0" border="1" width="100%" align="center">';
        $html .=   '<thead>';
        $html .=       '<tr>';
        $html .=           '<th>Model</th>';
        $html .=           '<th>images</th>';
        $html .=       '</tr>';
        $html .=   '</thead>';
        $html .=   '<tbody>';
        $i= 0;
        foreach($product['defect_images'] as $key2 => $values2){
         foreach($values2 as $key => $values){
             $html .= '<tr>';
             $html .=           '<td align="center">'.$images_model[$key2]['model'].'</td>';
             $html .=    '<td align="center">';
             $html .=        '<a href="'.$values.'" target="_blank"><img src="'.$values.'" width = "'. $product['image_width'].  '" height = "'. $product['image_height'] . '" /></a>';
             $html .=     '</td>';
             $html .= '</tr>';
         $i++;
       }
       }
        $html .=  '</tbody>';
        $html .= '</table>';
      }  

      $data['html'] = $html;
      return $data;
  }

  /**
  * [send mail to customer on exclusive]
  */
  public static function sendMailToCustomerOnExclusive(){
    $text  = "Dear Customer,\n\n";
    $text .= "Greetings from WholesaleBox!\n\n";
    $text .= "Congratulations! As per your purchase history with WholesaleBox, we have enabled access to EXCLUSIVE SECTION in our Android App, for next 7 days. \n";
    $text .= "With this, you can view and purchase the latest Exclusive Designs, which are available to only a few shopkeepers.\n\n";
    $text .= "Also, if you place any order within 7 days; this access gets renewed for another 30 days.\n\n";
    $text .= "We hope you are enjoying our services; the convenience of online wholesale market, quality, and lowest factory prices.\n\n";
    $text .= "For more information, you can always Call or WhatsApp at %s \n";
    return $text;
  }


  public static function getOutOfStockMail($data){
      $html  = self::getGeneralHeader();
      $html .= '<div style="width: 680px;">';
      $html .= '<p style="margin-top: 0px; margin-bottom: 20px;">';
      $html .= '<br><br>Dear <b>'.$data['firstname'].' '.$data['lastname'].'</b>, <br><br>';
      $html .= 'Greetings from Wholesale Box ! <br><br>';
      $html .= 'Following item(s) in your order #'.$data['order_no'].' are out of stock.<br><br>';
      $html .= '</p>';
      $html .= '<table cellspacing="0" cellpadding="5px" border="1" style="width: 100%;margin-bottom: 20px;">';
      $html .= '<thead>
                    <tr>
                      <td>Sr. No.</td>
                      <td>SKU</td>
                      <td>Sets</td>
                      <td>Piece In Sets</td>
                      <td>Pcs</td>
                      <td>Rate / Pc</td>
                      <td>Dis. / Pc</td>
                      <td>Tax Rate</td>
                      <td>Amount (Inc. Tax)</td>
                    </tr>
                <thead>
                <tbody>';

        $i = 1;
        $total_pieces = 0;
        $total = 0;
        foreach( $data['order_products'] as $product ){
        $html .= '<tr>';
        $html .=    '<td>'.$i.'</td>';

        $html .=    '<td>';
        $html .=        '<b>'.$product['model'].'</b><br>';
        $html .=        '<i style="font-size:10px">'.$product['name'].'</i>';
        $html .=    '</td>';
        $html .=    '<td>';
        $html .=        $product['quantity'];
        $html .=    '</td>';
        $html .=    '<td>';
        $html .=        $product['piece_in_set'];
        $html .=    '</td>';

        $tp    = (int)$product['quantity'] * (int)$product['piece_in_set'];
        $total_pieces += $tp;
        $price = (float)$product['price_per_piece'] - (float)$product['discount_per_piece'];
        $tax   = $price * (float)$product['output_tax_rates'] / 100 ;
        $html .=    '<td>'.$tp.'</td>';

        $html .=    '<td>';
        $html .=        $product['price_per_piece'];
        $html .=    '</td>';
        $html .=    '<td>';
        $html .=        $product['discount_per_piece'];
        $html .=    '</td>';
        $html .=    '<td>';
        $html .=        $tax;
        $html .=    '</td>';
        $html .=    '<td>';
        $total += (float)$price + (float)$tax;
        $html .=        (float)$price + (float)$tax;
        $html .=    '</td>';
        $html .= '</tr>';
        $html .= '<tr>
                      <td><b>Total</b></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>'.$total.'</td>
                  </tr>';
        $i++;
      }
      $html .= '</tbody>';
      $html .= '</table></div>';
      $html .= self::getGeneralFooter();
      return $html;
  }

    /**
  * [send mail to sellers to inform them about HSNID requirement as per GST rules]
  */
  public static function sendNotificationForHSNIDToSeller(){
    $text  = self::getGeneralHeader();
  $text .= '<div style="width:680px;">';

    $text .= "Dear Seller,</br></br>";
    $text .= "Greetings from WholesaleBox!</br></br>";
    $text .= "This mail is to notify you that as per the GST rules, HSN Code of your product(s) is required !</br>";
    $text .= "Kindly provide us the same by mentioning the HSN Code in the attached CSV file, productwise</br></br>";
    $text .= "You can also update the same from your Seller Panel, by going to Update Bulk Price page. </br></br>";
    $text .= "In case you are unable to provide us the HSN Code(s), the products may be <b>DISABLED</b> from WSB.</br>";
    $text .= self::getGeneralFooter();
  $text .= '</div>';

    return $text;
  }


  public static function sendMailToArchiveInventory($table_data){
	$text = self::getGeneralHeader();
	$text .= '<div style="width:680px;">';
	$text .= "Dear Customer,<br>\n\n";
    $text .= "Greetings from WholesaleBox!\n\n";
    $text .= "This is to inform you that the products listed below have been marked old and inactive \nand will no longer be available on our website listing. \n";
    $text .= $table_data."\n\n<br>";
    $text .= "In case you want to continue with the sale of these products, you can manage them in \nthe ARCHIVE SECTION and mark them active.\n\n";
    $text .= "With this, you can enable the products again on our website.\n\n";
    $text .= "We hope you are enjoying our services; the convenience of online wholesale market, quality,\nand lowest factory prices.\n\n";
    $text .= "For more information, you can always Call or WhatsApp at 8696491521 \n";
	$text .= self::getGeneralFooter();
	$text .= '</div>';
    return $text;

  }

  public static function listDataForSortOrderMail($param) {

	  $html  = '<table cellspacing="0" border="1" width="100%" align="center">';
      $html .=   '<thead>';
      $html .=       '<tr>';
      $html .=           '<th>S.No</th>';
      $html .=           '<th>Product Code</th>';
      $html .=           '<th>Current Sort Order</th>';
      $html .=           '<th>Date Modified</th>';
      $html .=           '<th>Since Days</th>';
      $html .=       '<tr>';
      $html .=   '</thead>';
      $html .=   '<tbody >';
      $i = 1;
      foreach( $param as $pid => $value ) {

          $html .=       '<tr>';
          $html .=           '<td>' . $i . '</td>';
          $html .=           '<td>' . $value['model'] . '</td>';
          $html .=           '<td>' . $value['sort_order'] . '</td>';
          $html .=           '<td>' . date('d-M-Y', strtotime($value['date_added'])) . '</td>';
          $html .=           '<td>' . $value['since_days'] . '</td>';
          $html .=       '</tr>';
          $i++;
      }
      $html .=   '</tbody>';
      $html .= '</table>';
	  return $html;
  }

  public static function mailContentsForSortOrderMailer($table_data){
	$text = self::getGeneralHeader();
	$text .= '<div style="width:680px;">';
	$text .= "Dear Team,<br>\n\n";
    $text .= "Greetings from WholesaleBox!\n\n";
    $text .= "This is to inform you that the products listed below were having sort order less than 999 \n 
              for more than 2 days. Sort Order has been RESET to 999. \n<br>";
    $text .= $table_data."\n\n<br>";
	$text .= self::getGeneralFooter();
	$text .= '</div>';
    return $text; 

  }

  public static function orderSplitBySellerInformationMailToOperationTeam($seller_datas, $seller_product_data){
    $html_table = '';
    foreach ($seller_datas as $key => $value) {
      if(!empty($seller_product_data[$key])){
        $html_table .= "
          <table border='1' width='100%'>
            <tr>
              <td colspan=4 text-align='left'> <b>" . $value['company'] .', '. $value['nickname'] . ', '. $value['city'] . "</b></td>
            </tr>
            <tr>
              <th>Order No.</th>
              <th>
                <table border='1' width='100%' style='border:0px;'>
                  <tr>
                    <th width='40%'>Product</th>
                    <th width='50%'>Dispatch Status</th>
                    <th width='10%'>Pieces</th>
                  </tr>
                </table>
              </th>
            </tr> ";

            foreach ($seller_product_data[$key] as $order_no_key => $product_datas) {
              $html_table .="
                <tr>
                  <td>". $order_no_key ."</td>
                  <td>
                    <table border='1' width='100%' style='border:0px;'> ";
                      foreach ($product_datas as $pro_key => $product_values) {
                        $html_table .="
                          <tr>
                            <td width='40%'>" .$product_values['model'] . "</td>
                            <td width='50%'>". $product_values['edit_type']. "</td>
                            <td width='10%'>" . ($product_values['quantity'] * $product_values['piece_in_set'])  ."</td>
                          </tr>";
                      }
                      $html_table .="
                    </table>
                  </td>
                </tr>";
            }
        $html_table .= "</table> <br>";
      }
    }
    return $html_table;
  }

  public static function sellerFulfillmentRate($table_caption,$data=array()){
    $html_table = '';

    $html_table .= "
        <table border='1' width='100%'>
          <caption><b>" . $table_caption . "</b></caption>
          <tr>
            <th>Nickname</th>
            <th>Company</th>
            <th>
              <table border='1' width='100%' style='border:0px;'>
                <tr>
                  <th colspan='2'>FullFillment</th>
                </tr>
                <tr>
                  <th width='50%'>Total Quantity</th>
                  <th width='50%'>Total Sales</th>
                </tr>
              </table>
            </th>
            <th>
              <table border='1' width='100%' style='border:0px;'>
                <tr>
                  <th colspan='2'>Not FullFillment</th>
                </tr>
                <tr>
                  <th width='50%'>Total Quantity</th>
                  <th width='50%'>Total Sales</th>
                </tr>
              </table>
            </th>
            <th>FullFillment Rate</th>
          </tr> ";

          foreach ($data as $values) {
            $html_table .="
              <tr>
                <td>". $values['nickname'] ."</td>
                <td>". $values['company'] ."</td>
                <td>
                  <table border='1' width='100%' style='border:0px;'>
                    <tr>
                      <td width='50%'>". $values['approved_total_quantity'] ."</td>
                      <td width='50%'>". $values['approved_total_sale'] ."</td>
                    </tr>
                  </table>
                </td>    
                <td>
                  <table border='1' width='100%' style='border:0px;'>
                    <tr>
                      <td width='50%'>". $values['not_approved_total_quantity'] ."</td>
                      <td width='50%'>". $values['not_approved_total_sale'] ."</td>
                    </tr>
                  </table>
                </td>    
                <td>". $values['fullfillment_rate'] ."</td>
              </tr>";
          }
      $html_table .= "</table> <br>";
    return $html_table;
  }


  public static function fieldValueOnClickToSee($data = array()){

    $html_table = '';

    $html_table .= "
        <table border='1' width='100%'>
          <tr>
            <th>S. No.</th>
            <th>Username</th>
            <th>Name</th>
            <th>Total Telephone Views</th>
            <th>Total Email Views</th>
            <th>Total Views</th>
          </tr> ";

          $i=1;
          foreach ($data as $values) {
            $html_table .="
              <tr>
                <td align='center'>". $i ."</td>
                <td>". $values['username'] ."</td>
                <td>". $values['name'] ."</td>
                <td align='center'>". $values['total_telephone_views'] ."</td>
                <td align='center'>". $values['total_email_views'] ."</td>
                <td align='center'>". $values['total_views'] ."</td>
              </tr>";
            $i++;  
          }
      $html_table .= "</table> <br>";
    return $html_table;
  }

  /**
  * Function Generate HTML for DNs Listing, not having CNs
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, August 2017
  */
  public static function mailDNsNotHavingCNs($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi All,<br><br>";
    
    if(!empty($data['debit_notes'])){ 
      $text .= "Following cases have no Credit Note generated, but Debit Note has already been generated! (".date('d-M-Y').")<br><br><br>";
      $text .= '<table border="1">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Type</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Debit Note Ids</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Debit Note Nos.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Debit Note Date</th>';
      $text .=    '</tr>';
    
      $row = 1;
      foreach($data['debit_notes'] as $debit_note){
        $text .= '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$debit_note['order_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$debit_note['payment_code'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$debit_note['debit_note_id'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$debit_note['debit_note_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$debit_note['debit_note_date'].'</td>';
        $text .= '</tr>';
        $row++;
      }
      $text .= '</table>';
    }else{
      $text .= "<i>No cases pending where Credit note is pending, but Debit Note has already been generated! (".date('d-M-Y').")<br><br><br></i>";
    }
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Function Generate HTML for Order Cancelled to sellers (
      1. Only when an order has been either PROCESSED OR TENTATIVE PROCESSED once , then cancellation email will go to seller
      2. This cancellation email will go only when THIS IS THE FIRST TIME THAT CANCELLATION IS BEING UPDATED IN ORDER HISTORY
    )
  * @param: $data array with order products
  * @return: HTML, String
  * @author: Vikas, 2017
  */
  public static function mailOrderCancelledToSellers($obj,$nickname, $order_no, $product_data){
    $text  = self::getGeneralHeader();
    $text .= "Dear " .$nickname .",<br><br>";
    $text .= "Greetings from Wholesale Box ! <br><br><br>";
    $text .= "There is a cancellation request from the customer for the Order No. ". $order_no ." <br><br><br>";
    
    $text .= '<table align="center" border="1">';
    $text .=    '<tr>';
    $text .=      '<th>Product</th>';
    $text .=      '<th>SKU</th>';
    $text .=      '<th>Comment</th>';
    $text .=      '<th>Sets</th>';
    $text .=      '<th>Total Pieces</th>';
    $text .=      '<th>Transfer Price / Piece</th>';
    $text .=      '<th>Amount (Inc. Tax)</th>';
    $text .=    '</tr>';

    if(!empty($product_data['products'])){ 
      $total = 0;
      foreach($product_data['products'] as $products){
        $text .=    '<tr>';
        $text .=      '<td><img src="'. $products['product_image'] . '" alt="'. $products['sku'] .'" title="'. $products['sku'] .'" class="img-thumbnail"></td>';
        $text .=      '<td>'.$products['sku'] .'</td>';
        $text .=      '<td>'.$products['comment'] .'</td>';
        $text .=      '<td>'.$products['quantity'] .'</td>';
        $text .=      '<td>'.$products['total_pieces'] .'</td>';
        $text .=      '<td>'.$obj->currency->format($products['transfer_price_per_piece']).'</td>';
        $text .=      '<td>'.$obj->currency->format($products['total_amount']).'</td>';
        $text .=    '</tr>';
        $total += $products['total_amount'];
      }
      $text .= '<tr>';
      $text .= '<td colspan="6" align="right">Total Bill Amount (Inc. Tax) </td>';
      $text .= '<td>'.$obj->currency->format($total).'</td>';
      $text .= '</tr>';
    }
    $text .="</table> <br><br>";
    $text .='Regards, <br>';
    $text .='Business Development Team <br>';
    $text .='Helpline: 9649558363';

    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Function to  send Mail to nofity about CN cancellation
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Oct 2017
  */
  public static function mailForCNCancellation($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="max-width:680px">';
    $text .= "Hi All,<br><br>";
    $text .= "Following Credit-Notes are beging cancelled, Due to order cancellation!<br><br><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CN No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CN Amt</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CN Date</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; "></th>';
    $text .=    '</tr>';
    if(!empty($data)){ 
      $row = 1;
      foreach($data as $cn_id => $cn_data){
        $text .= '<tr style="padding:5px;">';
        $text .=    '<td>'.$row.'</td>';
        $text .=    '<td>'.$cn_data['order_no'].'</td>';
        $text .=    '<td>'.$cn_data['cn_no'].'</td>';
        $text .=    '<td>'.$cn_data['cn_amount'].'</td>';
        $text .=    '<td>'.$cn_data['cn_date'].'</td>';
        $text .=    '<td>
                      <table border="1" style="border:none;">
                        <tr style="border:none;background-color:#33CEFF;">
                          <td>PaymentStatus</td>
                          <td>PaymentAmt</td>
                          <td>PayDate</td>
                        </tr>';
        foreach ($cn_data['trxn'] as $trxn) {
          $trxn_details = explode(':', $trxn);
          $text .=     '<tr style="border:none;">
                         <td>'.$trxn_details[0].'</td>
                         <td>'.$trxn_details[1].'</td>
                         <td>'.$trxn_details[2].'</td>
                       </tr>';
        }
        $text .=     '</table>
                    </td>
                  </tr>';
        $row++;
      }
    }
    $text .= '</table>';
    $text .= self::getGeneralFooter();
    $text .= '</div>';
    return $text; 
 }

  /**
  * Function Generate HTML for CNs Listing, not having DNs
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Dec 2017
  */
  public static function mailCNsNotHavingDNs($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi All,<br><br>";
    
    if(!empty($data['credit_notes'])){ 
      $text .= "Following cases have no Debit Note generated, but Credit Note has already been generated! (".date('d-M-Y').")<br><br><br>";
      $text .= '<table border="1">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Type</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Credit Note Ids</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Credit Note Nos.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Credit Note Date</th>';
      $text .=    '</tr>';
    
      $row = 1;
      foreach($data['credit_notes'] as $credit_note){
        $text .= '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_note['order_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_note['payment_code'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$credit_note['credit_note_id'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$credit_note['credit_note_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_note['credit_note_date'].'</td>';
        $text .= '</tr>';
        $row++;
      }
      $text .= '</table>';
    }else{
      $text .= "<i>No cases pending where Debit note is pending, but Credit Note has already been generated! (".date('d-M-Y').")</i><br><br><br>";
    }
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Function Generate HTML for DN no. 0 internally
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailForStoreDNsHTML($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi All,<br><br>";
    $text .= "Following Debit Note is generated for DebitNote No.: 0! (".date('d-M-Y').")<br><br><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Product Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Store Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">SubOrder No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">DebitNote Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">DebitNote Qty</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Pcs</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Post Revision Tentative Pcs</th>';
    $text .=    '</tr>';
    if(!empty($data['products'])){
      foreach ($data['products'] as $product) {
        $text .= '<tr>';
       $text .=    '<td style="padding:5px;">'.$product['model'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['store_sales'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['suborder_id'].'</td>';
        $text .=    '<td style="padding:5px;">'.$data['dn_id'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['dn_qty'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['ttl_qty'].'</td>';
        $text .=    '<td style="padding:5px;">'.(int)($product['ttl_qty']+$product['dn_qty']).'</td>';
        $text .= '</tr>';
      }
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML for Pending bankdetails of customer to release refunds
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Oct 2017
  */
  public static function mailPendingCustBankDetails($agent_data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "Following Customer Refund payment(s) could not be released due to lack of bank details.<br>";
    $text .= "Please update in the panel at the earliest, so that payments can be released in next cycle.<br><br><br>";
    
    foreach ($agent_data as $agent_name => $details) {
      $text .= '<br><h3>'. $agent_name.'</h3>';
      $text .= '<table border="1">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Type</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Executive</th>';
      $text .=    '</tr>';

      $row = 1;
      foreach ($details as $d) {
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['order_no'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['customer']['cust_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['refund_type'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.round($d['refund'], 2).'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['customer']['order_city'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['sales_staff_name'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';
    }
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();

    return $text; 
  }

  /**
  * Public function to Generate HTML for Pending bankdetails of customer to release refunds
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Oct 2017
  */
  public static function mailForPendingBankDetailsIndividually($agent_data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "Following Customer Refund payment(s) could not be released due to lack of bank details.<br>";
    $text .= "Please update in the panel at the earliest, so that payments can be released in next cycle.<br><br><br>";
    
    $temp_data = array();

    $text .= '<table border="1">';
    $text .=  '<tr>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Type</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
    $text .=  '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Executive</th>';
    $text .=   '</tr>';

      $row = 1;
      foreach ($agent_data as $d) {
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['order_no'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['customer']['cust_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['refund_type'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.round($d['refund'], 2).'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['customer']['order_city'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$d['sales_staff_name'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';

    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();

    return $text; 
  }

  /**
  * Public function to Generate HTML for duplicate refunds cross verify
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailDuplicateRefunds($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Refund payment(s) must be re-verify because of multiple payment against same order's Excess Payment / Credit Note.</p><br><b><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Type</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Reference</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data as $data_details) {
      $details = $data_details['details'];
      $text .=    "<tr>";
      $text .=        "<td>".$row."</td>";
      $text .=        "<td>".$details['order_no']."</td>";
      $text .=        "<td>".$details['customer']['cust_name']."</td>";
      $text .=        "<td>".$details['refund_type']."</td>";
      $text .=        "<td>".@$details['payment_breakup']['ref']."</td>";
      $text .=        "<td>".round($details['refund'], 2)."</td>";
      $text .=        "<td>".$details['customer']['order_city']."</td>";
      $text .=    "</tr>";
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /*
  * Mail to disable products where products price is between 1050 and 1200
  */
  public static function mailDisabledWrongPricedGarmentProductPerGST($data, $price_from = 0, $price_to = 0){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Following Garment product(s) have been disabled, as their Transfer prices were not suitable for GST rate transition (cannot be between Rs '".$price_from."' to Rs '".$price_to."') ! <br><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Product ID</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">WSB Product Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Stock Quantity</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Transfer Price</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">HSN Code</th>';
    $text .=    '</tr>';
    if(!empty($data['product_prices'])){ 
      $row = 1;
      foreach($data['product_prices'] as $product_price){
        $text .= '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$product_price['product_id'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_price['model'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_price['quantity'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$product_price['seller_name'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$product_price['transfer_price'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_price['hsncode'].'</td>';
        $text .= '</tr>';
        $row++;
      }
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML email template, for
  * successful refund of payment done, via Online PG.
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailOnlinePGRefundPaymentsToCustomer($data){
    $text = self::getGeneralHeader();

    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px;white-space: pre-wrap;">';
    $text .= "Dear ".$data['cust_name']." <br><br>";
    $text .= "<p>Greetings from Wholesale Box ! </p><br>";

    if($data['refund_type'] == "Credit Note"){
      $text .= "<p>This is regarding your Order No: ".$data['order_no'].". We have initiated refund of ".$data['refund']." against return(s) in Order ".$data['order_no'].". Make sure your bank details are updated in WholesaleBox App.</p><br>";
    }else{
      $text .= "<p>This is regarding your Order No: ".$data['order_no'].". We have initiated refund of ".$data['refund']." against Order ".$data['order_no']." due to excess payment. Make sure your bank details are updated in WholesaleBox App.</p><br>";
    }

    $text .= "<p>If you would like to update bank detail in website then go to My Account -> Edit Bank Details. </p><br><br>
              <p>Thank you for shopping with us! And hope to see you soon!</p><br><br>
              <p>For any assistance, call +91 95878 96265</p>
              <br><br>";
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML refunded payments by Online PG
  * This is a summary email template, to be generally mailed to internal team
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailOnlinePGRefundPayments($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Refund payment(s) are done by using Online Payment Gateway(s).</p><br><br>
              Kindly look into this.
              <br><br><br>";
    
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Type</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Pymt Gateway</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Ref</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data as $details) {
      $text .=    "<tr>";
      $text .=        "<td>".$row."</td>";
      $text .=        "<td>".$details['order_no']."</td>";
      $text .=        "<td>".$details['cust_name']."</td>";
      $text .=        "<td>".$details['refund_type']."</td>";
      $text .=        "<td>".$details['payment_gateway']."</td>";
      $text .=        "<td>".@$details['refund_ref']."</td>";
      $text .=        "<td>".round($details['refund'], 2)."</td>";
      $text .=        "<td>".$details['city']."</td>";
      $text .=    "</tr>";
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML Error alerts refunded payments
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailErrorRefundPayments($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Refund payment(s) having some issue.</p><br><br>
              <p>Kindly look into this with following Error Ref.</p>
              <br><br><br>";
    
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CreditNote Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refrence</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Error Ref</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Requested Refund</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data as $details) {
      $text .= '<tr>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$row.'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['cust_name'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.($details['refund_type']=="Credit Note" ? $details['cn_id']: 'N/A').'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['ref'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.@$details['error_ref'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.round($details['refund'], 2).'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['city'].'</td>';
      $text .= '</tr>';
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML Error alerts refunded payments
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailcheckForRefundUnderProcess($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Refund payment(s) are under process, after script execution completed.</p><br><br><br>";
    
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">TR Id.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Ref Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refrence</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Refund</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">User</th>';
    $text .=    '</tr>';

    $row = 1;
    foreach ($data as $details) {
      $text .= '<tr>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$row.'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['id'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['ref_id'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['refund_ref'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['total_refund'].'</td>';
      $text .=    '<td style="padding:5px;height: 35px;">'.$details['updated_by_user'].'</td>';
      $text .= '</tr>';
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }
  
  /**
  * Public function to Generate HTML tentative refunds initiated pending for approval
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function sendMailTentativeRefundApproval($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Tentative Refund(s) are initiated today and pending for your approval.</p><br><br>
              Please do a sanity check and then approve.
              <br><br><br>";
    
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Type</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Ref</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data as $details) {
      $text .=    '<tr>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['cust_name'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['refund_type'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.@$details['refund_ref'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.round($details['refund'], 2).'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['city'].'</td>';
      $text .=    '</tr>';
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }
  /*
  * send mail to Nitesh to inform them about Order(s) for insurance
  */
  public static function mailHtmlForOrderInsuranceReport(){
    $text  = self::getGeneralHeader();
  $text .= '<div style="width:680px;">';

    $text .= "Hi Nitesh,</br></br>";
    $text .= "Please find an attached report for the Insurance report for Order </br><br>";
    $text .= self::getGeneralFooter();
    $text .= '</div>';

    return $text;
  }

  /**
  * send mail to Returns to inform them about Return(s) for insurance
  */
  public static function mailHtmlForReturnInsuranceReport(){
    $text  = self::getGeneralHeader();
  $text .= '<div style="width:680px;">';

    $text .= "Hi All,</br></br>";
    $text .= "Please find an attached report for the Insurance report for Return </br><br>";
    $text .= self::getGeneralFooter();
    $text .= '</div>';

    return $text;
  }

  /**
  * send mail for rejected tentative refunds
  */
  public static function mailRejectedRefundsHTML($data){
    $text  = self::getGeneralHeader();
    $text .= '<div style="width:680px;">';
    $text .= "Hi All,<br><br>";
    $text .= "Follwing tentative refund is rejected:<br><br><br>";
    $text .= "Rejected Tentative refunds are marked as NOT_APPLICABLE; Excess payments in Order Table, and CN in Trxn_details table.
    <br><br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Ref Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Ref</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Refund Amt</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Rejected By</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Comment</th>';
    $text .=    '</tr>';
    $text .=    '<tr>';
    $text .=        '<td style="padding:5px;">'.$data['order_no'].'<br>'.$data['suborder_id'].'</td>';
    $text .=        '<td style="padding:5px;">'.$data['ref_id'].'</td>';
    $text .=        '<td style="padding:5px;">'.$data['refund_ref'].'</td>';
    $text .=        '<td style="padding:5px;">'.round($data['total_refund'], 2).'</td>';
    $text .=        '<td style="padding:5px;">'.$data['updated_by_user'].'</td>';
    $text .=        '<td style="padding:5px;">'.@$data['rejected_comment'].'</td>';
    $text .=    '</tr>';
    $text .= "</table>";
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text;
  }

  /*
   * Method for send a mail to stores on an order placed from their store
   * @param: $store_key = string of store name i.e. (ST, MU etc...)
   * @param: $order_details = array of order detaild i.e. (order_no and order date) 
   * @param: $product_data = array of product data 
   * @return : NULL
   * vikas, Jan 2018
   */
  public static function sendOrderEmailToStores($store_key, $order_details, $product_data){
    $html ="<div style='width: 680px;'>";
    $html .= "Dear " . $store_key . " Store";
    if($order_details['order_status_id']==9 || $order_details['order_status_id']==16){
      $html .= "<p> Order " . $order_details['order_no'] . " has been processed for pickup and dispatch. Please find the details of the products in the order below. Kindly keep the goods ready for pickup by the Operations team. </p>"; 
    } else if($order_details['order_status_id']==2){
      $html .= "<p> Order " . $order_details['order_no'] . " has been CANCELLED. Please find the details of the products in the order below. Please keep the goods back for display and further sale. </p>"; 
    } else {
      $html .= "<p> A store inventory order " . $order_details['order_no'] . " has been received. Please find the details of the products in the order below. Kindly set aside these goods for the tentative sale. </p>"; 
    }
    $html .= "<table style='border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;'>";
    $html .=  "<thead>";
    $html .=    "<tr>";
    $html .=      "<td style='font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;' colspan='2'>Order Detail</td>";
    $html .=    "</tr>";
    $html .=  "</thead>";
    $html .=  "<tbody>";
    $html .=    "<tr>";
    $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'><b>Order No: </b>" .$order_details['order_no'] . "</td>";
    $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'><b>Date Added: </b>" .date('d/m/Y', strtotime($order_details["date_added"])) . "</td>";
    $html .=    "</tr>";
    $html .=  "</tbody>";
    $html .= "</table>";

    $html .= "<table style='border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;'>";
    $html .=  "<thead>";
    $html .=    "<tr>";
    $html .=      "<td style='font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;'>Image</td>";
    $html .=      "<td style='font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;'>Product</td>";
    $html .=      "<td style='font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;'>Set</td>";
    $html .=      "<td style='font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;'>Pcs.</td>";
    $html .=    "</tr>";
    $html .=  "</thead>";
    $html .=  "<tbody>";
    
    $total_set = 0;
    $total_pics = 0;
    foreach ($product_data as $product) {
      $html .=    "<tr>";
      $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'>"; 
      if(!empty($product['thumb'])){ 
        $html .= "<a style='text-decoration: none'; href='".$product['href']."'>
                    <img src='".$product['thumb']."' 
                         alt='". $product['name']."' 
                         title='". $product['name'] ."'
                    class=img-thumbnail />
                    </a>";
      }
      $html .=      "</td>";
      $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'>
                      <a href='" . $product['href'] ."'> " . $product['name'] ." </a>
                      <br/>
                      <span style=font-size:11px;> Product Code : ". $product['model'] ." </span> "; 
                      if ($product['comment']) { 
                        $html .= "<br/> <span style=font-size:11px;> ". $product['comment'] ."</span> ";
                      } 
      $html .=      "</td>";
      $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'>" .$product['quantity'] . "</td>";
      $html .=      "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'>" .$product['pieces'] . "</td>";
      $html .=    "</tr>";

      $total_set += $product['quantity'];
      $total_pics += $product['pieces'];
    }
    $html .="<tr>";
    $html .=  "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: center; padding: 7px;' colspan=2><b>Total</b></td>";  
    $html .=  "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'><b>" . $total_set ."</b></td>";  
    $html .=  "<td style='font-size: 12px;  border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;'><b>" . $total_pics . "</b></td>";  
    $html .="</tr>";

    $html .=  "</tbody>";
    $html .= "</table>";
    $html .="</div>";

    return $html;
  }

  /**
  * Method for Mail sent to admin for payment cancelled
  * @param : data_mail : array of data i.e.{order_no, payment_gateway etc...}
  * @return NULL
  * @author : vikas, Feb 2018
  */
  public static function mailForPaymentCancelledToAdmin($data_mail = array()){
    $text = '<div style="width:680px;">';
    $text .= 'Following payment has been ' . $data_mail['mail_subject'] . '.</br></br>';
    $text .= '<table border="1">';
    $text .= '  <thead>';
    $text .= '    <tr>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Order No</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Payment Type</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Txn Status</th>';
    if(!empty($data_mail['payment_gateway'])) {
      $text .= '    <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Payment Gateway</th>';
    } else if(!empty($data_mail['merchant_txn_id'])) {
      $text .= '    <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Merchant Txn ID</th>';
    } else {
      $text .= ' ';
    }    
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Payment Mode</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Amount</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Txn Date Time</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Reference</th>';
    $text .= '      <th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Comment</th>';
    $text .= '    </tr>';
    $text .= '  </thead>';
    $text .= '  <tbody>';
    $text .= '    <tr>';
    $text .= '      <td>'.$data_mail['order_no'].'</td>';
    $text .= '      <td>'.$data_mail['payment_gateway'].'</td>';
    $text .= '      <td>'.$data_mail['txn_status'].'</td>';
    if(!empty($data_mail['payment_gateway'])) {
      $text .= '      <td>'.$data_mail['payment_gateway'].'</td>';
    } else if(!empty($data_mail['merchant_txn_id'])) {
      $text .= '      <td>'.$data_mail['merchant_txn_id'].'</td>';
    } else {
      $text .= ' ';
    }
    
    $text .= '      <td>'.$data_mail['payment_mode'].'</td>';
    $text .= '      <td>'.$data_mail['amount'].'</td>';
    $text .= '      <td>'.$data_mail['txn_date_time'].'</td>';
    $text .= '      <td>'.$data_mail['reference'].'</td>';
    $text .= '      <td>'.$data_mail['action_comment'].'</td>';
    $text .= '    </tr>';
    $text .= '  </tbody>';
    $text .= '</table>';
    $text .= '</div>';
    return $text;
  }

  /**
  * @param : mail data
  * @return :  mail format
  * @author : Anurag Jain
  */
  public static function mailForSellerPromotion($data) {
    $list_data = self::listFormatForSellerPromotion($data['category_data']);
    $text = self::getGeneralHeader();
    $text .= '<div style="width:680px;">';
    $text .= "Dear Team,<br>\n\n";
    $text .= "Greetings from WholesaleBox!\n\n";
    $text .= "This is to inform you that following categories of <b>".$data['seller_name']."</b> have been promoted on <b>".date('d-M-Y', strtotime($data['promotion_date']))."</b> for 48 hours by <b>".$data['promoted_by'].".</b> \n<br>";
    $text .= "Seller Name: ".$data['seller_name']." <br>Seller Code: ".$data['seller_code']."<br>\n\n";
    $text .= $list_data."<br><br>";
    $text .= "Note: Above Categories will be reset after 48 hours.";
    $text .= self::getGeneralFooter();
    $text .= '</div>';
    return $text;
  }
  
  /**
  * 
  * @param : mail list dtaa
  * @return : mail list format
  * @author : Anurag Jain
  */
  public static function listFormatForSellerPromotion($param) {
    $html  = '<table cellspacing="0" border="1" width="100%" align="center">';
    $html .=   '<thead>';
    $html .=       '<tr>';
    $html .=           '<th>S.No</th>';
    $html .=           '<th>Category Name</th>';
    $html .=           '<th>No. of Promoted Products</th>';
    $html .=       '<tr>';
    $html .=   '</thead>';
    $html .=   '<tbody>';
    $i = 1;
    foreach( $param as $pid => $value ) {
      $html .=       '<tr align="center">';
      $html .=           '<td>' . $i . '</td>';
      $html .=           '<td>' . $value['category_name'] . '</td>';
      $html .=           '<td>' . $value['count'] . '</td>';
      $html .=       '</tr>';
      $i++;
    }
    $html .=   '</tbody>';
    $html .= '</table>';
    return $html;
  }
  
  /**
  * 
  * @param : mail data
  * @return :  mail format
  * @author : Anurag Jain
  */
  public static function mailForResetSellerPromotion($data) {
    $list_data = self::listFormatForResetSellerPromotion($data);
    $text = self::getGeneralHeader();
    $text .= '<div style="width:680px;">';
    $text .= "Dear Team,<br><br>\n\n";
    $text .= "Greetings from WholesaleBox!\n\n";
    $text .= "This is to inform you that promotion for following categories of sellers have been reset on <b>".date('d-M-Y').".</b>\n<br><br>";
    $text .= $list_data."<br><br>";
    $text .= self::getGeneralFooter();
    $text .= '</div>';
    return $text;
  }
  
  /**
  * 
  * @param : mail list dtaa
  * @return : mail list format
  * @author : Anurag Jain
  */
  public static function listFormatForResetSellerPromotion($param) {
    $html  = '<table cellspacing="0" border="1" width="100%" align="center">';
    $html .=   '<thead>';
    $html .=       '<tr>';
    $html .=           '<th>S.No</th>';
    $html .=           '<th>Seller Name</th>';
    $html .=           '<th>Seller Code</th>';
    $html .=           '<th>Category Name</th>';
    $html .=           '<th>No. of Reset Products</th>';
    $html .=       '<tr>';
    $html .=   '</thead>';
    $html .=   '<tbody>';
    $i = 1;
    foreach( $param as $pid => $seller_data ) {
      $check = 1;
      foreach ($seller_data['categories'] as $_key => $category_data) {
        $html .=       '<tr align="center">';
        if($check) { 
          $html .=           '<td  rowspan="'.count($seller_data['categories']).'">' . $i . '</td>';
          $html .=           '<td  rowspan="'.count($seller_data['categories']).'">' . $seller_data['seller_name'] . '</td>';
          $html .=           '<td  rowspan="'.count($seller_data['categories']).'">' . $seller_data['seller_code'] . '</td>';
          $check = 0;
          $i++;
        }
        $html .=           '<td>' . $category_data['category_name'] . '</td>';
        $html .=           '<td>' . $category_data['count'] . '</td>';
        $html .=       '</tr>';
      }
    }
    $html .=   '</tbody>';
    $html .= '</table>';
    return $html;
  }
  
  /* Mail template for creditOrderLyingInPending
  * @author Ashish 16 Feb 2018
  */
  public static function mailcreditOrderLyingInPending($data, $date){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Following Credit order(s) are lying in Pending status, from '".$date."' ! <br><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Order No</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Company</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment City</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Method</th>';
    $text .=    '</tr>';
    if(!empty($data['credit_order_in_pending'])){ 
      $row = 1;
      foreach($data['credit_order_in_pending'] as $credit_order_in_pending){
        $text .= '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_order_in_pending['order_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_order_in_pending['customer_name'].'</td>';
        $text .=    '<td style="padding:5px;">'.$credit_order_in_pending['company'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$credit_order_in_pending['city'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$credit_order_in_pending['total'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$credit_order_in_pending['payment_method'].'</td>';
        $text .= '</tr>';
        $row++;
      }
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /* Mail template for sku wise Return 
  * @author Nishu 28 Feb 2018
  */
  public static function mailReturnReportSkuWise($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Returns report is SKU Wise with Sale Qty and Return Qty, <br />
              SKUs are disabled other then Store inventory, Check then manually if any doubt <br>
              Please Find attachment!! <br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU CODE</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Product ID</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Number Sold</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Number Returned</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return %</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Order</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Return Order</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return Order %</th>';
    $text .=    '</tr>';
    foreach ($data as $value) {
      $text .= '<tr>';
      foreach ($value as $key => $col) {
        $text .=    '<td style="padding:5px;">'.$col.'</td>';
      }
      $text .= '</tr>';
    }
    $text .= '</table>';
    $text .= '<br> <b>Remaining Content in CSV file, Find the attachment.</b>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /* Mail template for sku wise Replacement 
  * @author Nishu 28 Feb 2018
  */
  public static function mailReplacementSkuWise($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Replacement report is SKU Wise with Sale Qty and Return Qty, <br />
              SKUs are disabled other then Store inventory, Check then manually if any doubt<br>
              Please Find attachment!! <br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU CODE</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Product ID</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Number Sold</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Number Replaced</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Replacement %</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Order</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Replacement Order</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Replacement Order %</th>';
    $text .=    '</tr>';
    foreach ($data as $value) {
      $text .= '<tr>';
      foreach ($value as $key => $col) {
        $text .=    '<td style="padding:5px;">'.$col.'</td>';
      }
      $text .= '</tr>';
    }
    $text .= '</table>';
    $text .= '<br> <b>Remaining Content in CSV file, Find the attachment.</b>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /* Mail template for Seller wise Return 
  * @author Nishu 28 Feb 2018
  */
  public static function mailReturnSellerWise($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Following Returns report is Seller Wise with Sale Qty and Return Qty, Please Find attachment!! <br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Rating</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU Sold</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU Returned</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">%SKU Return</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Sales</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Return</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">%Sales Return</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Orders</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Orders Returned</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return Order %</th>';
    $text .=    '</tr>';
    foreach ($data as $value) {
      $text .= '<tr>';
      foreach ($value as $key => $col) {
        $text .=    '<td style="padding:5px;">'.$col.'</td>';
      }
      $text .= '</tr>';
    }
    $text .= '</table>';
    $text .= '<br> <b>Remaining Content in CSV file, Find the attachment.</b>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /* Mail template for Seller wise Replacement 
  * @author Nishu 28 Feb 2018
  */
  public static function mailReplacementSellerWise($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";
    
    $text .= "Following Replacement report is Seller Wise with Sale Qty and Return Qty, Please Find attachment!! <br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Rating</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU Sold</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU Replaced</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">%SKU Replacement</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Sales</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Replacement</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">%Sales Replacement</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Orders</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Orders Replaced</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Replacement Order %</th>';
    $text .=    '</tr>';
    foreach ($data as $value) {
      $text .= '<tr>';
      foreach ($value as $key => $col) {
        $text .=    '<td style="padding:5px;">'.$col.'</td>';
      }
      $text .= '</tr>';
    }
    $text .= '</table>';
    $text .= '<br> <b>Remaining Content in CSV file, Find the attachment.</b>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /* Mail template to Seller : for informing disableed SKU because of high returns
   * @author Nishu 5 March 2018
  */
  public static function mailToSellerDisableSkuForReturn($data){
    if(empty($data['products'])){
      return '';
    }
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Dear ".$data['name']."(".$data['nickname']."),<br><br>";
    $text .= "Greetings from Wholesalebox!<br><br>";
    $text .= "The sku list below has been disabled due to high returns/replacements. Please don't try to list this sku again under different code. The account may be suspended if it comes to our notice that sku has been listed under different name.<br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Product</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Model</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SKU</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">HSN Code</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data['products'] as $key => $product) {
      $text .=    '<tr>';
      $text .=    '<td style="padding:5px;">'.$row.'</td>';
      $text .=    '<td style="padding:5px;"><img src="'.$product["image"] .'" width = "'. $product['image_width'].  '" height = "'. $product['image_height'] . '" /><br>
                    '.$product['model'] .'
                    </td>';
      $text .=    '<td style="padding:5px;">'.$product['model'].'</td>';
      $text .=    '<td style="padding:5px;">'.$product['sku'].'</td>';
      $text .=    '<td style="padding:5px;">'.$product['hsn_code'].'</td>';
      $text .=    '</tr>';
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }


  /** 
  * Mail template of upload bulk product csv for sellers wholesalebox
  * @param : $data = array of error rows data.
  * @author vikas, March 2018
  */
  public static function applyBulkProductCSVUploadMail($data = array()){
    $html = self::getGeneralHeader();
    $html .= '<div style="width:680px;">';
    $html .= "<p>" . $data['message'] . "</p>";
    if(!empty($data['products'])){
      $html .= "<p>Following products could not be updated by Bulk CSV Upload feature in admin panel, due the various reasons, as tabulated below:</p>";
      $html .=    '<table border="1" width="100%" >';
      $html .=    '<thead>';
      $html .=      '<tr>';
      $html .=        '<td>S. No.</td>';
      $html .=        '<td>Product ID</td>';
      $html .=        '<td>WSB Product Code</td>';
      $html .=        '<td>Seller SKU</td>';
      $html .=        '<td>';
      $html .=          '<table border="1" width="100%" style="border:none;">';
      $html .=            '<tbody>';
      $html .=              '<tr>';
      $html .=                '<td width="30%">Column Name</td>';
      $html .=                '<td width="70%">Error Message</td>';      
      $html .=              '</tr>';      
      $html .=            '</tbody>';
      $html .=          '</table>';
      $html .=        '</td>';
      $html .=      '</tr>';
      $html .=    '</thead>';
      $html .=    '<tbody>';
      $sno = 1;
      foreach ($data['products'] as $key => $error_data) {
        $default_content = explode('~', $key);
        $html .=      '<tr>';
        $html .=        '<td>' . $sno . '</td>';
        $html .=        '<td>' . $default_content[0] . '</td>';
        $html .=        '<td>' . $default_content[1] . '</td>';
        $html .=        '<td>' . $default_content[2] . '</td>';
        $html .=        '<td>';
        foreach ($error_data as $field_key => $error_msg) {
          $html .=        '<table border="1" width="100%" style="border:none;">';
          $html .=          '<tbody>';
          $html .=            '<tr>';
          $html .=              '<td width="30%">' . $field_key . '</td>';
          $html .=              '<td width="70%">' . $error_msg . '</td>';      
          $html .=            '</tr>';      
          $html .=          '</tbody>';
          $html .=        '</table>';
        }
        $html .=        '</td>';
        
        $html .=      '</tr>';
        $sno++;
      }    
      $html .=    '</tbody>';
      $html .=  '</table>';
    }
    $html .= '</div>';
    $html .= self::getGeneralFooter();
    return $html;
  }

  /* 
   * @info: Mail template to WSB report
   * @author Nishu, March 2018
  */
  public static function mailToSendWsbLossReport($data){
    $text = self::getGeneralHeader();
    //Is Empty Check
    if(empty($data)){
      $text .= '<br>No WSB Loss Records available.';
    }else{
      $text .= '<br><div>';
      $text .= "Hi Account team,<br><br>";
      $text .= "Greetings from Wholesalebox!<br><br>";
      $text .= "Please find attached details about the goods marked as Loss to WSB, due to various reasons.<br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Sr. No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Type</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer ID</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Client Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Business Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Seller Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">No of Pcs</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Product Value  - Purchases</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Tax Rate</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">SGST</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">CGST</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">IGST</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Total Tax</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Total Amount</th>';
      $text .=    '</tr>';
      $row = 1;
      foreach ($data as $product) {
        $invoice_meta = unserialize($product['seller_invoice_meta']);

        $seller_tax = (float)$product['seller_input_tax'];
        $transfer_price_per_piece = (float)$product['tp_price'];
        $rate_per_piece = round($transfer_price_per_piece / (1 + $seller_tax / 100), 2);
        $tax_piece = round($transfer_price_per_piece - $rate_per_piece, 2);
        $tax_value = $tax_piece * $product['quantity'];
        $purchase_price = round($transfer_price_per_piece * $product['quantity'], 2);

        $cgst = '-';
        $sgst = '-';
        $igst = '-';
        if($product['seller_cst'] == 0){
          //Check credit note is intra state or inter state 
          if ($invoice_meta['seller_data']['state_code'][0]['gst_state_code'] == $invoice_meta['buyer_data']['state_code'][0]['gst_state_code']) {
              $cgst = $sgst = number_format((float) $tax_value / 2, 2, '.', '');
          } else {
              $igst = number_format((float) $tax_value, 2, '.', '');
          }
        }

        $text .=    '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$product['return_reason'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['order_no'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['customer_id'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['cust_name'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product['payment_company'].'</td>';
        $text .=    '<td style="padding:5px;">'.$invoice_meta['seller_data']['company'].'('.$invoice_meta['seller_data']['nickname'].')</td>';
        $text .=    '<td style="padding:5px;">'.$product['quantity'].'</td>';
        $text .=    '<td style="padding:5px;">'.round($product['quantity']*$rate_per_piece, 2).'</td>';
        $text .=    '<td style="padding:5px;">'.$product['seller_input_tax'].'</td>';
        $text .=    '<td style="padding:5px;">'.$sgst.'</td>';
        $text .=    '<td style="padding:5px;">'.$cgst.'</td>';
        $text .=    '<td style="padding:5px;">'.$igst.'</td>';
        $text .=    '<td style="padding:5px;">'.$tax_value.'</td>';
        $text .=    '<td style="padding:5px;">'.$purchase_price.'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';
      $text .= '</div>';
    }
    $text .= self::getGeneralFooter();
    return $text; 
  }

  public static function getAgentName($alldata){

    $return_data                  = array(); 
    $return_data['data']          = array(); 
    $return_data['agents_data']   = array(); 

    $customer_data = $alldata['data'];

    $url = 'https://www.wholesalebox.biz/cron/getAgentsDataOfCustomers';

    $input = array();
    $input['request_params'] = array();
      
    $input['request_params']['request_call_time'] = date('Y-m-d H:i:s');
    $input['request_params']['request_url']       = $url;
    $input['request_params']['credentials']       = array();
    $input['request_params']['customer_ids']      = array();

    $input['request_params']['credentials']['customer_id']  = $alldata['customer']['customer_id'];
    $input['request_params']['credentials']['access_token'] = $alldata['customer']['ws_access_token'];
    $customer_ids = array();

    foreach ($customer_data as $data) {
      $customer_id    = $data['customer']['customer_id'];
      $customer_ids[] = $customer_id;
    }
    $input['request_params']['customer_ids'] = array_unique($customer_ids);

    $data_string = json_encode($input);

    // Set some options - we are passing in a useragent too here
    // Get cURL resources
    $ch = curl_init($url);    
    // Set some options - we are passing in a useragent too here
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                                                                          
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                         
    'Content-Type: application/json',                                                                         
    'Content-Length: ' . strlen($data_string))                                                                      
    );
    // Send the request & save response to $resp
    $resp = curl_exec($ch);
    // Close request to clear up some resources
    curl_close($ch);
    $agent_data   = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $resp);


    $res =json_decode($agent_data);

    //Testing mail for debuging - uncomment the code below to debug
    //self::sendMailCrmApiReqRes($data_string, $agent_data);
    $agent_result = array();
    $agents_data  = array();
    //Loop over passed all customer_ids data
    foreach ($customer_data as $data) {
      $team_heads  = array();
      $customer_id = $data['customer']['customer_id'];
      
      if(!empty($res) && !empty($res->result)){
        foreach ($res->result as $result) {
          if($result->customer_id ==$customer_id){
            if(strtolower($result->Role) == 'field_sales'){
              $team_heads = (!empty($result->Sale_Support_data) ? $result->Sale_Support_data : $result->TL_data) ;
            }else{
              $team_heads = $result->TL_data;
            }
            break;
          }
        }
      }

      if(!empty($team_heads)){
        foreach ($team_heads as $tl) {
          if(!isset($agent_result[$tl->name])){ $agent_result[$tl->name] = array(); }
          $agent_result[$tl->name][] = $data;
          if(!empty($tl->email)){
            $agents_data[$tl->name] = $tl->email;
          }
        }
      }else{
        $agent_result['No TL Exist'][] = $data;
      }
      

    }


    $return_data['data']          = $agent_result; 
    $return_data['agents_data']   = $agents_data; 

    return $return_data;
  }

  /**
   * Sending mail for initiating refunds with attachment
   * @param void
   * @return void
   * @author Nishu, Nov 2017   
   */
  private function sendMailCrmApiReqRes($req ='', $res=''){
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = 'smtp.sendgrid.net';
      $mail->Port = 465;
      //$mail->SMTPDebug = 2;
      $mail->SMTPSecure = 'ssl';
      $mail->SMTPAuth = true;
      $mail->Username = 'apikey';
      $mail->Password = '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';


      $mail->addAddress(EMAIL_IDS['madhur']['email_id'], EMAIL_IDS['madhur']['name']);
      $mail->addAddress(EMAIL_IDS['nishu']['email_id'], EMAIL_IDS['nishu']['name']);
      $mail->Subject = 'CRM API Req and Response to getAgentName - ' . date('d/M/Y H:i:s', time());

      $body = "Howdy !! \n\n";
      $body .= "Requeste: ". $req. "\n\n\n";
      $body .= "Response: ". $res. "\n\n\n";
      $mail->Body = $body;
      $mail->send(1, false);
      
  } //End of sendMailRefundInitiatedCsv

  /**
  * Function Generate HTML to send alerts for pending selfshipments in returns
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, May 2018
  */
  public static function mailPendingSelfShipments($data, $product_images){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi All,<br><br>";
    
    $text .= "Following return(s) are marked as SELF SHIPMENT from more then 3 days, But still any status is not updated.<br>
      Kindly look into these return(s) manually. <br><br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Image</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Model</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Reason</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Quantity</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return Type</th>';
    $text .=    '</tr>';
    
    $row = 1;
    foreach($data as $d){
      $pid = $d['order_product_id'];
      $image = '';
      if(!empty($product_images['pid_to_imgs'][$pid])){
        $image = $product_images['pid_to_imgs'][$pid];
      }
      $text .= '<tr>';
      $text .=    '<td style="padding:5px;">'.$row.'</td>';
      $text .=    '<td style="padding:5px;">
                    <img src="'.$image.'" alt="" /><br>'.$d['order_product_id'];
      $text .=    '</td>';
      $text .=    '<td style="padding:5px;">'.$d['model'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['order_no'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_reason'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_quantity'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_type'].'</td>';
      $text .= '</tr>';
      $row++;
    }
    $text .= '</table>';
    
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Function Generate HTML to send alerts for pending selfshipments in returns
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, May 2018
  */
  public static function mailPendingGoodsReceived($data, $product_images){
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi All,<br><br>";
    
    $text .= "Following return(s) are marked as GOODS RECEIVED from more then 48 hours, But still any status is not updated.<br>
      Kindly look into these return(s) manually. <br><br><br>";
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Image</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Model</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Reason</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Quantity</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return Type</th>';
    $text .=    '</tr>';
    
    $row = 1;
    foreach($data as $d){
      $pid = $d['order_product_id'];
      $image = '';
      if(!empty($product_images['pid_to_imgs'][$pid])){
        $image = $product_images['pid_to_imgs'][$pid];
      }
      $text .= '<tr>';
      $text .=    '<td style="padding:5px;">'.$row.'</td>';
      $text .=    '<td style="padding:5px;">
                    <img src="'.$image.'" alt="" /><br>'.$d['order_product_id'];
      $text .=    '</td>';
      $text .=    '<td style="padding:5px;">'.$d['model'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['order_no'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_reason'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_quantity'].'</td>';
      $text .=    '<td style="padding:5px;">'.$d['return_type'].'</td>';
      $text .= '</tr>';
      $row++;
    }
    $text .= '</table>';
    
    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Function generate HTML to send alert to Team(s) for Product(s) expected Dispatch Date
  * @param  $data array of expected dispatch date products
  * @return HTML, String
  * @author Ashish, August 2018
  */
  public static function mailProductsOnExpectedDispatchDate($data, $days)
  {
    $text = self::getGeneralHeader();
    $text .= '<br><div>';
    $text .= "Hi,<br><br>";

    $text .= "PFB, the product(s) for Expected Dispatch Date in $days Days ! <br><br>";
    
    $text .= '<table border="1">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr. No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Product ID</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">WSB Product Code</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">WSB Product Sku</th>';    
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">WSB Product Name</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Expected Dispatch Date</th>';
    $text .=    '</tr>';
    if(!empty($data['product_expected_dispatch_date'])){ 
      $row = 1;
      foreach($data['product_expected_dispatch_date'] as $product_dispatch_date){
        $text .= '<tr>';
        $text .=    '<td style="padding:5px;">'.$row.'</td>';
        $text .=    '<td style="padding:5px;">'.$product_dispatch_date['product_id'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_dispatch_date['model'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_dispatch_date['sku'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$product_dispatch_date['product_name'].'</td>';
        $text .=    '<td style="padding:5px;width: 200px;">'.$product_dispatch_date['seller_name'].'</td>';
        $text .=    '<td style="padding:5px;">'.$product_dispatch_date['expected_dispatch_date'].'</td>';
        $text .= '</tr>';
        $row++;
      }
    }
    $text .= '</table>';

    $text .= '</div>';
    $text .= self::getGeneralFooter();
    return $text; 
  }


  /**
  * Public function to Generate HTML Advance Voucher's amount is exceeded from order payment table's entry
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function sendMailAdvanceVoucherAmountMismatch($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following  Advance Voucher's amount(s) are exceeded From Order Payment Table's entry.</p><br><br>
              <br><br><br>";
    
    $text .= '<table border="1" style="max-width:680px;">';
    $text .=    '<tr>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Id</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Advance Voucher</th>';
    $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order Payment</th>';
    $text .=    '</tr>';
    $row = 1;
    foreach ($data as $details) {
      $text .=    '<tr>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_id'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['payment_id'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['advance_val'].'</td>';
      $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_payment'].'</td>';
      $text .=    '</tr>';
      $row++;
    }
    $text .= '</table>';
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
   * Public method to create HTML for WSB Credit approved client not placing order from long time
   * @param: $fresh_client - fresh client who has not placed any order yet
   *         $other_client - Client who has not placed order from long time
   * @return: Nishu, March 2019
  */
  public static function mailToWsbCreditClientNotOrderedFromLongTime($fresh_client, $other_client){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following Customer's WSB credit is approved, but not placed order from last 30 days!</p><br><br><br>";
    //Fresh clients who has not placed any order
    if(!empty($fresh_client)){
      $text .= "<p>Following Customer(s) are Fresh Clients and have not placed a single order yet:</p>";
      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer ID</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Shop Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Owner Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Agent Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">TL Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Agent Assigned Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Credit Activated Date</th>';
      $text .=    '</tr>';
      $row = 1;
      foreach ($fresh_client as $details) {
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['company'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['owner_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['city'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['agent_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['tl_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['agent_assign_date'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['credit_activated_date'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table><br><br><br>';
    }

    //For those clients who has not placed order form long time
    if(!empty($other_client)){
      $text .= "<p>Following Customer(s) have not placed order from long time (More then 30 days):</p>";
      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer ID</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Shop Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Owner Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">City</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Agent Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">TL Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Agent Assigned Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Ordered Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Delivered Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Days From Delivery</th>';
       $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Credit Activated Date</th>';
      $text .=    '</tr>';
      $row = 1;
      foreach ($other_client as $details) {
        $last_order_date = '';
        if(!empty($details['last_order_date'])){
            $last_order_date = date("d-m-Y", strtotime($details['last_order_date']));
        }
        $last_deliver_date = '';
        if(!empty($details['last_deliver_date'])){
            $last_deliver_date = date("d-m-Y", strtotime($details['last_deliver_date']));
        }

        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['company'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['owner_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['city'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['agent_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['tl_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['agent_assign_date'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$last_order_date.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$last_deliver_date.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.MIN($details['days_diff'], $details['ordered_days_diff']).'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['credit_activated_date'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';
    }
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
  * Public function to Generate HTML Advance Voucher's amount is exceeded from order payment table's entry
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function orderWithAllSuborderEitherCancelledOrDeliveredButBalPending($data){
    
    $total_pending_balance = 0;
    
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    $text .= "<p>Following  order(s) having all suborders are either Cancelled or Delivered but balance is pending to receive from customer.</p><br><br>";
    if(!empty($data)){

      foreach ($data as $key => $value) {
        if($value['is_fully_failed_order'] > 0){
          $total_pending_balance += $value['order_bal'];
        }
      }
      $text .= "<h3>Total pending balance : ".$total_pending_balance." </h3>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Shop Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment City</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Payment Method</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Delivered Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order Total Value</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Balance Pending</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Success Payment Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Failed NACH Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Days</th>';
      $text .=    '</tr>';
      $row = 1;
      foreach ($data as $details) {
        if($details['is_fully_failed_order'] > 0){
          $text .=    '<tr>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_name'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['shop_name'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['payment_city'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['payment_code'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.date('d M Y',strtotime($details['order_date'])).'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.date('d M Y',strtotime($details['last_delivered_date'])).'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_total'].'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.ROUND($details['order_bal'], 2).'</td>';
          $last_successfull_payment_date = (!empty($details['last_successfull_payment_date'])) ? date('d M Y',strtotime($details['last_successfull_payment_date'])) : '';
          $text .=        '<td style="padding:5px;height: 35px;">'.$last_successfull_payment_date.'</td>';
          $last_failed_nach_date = (!empty($details['last_failed_nach_date'])) ? date('d M Y',strtotime($details['last_failed_nach_date'])) : '';
          $text .=        '<td style="padding:5px;height: 35px;">'.$last_failed_nach_date.'</td>';
          $text .=        '<td style="padding:5px;height: 35px;">'.$details['days'].'</td>';
          $text .=    '</tr>';
          $row++;
        }
      }
      $text .= '</table>';
    }else{
      $text .= "<p>No Data Found.</p><br>";
    }
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }


  /**
  * Public function to Generate HTML Advance Voucher's amount is exceeded from order payment table's entry
  * @param: $data array
  * @return: HTML, String
  * @author: Nishu, Nov 2017
  */
  public static function mailClientWithHighReturnRate($data){
    $text = self::getGeneralHeader();
    $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
    $text .= "Howdy !! <br><br>";
    if(!empty($data)){
      $text .= "<p>Following  clients(s) having high return rate.</p><br><br><br><br><br>";
      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CustomerId (MasterId)</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Order No</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Orders</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Total Return Order</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Return Rate</th>';
      $text .=    '</tr>';
      $row = 1;
      foreach ($data as $details) {
        $return_rate = ($details['return_order_count']/ $details['order_count']) * 100;
        $return_rate = ROUND($return_rate, 2);
        
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].' ('.$details['master_id'].')'.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_count'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['return_order_count'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$return_rate.'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';
    }else{
      $text .= "<p>No Data Found.</p><br>";
    }
    $text .= '</div>';
    $text .= "<br>";
    $text .= "Ciao !!<br>";
    $text .= "Customer Payments Bot";
    $text .= self::getGeneralFooter();
    return $text; 
  }

  /**
   * @info: Public method to prepare mail content to send mail- internal team for NACH Schedules bank Failure response
   * @param : $data array
   * @author: Nishu, April 2019
  */
  public static function sendMailUnderProcessOrderOnHoldHtml($data = array()){
    $text = '';
    if(!empty($data)){
      $text  = self::getGeneralHeader();
      $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
      $text .= "Howdy !! <br><br>";
      
      $text .= "Following WSB-CREDIT Order(s) are not delivered yet, manually stop these deliveries if possible, due to NACH Payment Bounced response. <br> <br><br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CustomerId</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order No</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Suborder Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Suborder Status</th>';
      $text .=    '</tr>';

      $all_suborder_statuses = array_flip(ORDER_STATUS);

      $row = 1;
      foreach ($data as $details) {
        $suborder_status = $all_suborder_statuses[$details['order_status_id']];

        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['suborder_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$suborder_status.'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';

      $text .= "</div>";
      $text .= self::getGeneralFooter();
    }
    return $text;
  }
  
  // /**
  //  * @info: Public method to prepare mail content to send mail- internal team for NACH Schedules bank Failure response
  //  * @param : $data array
  //  * @author: Nishu, April 2019
  // */
  // public function sendNachBouncedSummaryHtml($data = array()){
  //   $text = '';
  //   if(!empty($data)){
  //     $text  = self::getGeneralHeader();
  //     $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
  //     $text .= "Howdy !! <br><br>";
      
  //     $text .= "<p>Following WSB-CREDIT approved customer's NACH payments bounced so their WSB-CREDIT is blocked, Kindly have a look:</p><br><br><br><br><br>";

  //     $text .= '<table border="1" style="max-width:680px;">';
  //     $text .=    '<tr>';
  //     $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
  //     $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">CustomerId</th>';
  //     $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">UMRN No</th>';
  //     $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Amount</th>';
  //     $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Bounced Date</th>';
  //     $text .=    '</tr>';
      
  //     $row = 1;
  //     foreach ($data as $details) {
        
  //       $text .=    '<tr>';
  //       $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
  //       $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].'</td>';
  //       $text .=        '<td style="padding:5px;height: 35px;">'.$details['umrn_no'].'</td>';
  //       $text .=        '<td style="padding:5px;height: 35px;">'.$details['amount'].'</td>';
  //       $text .=        '<td style="padding:5px;height: 35px;">'.$details['date'].'</td>';
  //       $text .=    '</tr>';
  //       $row++;
  //     }
  //     $text .= '</table>';

  //     $text .= "<br>";
  //     $text .= "Ciao !!<br>";
  //     $text .= "Customer Payments Bot";
  //     $text .= self::getGeneralFooter();
  //   }
  //   return $text;
  // }

  /**
   * @info: Public method to get HTML for sendig mail about active sellers having no order from long time 
   * @param: array
   * @author: Nishu, May 2019
  */
  public function sendMailForActiveSellerWithNoOrderFromLongTime($data){
    $text = '';
    if(!empty($data)){
      $text  = self::getGeneralHeader();
      $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
      $text .= "Howdy !! <br><br>";
      
      $text .= "<p>Following Seller(s) are not having order(s) from long time, Kindly have a look:</p><br><br><br><br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Company</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Nickname</th>';
      $text .=    '</tr>';
      
      $row = 1;
      foreach ($data as $details) {
        
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['seller_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['company'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['nickname'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';

      $text .= "<br>";
      $text .= "Ciao !!<br>";
      $text .= "Seller Bot";
      $text .= self::getGeneralFooter();
    }
    return $text;
  }

  /**
   * @info: Public method to get HTML for sendig mail about WSB_CREDIT Limit changed 
   * @param:  array
   * @return: String
   * @author: Nishu, June 2019
  */
  public static function alertMailForIncreaseWsbCreditLimit($data){
    $text = '';
    if(!empty($data)){
      $text  = self::getGeneralHeader();
      $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
      $text .= "Howdy !! <br><br>";
      
      $text .= "<p>Following customers (with currently Enabled Credit), may need limit revision (last change 1 month back):</p><br><br><br><br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Customer Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Current Limit</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Last Change Date</th>';
      $text .=    '</tr>';
      
      $row = 1;
      foreach ($data as $details) {
        
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_id'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['customer_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['current_limit'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['last_change_date'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';

      $text .= "<br>";
      $text .= "Ciao !!<br>";
      $text .= "Customer Bot";
      $text .= self::getGeneralFooter();
    }
    return $text;
  }

  /**
   * @info: Public method to get HTML for sendig alert mail 
   *        To notify about Seller Payments Amount mismatch  
   * @param:  array
   * @return: String
   * @author: Nishu, 21st June 2019
  */
  public static function sendAlertForSellerPaymentAmountMismatch($data){
    $text = '';
    if(!empty($data)){
      $text  = self::getGeneralHeader();
      $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
      $text .= "Howdy !! <br><br>";
      
      $text .= "<p>Following Seller Payments Amount Linking Skipped because of amount mismatch with payment breakup totals , Kindly have a look:</p><br><br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;" rowspan="2">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " rowspan="2">Refrence</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " colspan="3">Payment Data</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " colspan="4">Breakup</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " rowspan="2">Difference</th>';
      $text .=    '</tr>';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Payment Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " >Date</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Amount</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Table Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " >Table Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " >Order No</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; " >Amount</th>';
      $text .=    '</tr>';
      
      $row = 1;
      
      foreach ($data as $ref_no => $details) {
        $breakup_row_count = count($details['ref_data']['data']);
        $payment_data      = $details['payment_data'] ?? array(); 
        $payment_id        = $payment_data['payment_id'] ?? 0;
        $payment_date      = $payment_data['dated'] ?? 0;
        $payment_amount    = $payment_data['amount'] ?? 0;

        $breakup_key = 0;

        $breakup_data = $details['ref_data']['data'] ?? array(); 
        $table_name   = $breakup_data[$breakup_key]['tablename'] ?? '';
        $table_id     = $breakup_data[$breakup_key]['id'] ?? '';
        $order_no     = $breakup_data[$breakup_key]['order_no'] ?? '';
        $amount       = $breakup_data[$breakup_key]['amount'] ?? '';
        $total_amount = $details['ref_data']['total_amount'] ?? 0;
        $diff_amount  = (float)($payment_amount - $total_amount);

        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$ref_no.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$payment_id.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$payment_date.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$payment_amount.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$table_name.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$table_id.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$order_no.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$amount.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;" rowspan="'.$breakup_row_count.'">'.$diff_amount.'</td>';
        $text .=    '</tr>';

        //Foreach
        foreach ($breakup_data as $key => $value) {
          if($key > $breakup_key){

            $table_name   = $value['tablename'] ?? '';
            $table_id     = $value['id'] ?? '';
            $order_no     = $value['order_no'] ?? '';
            $amount       = $value['amount'] ?? '';

            $text .=    '<tr>';
            $text .=        '<td style="padding:5px;height: 35px;">'.$table_name.'</td>';
            $text .=        '<td style="padding:5px;height: 35px;">'.$table_id.'</td>';
            $text .=        '<td style="padding:5px;height: 35px;">'.$order_no.'</td>';
            $text .=        '<td style="padding:5px;height: 35px;">'.$amount.'</td>';
            $text .=    '</tr>';

          }
        }


        $row++;
      }
      $text .= '</table>';

      $text .= "<br>";
      $text .= "Ciao !!<br>";
      $text .= "Seller Bot";
      $text .= self::getGeneralFooter();
      
    }
    return $text;
  }

    /**
   * @info: Public method to get HTML for sendig mail about pending Replacement
   * @param:  array
   * @return: String
   * @author: Nishu, Sept 2019
  */
  public static function alertMailForPendingReplacements($data){
    $text = '';
    if(!empty($data)){
      $text  = self::getGeneralHeader();
      $text .= '<br><div style="font-family:Arial,Helvetica,sans-serif;max-width:680px">';
      $text .= "Howdy !! <br><br>";
      
      $text .= "<p>Following Replacement(s) are with pending status :</p><br><br><br><br><br>";

      $text .= '<table border="1" style="max-width:680px;">';
      $text .=    '<tr>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif;">Sr.No.</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Order Id</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Name</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Seller Location</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Product Code</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Goods Received Date From Buyer</th>';
      $text .=      '<th style="padding:5px;height: 35px; background-color:#06396e;  color: whitesmoke; font-family: Arial,Helvetica,sans-serif; ">Pcs Count</th>';
      $text .=    '</tr>';
      
      $row = 1;
      foreach ($data as $details) {
        
        $text .=    '<tr>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$row.'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['order_no'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['seller_name'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['seller_location'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['product_code'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['goods_received_date'].'</td>';
        $text .=        '<td style="padding:5px;height: 35px;">'.$details['return_quantity'].'</td>';
        $text .=    '</tr>';
        $row++;
      }
      $text .= '</table>';

      $text .= "<br>";
      $text .= "Ciao !!<br>";
      $text .= "Customer Bot";
      $text .= self::getGeneralFooter();
    }
    return $text;
  }


}
