<?php

class CustomDebitNote {

    private $_debit_note_prefix;
    private $_debit_note_no;
    private $_order_no;
    private $_debit_note_id;
    private $_db;
    private $_registry;
    private $_load;
    private $_currency;
    private $_currency_code;
    private $_currency_value;
    private $_cancelled_flag = false;

    public function __construct($registry) {
        $this->_registry = $registry;

        if (method_exists($registry, 'get')) {
            $this->_db     = $registry->get('db');
            $this->_load   = $registry->get('load');
            $this->_format = $registry->get('currency');
        } else {
            $this->_db     = $registry->db;
            $this->_load   = $registry->load;
            $this->_format = $registry->currency;
        }
    }

    /* Method for calculate product price and set and many more
     * @param: product_field: array of product field with product_ids and total piece
     * @output: return an array of calculations of products
     * @author: Nishu, Dec 2016
     */
    public function calculationProducts($product_field = array()) {
        $data = array();
        $data['table_product_data'] = array();
        $order_product_ids = array();
        $product_qty = array();
        foreach ($product_field as $product) {
            $order_product_ids[] = $product['order_product_id'];
            $product_qty[$product['order_product_id']] = $product['product_quantity'];
        }
        $order_product_ids = array_unique($order_product_ids);
        // getting order products for this seller
        $sql_product = "SELECT oop.product_id,
                               oop.seller_sku,
                               oop.hsn_code,
                               oop.name,
                               oop.piece_in_set,
                               oop.price_per_piece,
                               oop.discount_per_piece,
                               oop.output_tax_rates,
                               oop.order_product_id,
                               oop.seller_cst,
                               oo.name as option_name,
                               oo.value as option_value 
                        FROM " . DB_PREFIX . "order_product oop ";
        $sql_product .= " LEFT JOIN " . DB_PREFIX . "order_option oo ON oo.order_product_id = oop.order_product_id ";
        $sql_product .= "WHERE oop.order_product_id IN (" . implode(',', $order_product_ids) . ")";
        
        $product_query = $this->_db->query($sql_product);

        // if no products, then no invoice can be generated
        if ($product_query->num_rows <= 0) {
            return false;
        }

        $table_product_data = array();
        $total_product_amount = 0;
        $total_product_tax = 0;
        $total_product_pieces = 0;
        $grand_total = 0;

        foreach ($product_query->rows as $product_info) {
            //DebitNote Qty
            $qty = $product_qty[$product_info['order_product_id']];

            $price_per_piece = (float) ($product_info['price_per_piece'] +$product_info['discount_per_piece']);
            $seller_tax      = (float) $product_info['output_tax_rates'];
            $total_pieces    = (int) $qty;

            $rate_per_piece = round($price_per_piece * (1 + $seller_tax / 100), 2);
            $tax_price = round($rate_per_piece - $price_per_piece, 2);

            $amount = round(($price_per_piece * $total_pieces), 2);
            $tax = round($tax_price * $total_pieces, 2);

            $table_product_data['product_data'][] = array(
                'order_product_id' => (int) $product_info['order_product_id'],
                'product_sku' => $product_info['seller_sku'],
                'product_name' => $product_info['name'],
                'quantity' => (int) $total_pieces,
                'piece_in_set' => (int) $product_info['piece_in_set'],
                'total_pieces' => (int) $total_pieces,
                'transfer_price_per_piece' => $rate_per_piece,
                'seller_tax' => (float)$seller_tax,
                'rate_per_piece' => $price_per_piece,
                'tax' => $tax ? $tax : '--',
                'amount' => $amount,
                'hsn_code' => $product_info['hsn_code'],
                'discount' => 0.00,
                'option_name' => $product_info['option_name'],
                'option_value' => $product_info['option_value']
            );
            $total_product_tax += $tax;
            $total_product_pieces += (int) $total_pieces;
            $total_product_amount += $amount;
        }
        $grand_total = $total_product_tax + $total_product_amount;
        $table_product_data['total_pieces'] = $total_product_pieces;
        $table_product_data['total_tax'] = $this->_format->format($total_product_tax, 'INR', 1);
        $table_product_data['total_amount'] = $this->_format->format($total_product_amount, 'INR', 1);
        $table_product_data['grand_total'] = $this->_format->format($grand_total, 'INR', 1);

        $table_product_data['amount_in_word'] = convert_to_currency_indian_format($grand_total);

        //echo "<pre>";print_r($table_product_data);die;
        return array(
            'table_product_data' => $table_product_data
        );
    }
}
