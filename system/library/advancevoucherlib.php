<?php

/**
 * Advance Voucher is Public static class
 * 
 * @author: Nilesh, 2017
 */
class AdvanceVoucherLib {

    /**
     * Public Static method to get total advance of order from order_payment table.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @return Total advance amount if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTotalAdvanceAmountInOrder($db, $order_id) {
        $sql = "SELECT 
                    sum(oop.amount) as total_order_advance_amount 
                FROM " . DB_PREFIX . "order_payment oop 
                WHERE successfull=1 AND 
                      order_id ='" . (int) $order_id . "' 
                GROUP BY oop.order_id";

        $total_order_advance_value = $db->query($sql);
        if ($total_order_advance_value->num_rows) {
            return $total_order_advance_value->row['total_order_advance_amount'];
        } else {
            return 0;
        }
    }

    /**
     * Public Static method to get total locked advance of order from advance_voucher table.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @return Total locked advance amount if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTotalLockedAdvanceAmountInOrder($db, $order_id) {

        $sql = "SELECT 
                    SUM(oav.value) as locked_advance 
                FROM " . DB_PREFIX . "advance_voucher oav 
                WHERE oav.status=1 AND 
                      oav.locked=1 AND 
                      oav.order_id ='" . (int) $order_id . "'
                GROUP BY oav.order_id ";

        $locked_advance_value = $db->query($sql);
        if ($locked_advance_value->num_rows) {
            return $locked_advance_value->row['locked_advance'];
        } else {
            return 0;
        }
    }

    /**
     * Public Static method to get total custom applied advance of suborder from advance_voucher table.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return Total custom advance amount in suborder if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTotalCustomAppliedAdvanceAmountInSubOrder($db, $order_id, $suborder_id) {

        $sql = "SELECT 
                    SUM(oav.value) as locked_advance 
                FROM " . DB_PREFIX . "advance_voucher oav 
                WHERE oav.status=1 AND
                      oav.locked=0 AND
                      oav.order_id ='" . (int) $order_id . "' AND 
                      oav.suborder_id = '" . $db->escape($suborder_id) . "' 
                GROUP BY oav.suborder_id ";

        $locked_advance_value = $db->query($sql);
        if ($locked_advance_value->num_rows) {
            return $locked_advance_value->row['locked_advance'];
        } else {
            return 0;
        }
    }

    /**
     * Public Static method to get total custom applied or locked advance (value>0 and status=1) of order from advance_voucher table.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @return Total custom advance amount in order if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTotalCustomLockedAppliedAdvanceAmountInOrder($db, $order_id) {

        $sql = "SELECT 
                    SUM(oav.value) as locked_advance 
                FROM " . DB_PREFIX . "advance_voucher oav 
                WHERE oav.status=1 AND
                      oav.value>0 AND
                      oav.order_id ='" . (int) $order_id . "'
                GROUP BY oav.order_id ";

        $locked_advance_value = $db->query($sql);
        if ($locked_advance_value->num_rows) {
            return $locked_advance_value->row['locked_advance'];
        } else {
            return 0;
        }
    }

    /**
     * Public Static method to get total suborder invoice amount with stored-procedure.
     * Global function for suborder invoice amount.
     * @param $db - Database variable.
     * @param $suborder_id (string) - suborder_id mandatory
     * @param $edit_type_flag (string) - edit_type_flag not mandatory - If suborder invoice is generated it will invoice_no or 1 elseif invoice not generated it will 0. Else leave blank it will auto calculate.
     * @return Total suborder invoice amount if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTotalSubOrderInvoiceAmount($db, $suborder_id, $edit_type_flag = '') {

        if (empty($edit_type_flag)) {
            $edit_type = $db->query("SELECT 
                                        os.invoice_no as edit_type_flags 
                                    FROM " . DB_PREFIX . "suborder os 
                                    WHERE os.suborder_id = '" . $db->escape($suborder_id) . "'");

            if ($edit_type->num_rows) {
                $edit_type_flag = $edit_type->row['edit_type_flags'];
            }
        }

        $db->query("CALL calculateTotalInvoiceAmount('" . $db->escape($suborder_id) . "', '" . (int) $edit_type_flag . "', @total_invoice_amount)");

        $suborder_invoice_total = (float) $db->query("SELECT @total_invoice_amount")->row['@total_invoice_amount'];
        if ($suborder_invoice_total > 0) {
            $order_curr_query = $db->query("SELECT 
                                        oo.currency_value,
                                        oo.live_currency_conversion_rate
                                    FROM " . DB_PREFIX . "order oo INNER JOIN
                                         " . DB_PREFIX . "suborder os ON oo.order_id=os.order_id
                                    WHERE os.suborder_id = '" . $db->escape($suborder_id) . "'");
            $order_curr_data = $order_curr_query->row;

            return ROUND($suborder_invoice_total * $order_curr_data['currency_value'] / $order_curr_data['live_currency_conversion_rate'], 2);
        } else {
            return 0;
        }
    }

    /**
     * Public Static method to get all applied advance vouchers detail of suborder.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return Suborder applied advance voucher details if successful; Else false
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getAdvanceVoucher($db, $order_id, $suborder_id) {

        $sql = "SELECT 
                    advance_voucher_id,
                    advance_voucher_prefix,
                    advance_voucher_no,
                    advance_voucher_date,
                    value,
                    payment_id,
                    user_id,
                    locked
                FROM " . DB_PREFIX . "advance_voucher
                WHERE order_id = '" . (int) $order_id . "' AND 
                      suborder_id = '" . $db->escape($suborder_id) . "' AND
                      status = '1'";
        $advance_data = $db->query($sql);
        if ($advance_data->num_rows) {
            return $advance_data->rows;
        } else {
            return false;
        }
    }

    /**
     * Public Static method to get custom splitted revised advance voucher amount of suborder.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @param $total_suborder_invoice_amount (string) - not mandatory - total suborder invoice amount leave blank if not have it will calculate automatically  
     * @param $order_total_advance (string) - not mandatory - order total advance amount - leave blank if not have it will calculate automatically  
     * @param $remaining_advance (string) -  not mandatory - remaining advance is balance of total order advance and total locked advance amount of order - blank if not have it will calculate automatically
     * @return splitted advance amount of suborder if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getSplittedAdvanceAmount($db, $order_id, $suborder_id, $total_suborder_invoice_amount = 0, $order_total_advance = 0, $remaining_advance = 0) {

        $sql = "SELECT GROUP_CONCAT(DISTINCT os.suborder_id ) as suborder_ids
                FROM " . DB_PREFIX . "suborder os INNER JOIN 
                     " . DB_PREFIX . "advance_voucher oav ON 
                         os.order_id=oav.order_id
                WHERE os.suborder_id=oav.suborder_id AND
                      os.order_id='" . (int) $order_id . "' AND
                      os.invoice_no=0 AND
                      os.order_status_id > 0 AND
                      os.order_status_id != 2 AND 
                      oav.value=0 AND 
                      oav.locked = 0 AND 
                      oav.status=1";

        $suborder_ids = $db->query($sql)->row['suborder_ids'];
        $suborder_ids = explode(',', $suborder_ids);


        $total_suborder_non_freezed_invoice_amount = 0;
        foreach ($suborder_ids as $key => $value) {
            $total_single_suborder_invoice_amount = (float) self::getTotalSubOrderInvoiceAmount($db, $value);
            if (($value == $suborder_id) && $total_suborder_invoice_amount == 0) {
                $total_suborder_invoice_amount = $total_single_suborder_invoice_amount;
            }
            $total_suborder_non_freezed_invoice_amount += $total_single_suborder_invoice_amount;
        }
        $splitted_advance = 0;

        if ($order_total_advance == 0) {
            $order_total_advance = (float) self::getTotalAdvanceAmountInOrder($db, $order_id);
        }

        if ($remaining_advance == 0) {
            $total_locked_and_custom_advance_amount = (float) self::getTotalCustomLockedAppliedAdvanceAmountInOrder($db, $order_id);

            $remaining_advance = (float) $order_total_advance - $total_locked_and_custom_advance_amount;
        }

        $splitted_advance = (float) (($total_suborder_invoice_amount / $total_suborder_non_freezed_invoice_amount) * $remaining_advance);

        return round($splitted_advance, 2);
    }

    /**
     * Public Static method to get freezed advance revised amount of suborder.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return if suborder invoice is already generated it will return all locked advance amount of that suborder else return it will calculate automatically if successful; Else false
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function freezeAdvance($db, $order_id, $suborder_id) {

        $updated_advance_amount = 0;

        $advance_voucher_arr = self::getAdvanceVoucher($db, $order_id, $suborder_id);
        if ($advance_voucher_arr[0]['locked']) {
            foreach ($advance_voucher_arr as $key => $value) {
                $updated_advance_amount += $value['value'];
            }
            return $updated_advance_amount;
        }

        //getting order details
        $order_info = OrderInfo::getOrderInfo(
                        $db, $order_id, $suborder_id, array('order' =>
                    array(
                        'select' => array(
                            'payment_code'
                        )
                    ),
                    'suborder' =>
                    array(
                        'select' => array(
                            'invoice_no'
                        )
                    )
                        )
        );

        //getting total suborder invoice amount
        $total_suborder_invoice_amount = (float) self::getTotalSubOrderInvoiceAmount($db, $suborder_id, $order_info['suborder'][$suborder_id]['invoice_no']);

        //getting total order advance amount
        $total_advance_amount = (float) self::getTotalAdvanceAmountInOrder($db, $order_id);

        //getting already applied custom advance amount
        $total_custom_advance_amount = (float) self::getTotalCustomAppliedAdvanceAmountInSubOrder($db, $order_id, $suborder_id);

        //getting total locked advance value of order
        $total_locked_advance_amount = (float) self::getTotalLockedAdvanceAmountInOrder($db, $order_id);

        $remaining_advance_amount = $total_advance_amount - $total_locked_advance_amount;

        /* if ($order_info['order']['payment_code'] != 'cod') {
          $updated_advance_amount = (float) min($remaining_advance_amount, $total_suborder_invoice_amount);
          } else */

