<?php

//Define All order status as CONSTANTs
define('ORDER_STATUS', array(
                        'Missing'               => 0,
                        'Pending'               => 1,
                        'Canceled'              => 2,
                        'Out for delivery'      => 4,
                        'Complete'              => 5,
                        'Client Dispute'        => 6,
                        'Failed'                => 8,
                        'Processed'             => 9,
                        'Parcel Lost'           => 11,
                        'Reversed'              => 12,
                        'Shipped'               => 13,
                        'Shipped with tracking' => 14,
                        'Delivered'             => 15,
                        'Tentative Processed'   => 16,
                        'Delivery Issues'       => 17
                       )
  );

//List of order status with generic flow for customer
define('GENERIC_ORDER_STATUS', array(
                                  'order_received' => 'Order Received',
                                  'processed'      => 'Processed',
                                  'shipped'        => 'Shipped',
                                  'delivered'      => 'Delivered'
                                )
  );

// Faild/Not_From_Generic_Flow order status
define('FAILED_ORDER_STATUS', array(
                                  'failed'          => 'Failed',
                                  'cancelled'       => 'Cancelled',
                                  'client_dispute'  => 'Client Dispute',
                                  'delivery_issues' => 'Delivery Issues',
                                  'parcel_lost'     => 'Parcel Lost',
                                  'reversed'        => 'Reversed'
                                )
  );

//Define Order status Labels by order status keys
define('ORDER_STATUS_LABEL', array(
                              'order_received'  => 'Order Received',
                              'processed'       => 'Processed',
                              'shipped'         =>'Shipped',
                              'delivered'       => 'Delivered',
                              'failed'          => 'Failed',
                              'cancelled'       => 'Cancelled',
                              'client_dispute'  => 'Client Dispute',
                              'delivery_issues' => 'Delivery Issues',
                              'parcel_lost'     => 'Parcel Lost',
                              'reversed'        => 'Reversed'
                             )
);

//Order Status Clustering for Customer
define('ORDER_STATUS_CLUSTERS', array(
                              'order_received'=> array(
                                                  ORDER_STATUS['Pending']
                                                 ),
                              'processed'     => array(
                                                  ORDER_STATUS['Processed'],
                                                  ORDER_STATUS['Tentative Processed']
                                                 ),
                              'shipped'       => array(
                                                  ORDER_STATUS['Shipped'],
                                                  ORDER_STATUS['Shipped with tracking'],
                                                  ORDER_STATUS['Out for delivery']
                                                 ),
                              'delivered'      => array(
                                                  ORDER_STATUS['Complete'],
                                                  ORDER_STATUS['Delivered']
                                                 ),
                              'failed'         => array(
                                                  ORDER_STATUS['Failed']
                                                  ),
                              'cancelled'      => array(
                                                  ORDER_STATUS['Canceled']
                                                  ),
                              'client_dispute' => array(
                                                  ORDER_STATUS['Client Dispute']
                                                  ),
                              'delivery_issues'=> array(
                                                  ORDER_STATUS['Delivery Issues']
                                                  ),
                              'parcel_lost'    => array(
                                                  ORDER_STATUS['Parcel Lost']
                                                  ),
                              'reversed'       => array(
                                                  ORDER_STATUS['Reversed']
                                                  )
                              )
  );

//Order Status Clustering for Customer
define('ORDER_STATUS_FOR_PROGRESS_BAR', array(
                                        'order_received'  => array(
                                                              'progress'   => '15',
                                                              'color'      => 'green_color',
                                                              'color_code' => '#388E3C'
                                                             ),
                                        'processed'       => array(
                                                              'progress'   => '25',
                                                              'color'      => 'green_color',
                                                              'color_code' => '#388E3C'
                                                             ),
                                        'shipped'         => array(
                                                              'progress'   => '50',
                                                              'color'      => 'green_color',
                                                              'color_code' => '#388E3C'
                                                             ),
                                        'delivered'       => array(
                                                              'progress'   => '100',
                                                              'color'      => 'green_color',
                                                              'color_code' => '#388E3C'
                                                             ),
                                        'failed'          => array(
                                                              'progress'   => '100',
                                                              'color'      => 'red_color',
                                                              'color_code' => '#f03140'
                                                             ),
                                        'cancelled'       => array(
                                                              'progress'   => '100',
                                                              'color'      => 'red_color',
                                                              'color_code' => '#f03140'
                                                             ),
                                        'client_dispute'  => array(
                                                              'progress'   => '100',
                                                              'color'      => 'orange_color',
                                                              'color_code' => '#FFB74D'
                                                             ),
                                        'delivery_issues' => array(
                                                              'progress'   => '80',
                                                              'color'      => 'orange_color',
                                                              'color_code' => '#FFB74D'
                                                             ),
                                        'parcel_lost'     => array(
                                                              'progress'   => '100',
                                                              'color'      => 'red_color',
                                                              'color_code' => '#f03140'
                                                             ),
                                        'reversed'        => array(
                                                              'progress'   => '100',
                                                              'color'      => 'red_color',
                                                              'color_code' => '#f03140'
                                                             ),
                                        )
  );

