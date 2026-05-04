<?php

class AdvanceVoucher {

    public static function getAdvanceVouchers($db, $order_id, $suborder_id) {
        $result = self::getAdvanceVouchersByData($db, array('order_id' => $order_id, 'suborder_id' => $suborder_id));
        
        if (empty($result))
            return false;
        $suborder_to_advance = array();
        $advance_for_update = array();
        array_walk($result, function( $val, $key ) use (&$suborder_to_advance, &$advance_for_update) {
            if ($val['value'] > 0) {
                $suborder_to_advance[$val['suborder_id']] = array(
                    'payment_id' => $val['payment_id'],
                    'value' => $val['value'],
                );
            } else {
                $advance_for_update[] = $val;
            }
        });
        $advance_factor = 0;
        if (!empty($suborder_to_advance[$suborder_id]['value'])) {
            $payment_id = $suborder_to_advance[$suborder_id]['payment_id'];
            $total_advance_data = AdvanceVoucher::getOrderPaymentDataByPaymentId($db, $payment_id);
            $total_advance = !empty($total_advance_data['amount']) ? $total_advance_data['amount'] : 0;
            $advance_factor = (float) $suborder_to_advance[$suborder_id]['value'] / (float) $total_advance;
        }
        $suborder_to_ad = array();

        if (!empty($advance_for_update)) {
            foreach ($advance_for_update as $key => $value) {
                if (!empty($advance_factor)) {
                    $payment_value_data = AdvanceVoucher::getOrderPaymentDataByPaymentId($db, $value['payment_id']);
                    $payment_value =!empty($payment_value_data['amount']) ? $payment_value_data['amount'] : 0;
                    $suborder_to_ad[$value['payment_id']] = array('value' => (float) $advance_factor * (float) $payment_value, 'advance_voucher_id' => $value['advance_voucher_id']);
                } else {
                    $advances = OrderPayment::readjustAdvanceInSuborders($db, $value['payment_id']);
                    $suborder_to_ad[$value['payment_id']] = $advances[$suborder_id];
                }
            }
        } else {
            $suborder_to_ad = array_combine(
                    array_column($result, 'payment_id'), $result
            );
        }

        return $suborder_to_ad;
    }

    public static function getAdvanceVouchersByData($db, $data) {

        if (!empty($data)) {
            $sql = "Select 
                           av.order_id, 
                           av.suborder_id, 
                           av.advance_voucher_id, 
                           av.payment_id,
                           av.value,
                           av.user_id,
                           concat(av.advance_voucher_prefix,'-',av.advance_voucher_no) as advance_voucher_no,
                           av.advance_voucher_date,
                           op.payment_gateway
                from " . DB_PREFIX . "advance_voucher AS av 

                INNER JOIN 
                        " . DB_PREFIX . "order_payment AS op ON op.payment_id = av.payment_id
                
                WHERE 1 = 1 ";

            if (!empty($data['order_id'])) {
                $sql .= " AND av.order_id = '" . (int) $data['order_id'] . "' ";
            }

            if (!empty($data['suborder_id'])) {
                $sql .= " AND av.suborder_id = '" . $db->escape($data['suborder_id']) . "' ";
            }

            if (!empty($data['payment_id'])) {
                $sql .= " AND av.payment_id = '" . (int) $data['payment_id'] . "' ";
            }

            $result = $db->query($sql);
            if ($result->num_rows) {
                return $result->rows;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getAdvanceVouchersBySuborderGroup($db, $order_id) {
        $sql = "SELECT suborder_id,
                sum(value) as value,
                user_id
                from " . DB_PREFIX . "advance_voucher 
                WHERE order_id='" . (int) $order_id . "'  AND status = 1 group by suborder_id";
        $result = $db->query($sql);
        if ($result->num_rows) {
            return $result->rows;
        } else {
            return false;
        }
    }

    public static function getOrderPaymentByPaymentId($db, $payment_id) {
        $sql = "Select amount 
                from " . DB_PREFIX . "order_payment 
                where  payment_id ='" . (int) $payment_id . "'";

        $result = $db->query($sql);
        if ($result->num_rows)
            return $result->row['amount'];
        else {
            return false;
        }
    }

    public static function getSumOfAdvancePaymentByOrderId($db, $order_id) {
        $sql = "Select sum(amount) as amount 
                from " . DB_PREFIX . "order_payment 
                where successfull='1' and amount>0 and order_id ='" . $db->escape($order_id) . "' group by order_id";

        $result = $db->query($sql);
        if ($result->num_rows)
            return $result->row['amount'];
        else {
            return false;
        }
    }

    public static function getPaymentRecordByOrderId($db, $order_id) {
        $sql = "Select amount, 
                payment_id, 
                user_id 
                from " . DB_PREFIX . "order_payment 
                where successfull='1' and amount>0 and order_id ='" . $db->escape($order_id) . "'";

        $result = $db->query($sql);
        if ($result->num_rows)
            return $result->rows;
        else {
            return false;
        }
    }

    public static function updateAdvanceVouchers($db, $advance_voucher_id, $data_arr) {
        $sql = "update " . DB_PREFIX . "advance_voucher 
                set value ='" . $data_arr['value'] . "'
                where advance_voucher_id='" . $advance_voucher_id . "' and value=0";
        if ($db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }
    public static function getAdvanceBySuborderId($db, $order_id, $suborder_id) {
        $advance = 0;
        $advance_in_advance_voucher = self::getAdvanceVouchers($db, $order_id, $suborder_id);
        if (!empty($advance_in_advance_voucher)) {
            foreach ($advance_in_advance_voucher as $key => $value) {
                $advance += (float) $value['value'];
            }
        } else {
            $sql = "SELECT custom_totals FROM oc_suborder where order_id=" . (int) $order_id . " and suborder_id='" . $db->escape($suborder_id) . "'";
            if ($result = $db->query($sql)) {
                $result = unserialize($result->row['custom_totals']);
                $advance = !empty($result['advance']['value']) ? (float) $result['advance']['value'] : 0;
            }
        }
        return $advance;
    }

    public static function getOrderPaymentDataByPaymentId($db, $payment_id) {
        $sql = "Select amount,payment_gateway
                from " . DB_PREFIX . "order_payment 
                where  payment_id ='" . (int) $payment_id . "'";

        $result = $db->query($sql);
        if ($result->num_rows)
            return $result->row;
        else {
            return false;
        }
    }

    public static function getAdvanceVouchersByOrderIds($db, $order_ids) {
        if (empty($order_ids)) return array();
        $response = array();
        foreach($order_ids as $order_id) {
            $response[$order_id] = array();
        }

        $sql = "SELECT order_id, suborder_id,
                sum(value) as value,
                user_id
                from " . DB_PREFIX . "advance_voucher 
                WHERE order_id IN(" . implode(",", $order_ids) . ")  AND status = 1 group by suborder_id";
        $result = $db->query($sql);
        if ($result->num_rows) {
            foreach($result->rows as $row) {
                $response[$row['order_id']][] = $row;
            }
        }

        return $response;
    }

}

?>