        if ($order_info['order']['payment_code'] != 'cod') {
            $updated_advance_amount = (float) min($remaining_advance_amount, $total_suborder_invoice_amount);
        } elseif ($total_custom_advance_amount > 0) {
            $updated_advance_amount = (float) min($total_custom_advance_amount, $total_suborder_invoice_amount);
        } else {
            $updated_advance_amount = (float) min(self::getSplittedAdvanceAmount($db, $order_id, $suborder_id, $total_suborder_invoice_amount, $total_advance_amount), $total_suborder_invoice_amount);
        }
        return $updated_advance_amount;
    }

    /**
     * Public Static method to get order payment amount by payment id.
     * @param $db - Database variable.
     * @param $payment_id (string) - payment_id mandatory
     * @return amount received of that payment_id if successful; Else false
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getOrderPaymentByPaymentId($db, $payment_id) {
        $sql = "Select amount,
                       payment_gateway,
                       payment_mode
                from " . DB_PREFIX . "order_payment 
                where  payment_id ='" . (int) $payment_id . "'";

        $result = $db->query($sql);
        if ($result->num_rows)
            return $result->row;
        else {
            return false;
        }
    }

    /**
     * Public Static method to get advance voucher breakup.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return array of payment_id as key till suborder has as much payment count and value as advance voucher details array
     * 
     * Array
      (
      [payment_id_1] => Array
      (
      [value] => 387.04
      [advance_voucher_id] => 2872
      [advance_voucher_prefix] => WSB-DL-AV-2017-18
      [advance_voucher_no] => 839
      [advance_voucher_date] => 2017-08-18 20:27:09
      [user_id] => 0
      [locked] => 0
      )
      [payment_id_2] => Array
      (
      [value] => 387.04
      [advance_voucher_id] => 2872
      [advance_voucher_prefix] => WSB-DL-AV-2017-18
      [advance_voucher_no] => 839
      [advance_voucher_date] => 2017-08-18 20:27:09
      [user_id] => 0
      [locked] => 0
      )
      )
     * if successful; Else blank array();
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getAdvanceVoucherBreakup($db, $order_id, $suborder_id) {

        $advance_voucher_amount = array();
        $advance_voucher_details = self::getAdvanceVoucher($db, $order_id, $suborder_id);

        if (empty($advance_voucher_details)) {
            return false;
        }
        $suborder_total_advance = (float) self::freezeAdvance($db, $order_id, $suborder_id);

        $order_total_advance = (float) self::getTotalAdvanceAmountInOrder($db, $order_id);

        $voucher_count = count($advance_voucher_details);
        $x = 1;
        $all_advance_voucher_amount = 0;
        foreach ($advance_voucher_details as $key => $value) {
            $payment_id = $value['payment_id'];
            $payment_detail = self::getOrderPaymentByPaymentId($db, $payment_id);

            if ($value['locked']) {
                $advance_voucher_amount[$payment_id]['value'] = round($value['value'], 2);
                $advance_voucher_amount[$payment_id]['order_id'] = $order_id;
                $advance_voucher_amount[$payment_id]['suborder_id'] = $suborder_id;
                $advance_voucher_amount[$payment_id]['advance_voucher_id'] = $value['advance_voucher_id'];
                $advance_voucher_amount[$payment_id]['advance_voucher_prefix'] = $value['advance_voucher_prefix'];
                $advance_voucher_amount[$payment_id]['advance_voucher_no'] = $value['advance_voucher_no'];
                $advance_voucher_amount[$payment_id]['advance_voucher_date'] = $value['advance_voucher_date'];
                $advance_voucher_amount[$payment_id]['user_id'] = $value['user_id'];
                $advance_voucher_amount[$payment_id]['locked'] = $value['locked'];
                $advance_voucher_amount[$payment_id]['payment_gateway'] = $payment_detail['payment_gateway'];
                $advance_voucher_amount[$payment_id]['payment_mode']    = $payment_detail['payment_mode'];
            } else {
                $advance_amount[$payment_id] = (float) $payment_detail['amount'];

                if ((int) $x === (int) $voucher_count) {
                    $advance_voucher_amount[$payment_id]['value'] = round(($suborder_total_advance - $all_advance_voucher_amount), 2);
                } else {
                    $advance_voucher_amount[$payment_id]['value'] = round((($advance_amount[$payment_id] / $order_total_advance) * $suborder_total_advance), 2);
                }
                $all_advance_voucher_amount += $advance_voucher_amount[$payment_id]['value'];
                $advance_voucher_amount[$payment_id]['order_id'] = $order_id;
                $advance_voucher_amount[$payment_id]['suborder_id'] = $suborder_id;
                $advance_voucher_amount[$payment_id]['advance_voucher_id'] = $value['advance_voucher_id'];
                $advance_voucher_amount[$payment_id]['advance_voucher_prefix'] = $value['advance_voucher_prefix'];
                $advance_voucher_amount[$payment_id]['advance_voucher_no'] = $value['advance_voucher_no'];
                $advance_voucher_amount[$payment_id]['advance_voucher_date'] = $value['advance_voucher_date'];
                $advance_voucher_amount[$payment_id]['user_id'] = $value['user_id'];
                $advance_voucher_amount[$payment_id]['locked'] = $value['locked'];
                $advance_voucher_amount[$payment_id]['payment_gateway'] = $payment_detail['payment_gateway'];
                $advance_voucher_amount[$payment_id]['payment_mode'] = $payment_detail['payment_mode'];
                $x++;
            }
        }

        return $advance_voucher_amount;
    }

    /**
     * Public Static method to get total locked advance of order from advance_voucher table.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @return Total locked advance amount if successful; Else 0
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getProductsArrayByOrderSuborderId($db, $order_id, $suborder_id, $edit_type) {
        $sql = "SELECT 
                    ((oop.price_per_piece + oop.discount_per_piece) * 
                    oop.quantity * 
                    oop.piece_in_set) * 
                    (1 + oop.output_tax_rates/100) as total, 
                    oop.order_product_id, 
                    oop.hsn_code, 
                    oop.output_tax_rates  
                FROM " . DB_PREFIX . "order_product oop ";
        $sql .= "WHERE 
                    oop.suborder_id = '" . $db->escape($suborder_id) . "' AND ";

        if ($edit_type) {
            $sql .= " oop.buyer_invoice_id > 0 ";
        } else {
            $sql .= "
                    oop.edit_type in ('YES',
                                        'SELLER_LATER_DISAPTCH', 
                                        'SELLER_APPROVED',
                                        'SELLER_PARTIAL',
                                        'DAMAGE_BY_COURIER_COMPANY') ";
        }

        $result = $db->query($sql);
        if (!$result->num_rows) {
            return false;
        }
        return $result->rows;
    }

    /**
     * Public Static method to get single advance voucher breakup.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return array of advance_voucher_id as key till suborder has as much advance voucher count and value again has key value pair here key is order_product_id as suborder have and value is  advance details product wise array. It has shipping key also if applicable.
     * 
     * Array
      (
      [advance_voucher_id_1] => Array
      (
      [order_product_id_1] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      [advance_tax_rate] => 5.00
      [advance_hsn] => 62044290
      )
      [order_product_id_2] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      [advance_tax_rate] => 5.00
      [advance_hsn] => 62044290
      )

      [shipping] => Array
      (
      [advance_inc_tax] => 14.54
      [advance_exc_tax] => 13.85
      [advance_tax_val] => 0.69
      [advance_tax_rate] => 5.00
      [advance_hsn] =>
      )

      )
      [advance_voucher_id_2] => Array
      (
      [order_product_id_1] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      [advance_tax_rate] => 5.00
      [advance_hsn] => 62044290
      )
      [order_product_id_2] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      [advance_tax_rate] => 5.00
      [advance_hsn] => 62044290
      )
      )

      )
     * if successful; Else blank array();
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getSingleAdvanceVoucherBreakup($db, $order_id, $suborder_id) {

        // get shipping charge
        $sql = "SELECT shipping_charge, 
                       invoice_no 
                FROM " . DB_PREFIX . "suborder 
                WHERE order_id='" . (int) $order_id . "' AND
                      suborder_id = '" . $db->escape($suborder_id) . "'";
        $shipping_query = $db->query($sql);
        $shipping_charge = 0;
        $invoice_no = 0;
        if ($shipping_query->num_rows) {
            $shipping_charge = (float) $shipping_query->row['shipping_charge'];
            $invoice_no = (float) $shipping_query->row['invoice_no'];
        }
        //getting product details for splitting taxwise
        $products = self::getProductsArrayByOrderSuborderId($db, $order_id, $suborder_id, $invoice_no);
        $advance_voucher = self::getAdvanceVoucherBreakup($db, $order_id, $suborder_id);

        $total_invoice_amount = (float) self::getTotalSubOrderInvoiceAmount($db, $suborder_id);
        $sigle_voucher_details = array();
        $single_product_advance = 0;
        $single_product_advance_exc_tax = 0;
        $single_product_tax = 0;
        $product_count = count($products);
        foreach ($advance_voucher as $payment_id => $advance) {
            $total_product_advance = 0;
            $advance_voucher_id = $advance['advance_voucher_id'];
            $advance_value = $advance['value'];
            $max_tax_rate_for_shipping = max(array_column($products, 'output_tax_rates'));
            $max_tax_rate_for_shipping = number_format((float) $max_tax_rate_for_shipping, 2);
            $x = 1;
            foreach ($products as $product) {
                if ($x == $product_count && $shipping_charge == 0) {
                    $single_product_advance = $advance_value - $total_product_advance;
                } else {
                    $single_product_advance = round(( ($product['total'] / $total_invoice_amount) * $advance_value), 2);
                }
                $single_product_advance_exc_tax = round(($single_product_advance / (1 + (float) $product['output_tax_rates'] / 100)), 2);
                $single_product_tax = $single_product_advance - $single_product_advance_exc_tax;
                $total_product_advance += $single_product_advance;
                $sigle_voucher_details[$advance_voucher_id][$product['order_product_id']]['advance_inc_tax'] = $single_product_advance;
                $sigle_voucher_details[$advance_voucher_id][$product['order_product_id']]['advance_exc_tax'] = $single_product_advance_exc_tax;
                $sigle_voucher_details[$advance_voucher_id][$product['order_product_id']]['advance_tax_val'] = $single_product_tax;
                $sigle_voucher_details[$advance_voucher_id][$product['order_product_id']]['advance_tax_rate'] = number_format((float) $product['output_tax_rates'], 2);
                $sigle_voucher_details[$advance_voucher_id][$product['order_product_id']]['advance_hsn'] = $product['hsn_code'];
                $x++;
            }

            if ($shipping_charge > 0) {
                $ship_advance_inc_tax = round(($advance_value - $total_product_advance), 2);
                $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_inc_tax'] = $ship_advance_inc_tax;
                $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_exc_tax'] = round(($ship_advance_inc_tax / (1 + $max_tax_rate_for_shipping / 100)), 2);
                $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_tax_val'] = round(($sigle_voucher_details[$advance_voucher_id]['shipping']['advance_inc_tax'] - $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_exc_tax']), 2);
                $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_tax_rate'] = $max_tax_rate_for_shipping;
                $sigle_voucher_details[$advance_voucher_id]['shipping']['advance_hsn'] = '';
            } else {
                $sigle_voucher_details[$advance_voucher_id]['shipping'] = 0;
            }
        }

        return $sigle_voucher_details;
    }

    /**
     * Public Static method to get tax wise advance voucher breakup.
     * @param $db - Database variable.
     * @param $order_id (string) - order_id mandatory
     * @param $suborder_id (string) - suborder_id mandatory
     * @return array of advance_voucher_id as key till suborder has as much advance voucher count and value again has key value pair here key is output_tax_rate and value is advance details tax wise group array. It has shipping key also if applicable and shipping key is only in maximum of tax.
     * 
     * Array
      (
      [advance_voucher_id_1] => Array
      (
      [5.00] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      )
      [12.00] => Array
      (
      [advance_inc_tax] => 372.5
      [advance_exc_tax] => 354.76
      [advance_tax_val] => 17.74
      [shipping] => Array
      (
      [advance_inc_tax] => 14.54
      [advance_exc_tax] => 13.85
      [advance_tax_val] => 0.69
      )

      )
      )
      )
     * if successful; Else blank array();
     * @warning: Errors are triggered if input conditions are not satisfied
     * @author: Nilesh, 2017
     */
    public static function getTaxWiseAdvanceVoucherBreakup($db, $order_id, $suborder_id) {

        $advance_vouchers = self::getSingleAdvanceVoucherBreakup($db, $order_id, $suborder_id);

        $tax_wise_group_advance = array();
        foreach ($advance_vouchers as $advance_voucher_id => $advance_arr) {
            foreach ($advance_arr as $order_product_id => $advance) {
                if ($order_product_id != 'shipping') {
                    $tax_keys = $advance['advance_tax_rate'];
                    if (!empty($tax_wise_group_advance[$advance_voucher_id][$tax_keys])) {
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_inc_tax'] += $advance['advance_inc_tax'];
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_exc_tax'] += $advance['advance_exc_tax'];
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_tax_val'] += $advance['advance_tax_val'];
                    } else {
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_inc_tax'] = $advance['advance_inc_tax'];
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_exc_tax'] = $advance['advance_exc_tax'];
                        $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['advance_tax_val'] = $advance['advance_tax_val'];
                    }
                } elseif ($order_product_id == 'shipping' && $advance != 0) {
                    $tax_keys = $advance['advance_tax_rate'];
                    $tax_wise_group_advance[$advance_voucher_id][$tax_keys]['shipping'] = array(
                        'advance_inc_tax' => $advance['advance_inc_tax'],
                        'advance_exc_tax' => $advance['advance_exc_tax'],
                        'advance_tax_val' => $advance['advance_tax_val']
                    );
                }
            }
        }
        return $tax_wise_group_advance;
    }

    public static function updateAdvanceVouchersByVoucherId($db, $advance_voucher_id, $data_arr) {

        $sql = "UPDATE " . DB_PREFIX . "advance_voucher 
                SET value ='" . (float) $data_arr['value'] . "',
                    locked ='1'
                WHERE advance_voucher_id='" . $advance_voucher_id . "'";

        if ($db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to get sum of all cash discount of single order
     * @param $db            db object
     * @param $order_ids_arr will be in array
     */
    public static function getOrderCashDiscount($db, $order_ids_arr) {
        $sql = "SELECT oopt.order_id,
                    oav.suborder_id,
                    SUM(oav.value) as cash_discount
             FROM " . DB_PREFIX . "order_payment oopt 
             INNER JOIN
                  " . DB_PREFIX . "advance_voucher oav ON oopt.payment_id = oav.payment_id 
                  INNER JOIN
                  " . DB_PREFIX . "suborder osub ON osub.order_id = oav.order_id
             WHERE oopt.order_id IN (" . implode(',', $order_ids_arr) . ") AND
                   oopt.payment_gateway IN ('cashback', 'coupon') AND
                   osub.suborder_id = oav.suborder_id AND
                   osub.buyer_invoice_id > 0 AND
                   osub.invoice_no > 0 AND
                   osub.order_status_id > 0 AND
                   osub.order_status_id != 2 AND
                   oopt.successfull = 1 AND
                   oav.status = 1 AND
                   oav.locked = 1
             GROUP BY oav.suborder_id";
        $result = $db->query($sql);
        $return_arr = array();
        if ($result->num_rows > 0) {
            foreach ($result->rows as $value) {
                $suborder_id = $value['suborder_id'];
                $return_arr[$suborder_id] = (float) $value['cash_discount'];
            }
        }
        return $return_arr;
    }

    /*
     * We are changing this logic slightly. Advance voucher code will now work with below methods. 
     */

    /*
     * getActiveAdvanceVouchersWithPayment get all raw active advance vouchers and total payment for order
     *  @param 
     *      $db - db object
     *      $order_id - $order_id (int)
     *      $payment_id - payment_id(int) default 0
     *  @return: advance_vouchers_id wise array
     *  @author: NILESH, 2018
     */

    public static function getActiveAdvanceVouchersWithPayment($db, $order_id, $payment_id = 0) {
        $result = array();
        $sql = "SELECT oav.*,
                       oopt.payment_gateway,
                       oopt.payment_mode,
                       oopt.merchant_txn_id,
                       oopt.amount
                FROM " . DB_PREFIX . "advance_voucher oav INNER JOIN 
                     " . DB_PREFIX . "order_payment oopt ON oav.payment_id=oopt.payment_id
                WHERE oav.order_id = '" . (int) $order_id . "' AND 
                      oav.status = 1 AND 
                      oopt.successfull = 1";

        if ($payment_id > 0) {
            $sql .= " AND oav.payment_id = '" . (int) $payment_id . "'";
        }

        $sql .= " ORDER BY oav.payment_id ASC";

        $query = $db->query($sql);

        if ($query->num_rows) {
            $result = $query->rows;
        }
        return $result;
    }

    /*
     * getBalanceUnusedOrderPayment get Balance (remaining available advance) of a payment_id
     *  @param 
     *      $db - db object
     *      $order_id - $order_id (int)
     *      $payment_id - payment_id(int)
     *      $advance_vouchers_array - call getActiveAdvanceVouchersWithPayment 
     *  @return: balance available advance amount of a payment 
     *  @author: NILESH, 2018
     */

    public static function getBalanceUnusedOrderPayment($db, $order_id, $payment_id, $advance_vouchers_array = array()) {

        if (empty($advance_vouchers_array)) {
            $advance_vouchers_array = self::getActiveAdvanceVouchersWithPayment($db, $order_id, $payment_id);
        }

        $utilized_advance_value = 0;
        foreach ($advance_vouchers_array as $adv_arr) {
            $payment_id_key = $adv_arr['payment_id'];
            if ($payment_id_key != $payment_id) {
                continue;
            }
            $total_payment_of_curr_payment_id = $adv_arr['amount'];
            if (($adv_arr['locked'] = 1) || ((float) $adv_arr['value'] > 0)) {
                $utilized_advance_value += $adv_arr['value'];
            }
            $total_payment_of_curr_payment_id = $adv_arr['amount'];
        }
        $balance_unused_amount = MAX(($total_payment_of_curr_payment_id - $utilized_advance_value), 0);

        return (float) $balance_unused_amount;
    }

    /*
     * calculateAdvanceVouchers calculate advance vouchers value for update or display tentative
     *  @param 
     *      $db - db object
     *      $order_id - $order_id (int)
     *      $suborder_id - suborder_id array default blank array
     *      $advance_vouchers_array - call getActiveAdvanceVouchersWithPayment 
     *  @return: balance available advance amount of a payment 
     *  @author: NILESH, 2018
     */

    public static function calculateAdvanceVouchers($db, $order_id, $suborder_id) {
        //creating a local array storing advance_voucher_id for current suborder_id and their calculated value
        $calculated_advance_vouchers = array();
        $suborder_invoice_array = OrderInfo::getSuborderInvoices($db, $order_id);
        if (empty($suborder_invoice_array[$suborder_id])) {
            return $calculated_advance_vouchers;
        } elseif (($suborder_invoice_array[$suborder_id]['invoice_status'] == 'cancelled') ||
                ($suborder_invoice_array[$suborder_id]['invoice_status'] == 'undefined')) {
            return $calculated_advance_vouchers;
        }

        $advance_voucher_array = self::getActiveAdvanceVouchersWithPayment($db, $order_id);
        if (!empty($advance_voucher_array)) {
            $suborder_invoice_amount = (float) AdvanceVoucherLib::getTotalSubOrderInvoiceAmount($db, $suborder_id);
            $total_calculated_advance_amount = 0;
            $final_suborder_invoice_array = array();
            foreach ($advance_voucher_array as $advance_info) {
                $avdance_voucher_id = $advance_info['advance_voucher_id'];
                $suborder_id_key = $advance_info['suborder_id'];
                $payment_id = $advance_info['payment_id'];
                if (empty($final_suborder_invoice_array[$payment_id])) {
                    foreach ($advance_voucher_array as $inner_advance_info) {
                        if (($payment_id == $inner_advance_info['payment_id']) && ((float)$inner_advance_info['value']==0) && !empty($suborder_invoice_array[$inner_advance_info['suborder_id']])) {
                            $final_suborder_invoice_array[$payment_id][$inner_advance_info['suborder_id']] = $suborder_invoice_array[$inner_advance_info['suborder_id']];
                        }
                    }
                }

                if ($suborder_id_key != $suborder_id) {
                    continue;
                }
                $calculated_value = 0;
                if ($advance_info['locked'] == 1 || $suborder_invoice_array[$suborder_id]['invoice_status'] == 'invoiced') {
                    $calculated_value = (float) $advance_info['value'];
                } else if ((float) $advance_info['value'] > 0) {
                    $calculated_value = (float) ROUND(MAX(MIN(($suborder_invoice_amount - $total_calculated_advance_amount), $advance_info['value']), 0), 2);
                } else {

                    $balance_unused_amount = (float) self::getBalanceUnusedOrderPayment($db, $order_id, $payment_id, $advance_voucher_array);
                    if ($balance_unused_amount <= 0) {
                        $calculated_value = 0;
                        $calculated_advance_vouchers[$avdance_voucher_id] = self::_createCalculatedAdvance($order_id, $suborder_id, $calculated_value, $advance_info);
                        continue;
                    }

                    if (strtolower(trim($suborder_invoice_array[$suborder_id]['payment_code'])) === 'cod') {
                        $suborder_uninvoice_suborder_amount = OrderInfo::getUninvoicedSuborderAmounts($db, $order_id, '', $final_suborder_invoice_array[$payment_id]);
                        $calculated_value = ROUND($balance_unused_amount * ($suborder_invoice_amount / $suborder_uninvoice_suborder_amount), 2);
                    } else {
                        $calculated_value = MIN($balance_unused_amount, $suborder_invoice_amount);
                    }
                    $calculated_value = ROUND(MAX(MIN(($suborder_invoice_amount - $total_calculated_advance_amount), $calculated_value), 0), 2);
                }
                $calculated_advance_vouchers[$avdance_voucher_id] = self::_createCalculatedAdvance($order_id, $suborder_id, $calculated_value, $advance_info);
                $total_calculated_advance_amount += $calculated_value;
            }
        } 
        return $calculated_advance_vouchers;
    }

    /*
     * _createCalculatedAdvance create advance info array using data
     *  @return: array of advance info
     *  @author: NILESH, 2018
     */

    private static function _createCalculatedAdvance($order_id, $suborder_id, $calculated_value, $data) {

        $result = array(
            'value'                  => $calculated_value,
            'order_id'               => $order_id,
            'suborder_id'            => $suborder_id,
            'advance_voucher_id'     => $data['advance_voucher_id'],
            'advance_voucher_prefix' => $data['advance_voucher_prefix'],
            'advance_voucher_no'     => $data['advance_voucher_no'],
            'advance_voucher_date'   => $data['advance_voucher_date'],
            'user_id'                => $data['user_id'],
            'locked'                 => $data['locked'],
            'payment_gateway'        => $data['payment_gateway'],
            'payment_mode'           => $data['payment_mode']
        );
        return $result;
    }

    /**
     * Public function getTotalAdvanceOfSuborderPaymentGatewayWise used to get suborder total advance of suborder payment gateway wise
     * @param: Object DB object
     */
    public static function getTotalAdvanceOfSuborderPaymentGatewayWise($db, $order_id, $suborder_id, $use_wsb_credit = true  ) : array {
       
        $advance = array('cash_discount'=>0,'advance_collected'=>0);
        $cashback_types = array('cashback','coupon');
        $cashback_payments =0;
        $other_payments  = 0;
        $advance_in_advance_voucher = self::calculateAdvanceVouchers($db, $order_id, $suborder_id);
       
        //get advance used in privious cod failed CN's for surorder id
        if (!empty($advance_in_advance_voucher)) {
            foreach ($advance_in_advance_voucher as $key => $value) {
                if(isset($value['payment_gateway']) && in_array($value['payment_gateway'], $cashback_types)) {
                    $cashback_payments += $value['value'];
                }else if(
                    $use_wsb_credit || 
                    (isset($value['payment_gateway']) && $value['payment_gateway'] != 'wsb_credit') 
                ){
                    $other_payments += $value['value'];
                }
            }
            $privious_used_advanced = self::getAdvanceUsedPriviousForSuborder($db,$suborder_id);
            $privious_used_cash_discount = $privious_used_advanced['cash_discount'] ?? 0;
            $privious_used_advanced_collected = $privious_used_advanced['advance_collected'] ?? 0;
            $advance['cash_discount']       = round( ($cashback_payments - $privious_used_cash_discount), 2 );
            $advance['advance_collected']   = round( ($other_payments - $privious_used_advanced_collected), 2 );
        }
       
        return $advance;
    }


    /**
     * Public function to get advance used for cod failed in same suborder credit notes
     * @param: Object DB object
     * @param: string $usrorder_id
     * @return: array
     * @author: MSA, Sept 2018
    */
    public static function getAdvanceUsedPriviousForSuborder($db, $suborder_id)
    {
        $sql = "
                SELECT 
                   SUM(`advance_collected`) AS advance_collected,
                   SUM(`cash_discount`) AS cash_discount
                FROM
                    " . DB_PREFIX . "credit_note
                WHERE
                        is_cod_failed = 1
                    AND 
                        credit_note_status = 1     
                    AND
                        suborder_id = '".$db->escape($suborder_id)."'
                    ";
        $result = $db->query($sql);
        if($result->num_rows) {
            return $result->row;
        }
        return array();
    }


}

?>