//Define All order status  with Extra Info As CONSTANTs
define('CURRENT_ORDER_STATUS_INFO', array(
                                      ORDER_STATUS['Pending']               => array(),
                                      ORDER_STATUS['Processed']             => array(),
                                      ORDER_STATUS['Tentative Processed']   => array(),
                                      ORDER_STATUS['Shipped']               => array(
                                                                               'track' => array(
                                                                                            'message' => 'Shipment Status: ',
                                                                                            'button'   => array(
                                                                                                            'label'    => 'Track',
                                                                                                            'icon'     => 'fa fa-map-marker',
                                                                                                            'color'    => 'white_color',
                                                                                                            'bg_color' => 'blue_bg_color',
                                                                                                            'url'      => 'javascript:void(0);'
                                                                                                          )
                                                                                            )
                                                                               ),
                                      ORDER_STATUS['Shipped with tracking'] => array(
                                                                                'track' => array(
                                                                                            'message' => 'Shipment Status: ',
                                                                                            'button'   => array(
                                                                                                            'label' => 'Track',
                                                                                                            'icon'  => 'fa fa-map-marker',
                                                                                                            'color'    => 'white_color',
                                                                                                            'bg_color' => 'blue_bg_color',
                                                                                                            'url'      => 'javascript:void(0);'
                                                                                                          )
                                                                                            )
                                                                               ),
                                      ORDER_STATUS['Out for delivery']      => array(
                                                                                'track' => array(
                                                                                            'message' => 'Shipment is Out for Delivery: ',
                                                                                            'button'   => array(
                                                                                                            'label'    => 'Track',
                                                                                                            'icon'     => 'fa fa-map-marker',
                                                                                                            'color'    => 'white_color',
                                                                                                            'bg_color' => 'blue_bg_color',
                                                                                                            'url'      => 'javascript:void(0);'
                                                                                                          )
                                                                                            )
                                                                               ),
                                      ORDER_STATUS['Complete']              => array(
                                                                                'return'=> array(
                                                                                              'message' => '',
                                                                                              'button'   => array(
                                                                                                              'label'    => 'Return/Replacment',
                                                                                                              'icon'     => '',
                                                                                                              'color'    => 'white_color',
                                                                                                              'bg_color' => 'red_bg_color',
                                                                                                              'url'      => 'javascript:void(0);'
                                                                                                            )
                                                                                              )
                                                                                ),
                                      ORDER_STATUS['Delivered']             => array(
                                                                                'track' => array(
                                                                                            'message' => 'Shipment Delivered',
                                                                                            'button'   => array(
                                                                                                          'label'    => 'Track',
                                                                                                          'icon'     => 'fa fa-map-marker',
                                                                                                          'color'    => 'white_color',
                                                                                                          'bg_color' => 'blue_bg_color',
                                                                                                          'url'      => 'javascript:void(0);'
                                                                                                          )
                                                                                            ),
                                                                                'return'=> array(
                                                                                              'message' => '',
                                                                                              'button'   => array(
                                                                                                              'label'    => 'Return/Replacment',
                                                                                                              'icon'     => 'fa fa-reply',
                                                                                                              'color'    => 'white_color',
                                                                                                              'bg_color' => 'red_bg_color',
                                                                                                              'url'      => 'javascript:void(0);'
                                                                                                            )
                                                                                              )
                                                                                
                                                                                ),
                                      ORDER_STATUS['Failed']                => array(
                                                                               'track' => array(
                                                                                          'message' => 'Shipment Delivery Failed by Customer',
                                                                                          'button'   => array(
                                                                                                          'label'    => 'Track',
                                                                                                          'icon'     => 'fa fa-map-marker',
                                                                                                          'color'    => 'white_color',
                                                                                                          'bg_color' => 'blue_bg_color',
                                                                                                          'url'      => 'javascript:void(0);'
                                                                                                        )
                                                                                          )
                                                                               ),
                                      ORDER_STATUS['Canceled']              => array(
                                                                                'cancelled' => array(
                                                                                          'message' => 'Order Canceled : ',
                                                                                          'button'   => array()
                                                                                          )
                                                                               ),
                                      ORDER_STATUS['Client Dispute']        => array(),
                                      ORDER_STATUS['Delivery Issues']       => array(
                                                                                'track' => array(
                                                                                            'message' => 'Courier Partner unable to Deliver. Kindly Take Delivery.',
                                                                                            'button'   => array(
                                                                                                          'label'    => 'Track',
                                                                                                          'icon'     => 'fa fa-map-marker',
                                                                                                          'color'    => 'white_color',
                                                                                                          'bg_color' => 'blue_bg_color',
                                                                                                          'url'      => 'javascript:void(0);'
                                                                                                          )
                                                                                            )
                                                                               ),
                                      ORDER_STATUS['Parcel Lost']           => array(),
                                      ORDER_STATUS['Reversed']              => array()
                                     )
  );

//Order status with not editable status(i.e. out for shipment)
define('ORDER_STATUS_SHIPPED', array(
                                ORDER_STATUS['Shipped'],
                                ORDER_STATUS['Shipped with tracking'],
                                ORDER_STATUS['Out for delivery'],
                                ORDER_STATUS['Complete'],
                                ORDER_STATUS['Delivered'],
                                ORDER_STATUS['Failed'],
                                ORDER_STATUS['Canceled'],
                                ORDER_STATUS['Reversed'],
                                ORDER_STATUS['Parcel Lost'],
                                ORDER_STATUS['Delivery Issues'],
                                ORDER_STATUS['Client Dispute']
                               )
);

//Define Order type Filtering (Like: Delivered, Cancelled, Returned etc.)
define('ORDER_TYPE_FILTER', array(
                             'MY_ORDERS'              => 0,
                             'PAYMENT_PENDING_ORDERS' => 1,
                             'DELIVERED_ORDERS'       => 2,
                             'CANCELLED_ORDERS'       => 3,
                             'RETURNED_ORDERS'        => 4
                            )
);