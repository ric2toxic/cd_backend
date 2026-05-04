<?php
// **********
// * Global *
// **********
$_['ms_viewinstore'] = 'स्टोर में देखें';
$_['ms_view'] = 'व्यू';
$_['ms_view_modify'] = 'देखें / बदलें';
$_['ms_publish'] = 'प्रकाशित करें';
$_['ms_unpublish'] = 'अप्रकाशित';
$_['ms_edit'] = 'संपादित करें';
$_['ms_clone'] = 'क्लोन';
$_['ms_relist'] = 'रीलिस्ट';
$_['ms_rate'] = 'दर';
$_['ms_download'] = 'डाउनलोड';
$_['ms_create_product'] = 'उत्पाद बनाएं';
$_['ms_delete'] = 'हटायें';
$_['ms_update'] = 'अपडेट करें';
$_['ms_type'] = 'प्रकार';
$_['ms_amount'] = 'राशि';
$_['ms_status'] = 'स्थिति';
$_['ms_date_paid'] = 'भुगतान की तारीख';
$_['ms_last_message'] = 'अंतिम संदेश';
$_['ms_description'] = 'विवरण';
$_['ms_id'] = '#';
$_['ms_by'] = 'द्वारा';
$_['ms_action'] = 'कार्रवाई';
$_['ms_sender'] = 'प्रेषक';
$_['ms_message'] = 'संदेश';
$_['ms_none'] = 'कोई नहीं';
$_['ms_payement_status']= 'भुगतान स्थिति';
$_['ms_invoice'] = 'चालान स्थिति';
$_['ms_debit_note'] = 'डेबिट नोट';
$_['ms_debit_amount'] = 'डेबिट राशि';
$_['ms_remarks'] = 'टिप्पणियां';


$_['ms_date_created'] = 'बनाने की तिथि';
$_['ms_date_processed'] = 'प्रोसेस की तारीख';
$_['ms_date'] = 'तारीख';

$_['ms_button_submit'] = 'भेजें';
$_['ms_button_add_special'] = 'नया विशेष मूल्य परिभाषित करें';
$_['ms_button_add_discount'] = 'नई गुणवत्ता छूट परिभाषित करें';
$_['ms_button_submit_request'] = 'अनुरोध भेजें';
$_['ms_button_save'] = 'सहेजें';
$_['ms_button_cancel'] = 'रद्द करें';
$_['ms_button_select_predefined_avatar'] = 'पूर्व-परिभाषित अवतार चुनें';

$_['ms_button_select_image'] = 'छवि चुनें';
$_['ms_button_select_images'] = 'छवियों को चुनें';
$_['ms_button_select_files'] = 'फ़ाइलें चुनें';

$_['ms_transaction_order_created'] = 'ऑर्डर बनाया गया';
$_['ms_transaction_order'] = 'बिक्री: ऑर्डर आईडी #%s';
$_['ms_transaction_sale'] = 'बिक्री: %s (-%s कमीशन)';
$_['ms_transaction_refund'] = 'रिफ़ंड: %s';
$_['ms_transaction_listing'] = 'उत्पाद लिस्टिंग: %s (%s)';
$_['ms_transaction_signup']      = '%s पर पंजीकरण शुल्क';
$_['ms_request_submitted'] = 'आपके अनुरोध को भेजा गया है';

$_['ms_totals_line'] = 'फिलहाल %s विक्रेता और %s उत्पाद बिक्री के लिए हैं!';

$_['ms_text_welcome'] = '<a href="%s">लॉग इन करें</a> | <a href="%s">एक खाता बनाएं</a> | <a href="%s">एक विक्रेता खाता बनाएं</a>.';
$_['ms_button_register_seller'] = 'विक्रेता को पंजीकृत करें';
$_['ms_register_seller_account'] = 'विक्रेता खाता पंजीकृत करें';

// Mails

// Seller
$_['ms_mail_greeting'] = "नमस्ते %s,\n\n";
$_['ms_mail_greeting_no_name'] = "नमस्ते,\n\n";
$_['ms_mail_ending'] = "\n\nसादर,\n%s";
$_['ms_mail_message'] = "\n\nसंदेश:\n%s";

$_['ms_mail_subject_seller_account_created'] = 'विक्रेता खाता बनाया गया है';
$_['ms_mail_seller_account_created'] = <<<ईओटी
%s पर आपका विक्रेता खाता बना दिया गया है!

अब आप अपने लिए उत्पाद लेना शुरू कर सकते हैं।
ईओटी;

$_['ms_mail_subject_seller_account_awaiting_moderation'] = 'विक्रेता खाता मॉडरेशन की प्रतीक्षा में है';
$_['ms_mail_seller_account_awaiting_moderation'] = <<<ईओटी
%s पर अपका विक्रेता खाता बना दिया गया है और अब संयम का इंतजार है।

जैसे ही इसे मंजूरी मिलती है आपको एक ईमेल प्राप्त होगी।
ईओटी;

$_['ms_mail_subject_product_awaiting_moderation'] = 'उत्पाद मॉडरेशन के लिए प्रतीक्षा में है';
$_['ms_mail_product_awaiting_moderation'] = <<<ईओटी
%s पर आपका उत्पाद %s संयम का इंतजार कर रहा है।

जैसे ही इसका प्रसंस्करण होता है आपको एक ईमेल प्राप्त होगी।
ईओटी;

$_['ms_mail_subject_product_purchased'] = 'नया ऑर्डर';
$_['ms_mail_product_purchased'] = <<<ईओटी
आपके उत्पाद %s से खरीदे गए हैं।

ग्राहक: %s (%s)

उत्पाद:
%s
कुल: %s
ईओटी;

$_['ms_mail_product_purchased_no_email'] = <<<ईओटी
आपके उत्पाद %s से खरीदे गए हैं।

ग्राहक: %s

उत्पाद:
%s
कुल: %s
ईओटी;

$_['ms_mail_subject_seller_contact'] = 'नया ग्राहक संदेश';
$_['ms_mail_seller_contact'] = <<<ईओटी
आप को एक नया ग्राहक संदेश प्राप्त हुआ है!

नाम: %s

ईमेल: %s

उत्पाद: %s

संदेश:
%s
ईओटी;

$_['ms_mail_seller_contact_no_mail'] = <<<ईओटी
आप को एक नया ग्राहक संदेश प्राप्त हुआ है!

नाम: %s

उत्पाद: %s

संदेश:
%s
ईओटी;

$_['ms_mail_product_purchased_info'] = <<<ईओटी
\n
डिलिवरी का पता:

%s %s
%s
%s
%s
%s %s
%s
%s
ईओटी;

$_['ms_mail_product_purchased_comment'] = 'टिप्पणी: %s';

$_['ms_mail_subject_withdraw_request_submitted'] = 'भुगतान अनुरोध भेजा गया';
$_['ms_mail_withdraw_request_submitted'] = <<<ईओटी
हमें आपका भुगतान संबंधी अनुरोध प्राप्त हो गया है। जैसे ही इसका प्रसंस्करण होता है आपको अपनी आय प्राप्त हो जाएगी।
ईओटी;

$_['ms_mail_subject_withdraw_request_completed'] = 'भुगतान पूर्ण हुआ';
$_['ms_mail_withdraw_request_completed'] = <<<ईओटी
आपका भुगतान संबंधी अनुरोध संसाधित हो गया है। अब आप को अपनी आय प्राप्त हो जानी चाहिए।
ईओटी;

$_['ms_mail_subject_withdraw_request_declined'] = 'भुगतान अनुरोध अस्वीकृत हुआ';
$_['ms_mail_withdraw_request_declined'] = <<<ईओटी
आपके भुगतान संबंधी अनुरोध को अस्वीकार कर दिया गया है। आपकी राशि %s पर आपके बकाया में लौटा दी गई है।
ईओटी;

$_['ms_mail_subject_transaction_performed'] = 'नया लेनदेन';
$_['ms_mail_transaction_performed'] = <<<ईओटी
%s पर आपके खाते में नया लेन-जोड़ दिया गया है।
ईओटी;

$_['ms_mail_subject_remind_listing'] = 'उत्पाद लिस्टिंग समाप्त हो चूका है';
$_['ms_mail_seller_remind_listing'] = <<<ईओटी
आपके उत्पाद %s को सूची से हटा दिया गया है। अपने खाते के विक्रेता के क्षेत्र में जाएं अगर आप फिर से उत्पाद को सूचीबद्ध करवाना चाहते हैं।
ईओटी;

// *********
// * Admin *
// *********
$_['ms_mail_admin_subject_seller_account_created'] = 'नया विक्रेता खाता बनाया गया';
$_['ms_mail_admin_seller_account_created'] = <<<ईओटी
%s पर नया विक्रेता खाता बना दिया गया है!
विक्रेता का नाम: %s (%s)
ई-मेल: %s
ईओटी;

$_['ms_mail_admin_subject_seller_account_awaiting_moderation'] = 'नया विक्रेता खाता मॉडरेशन की प्रतीक्षा में है';
$_['ms_mail_admin_seller_account_awaiting_moderation'] = <<<ईओटी
%s पर नया विक्रेता खाता बना दिया गया है और अब मोडरेशन का इंतजार है।
विक्रेता का नाम: %s (%s)
ई-मेल: %s

आप बहु-विक्रेता - पिछले कार्यालय में विक्रेता खंड में इसका प्रसंस्करण कर सकते हैं।
ईओटी;

$_['ms_mail_admin_subject_product_created'] = 'नया उत्पाद जोड़ा गया है';
$_['ms_mail_admin_product_created'] = <<<ईओटी
नए उत्पाद %s को %s में जोड़ दिया गया है।

आप पिछले कार्यालय में इसे संपादित कर सकते हैं और देख सकते हैं।
ईओटी;

$_['ms_mail_admin_subject_new_product_awaiting_moderation'] = 'नया उत्पाद मॉडरेशन की प्रतीक्षा में है';
$_['ms_mail_admin_new_product_awaiting_moderation'] = <<<ईओटी
नए उत्पाद %s को %s में जोड़ दिया गया है और मोडरेशन का इंतजार है।

आप बहु-विक्रेता - पिछले कार्यालय में उत्पाद खंड में इसका प्रसंस्करण कर सकते हैं।
ईओटी;

$_['ms_mail_admin_subject_edit_product_awaiting_moderation'] = 'उत्पाद संपादित किया गया है और मॉडरेशन की प्रतीक्षा में है';
$_['ms_mail_admin_edit_product_awaiting_moderation'] = <<<ईओटी
%s पर उत्पाद %s को संपादित किया गया है और मोडरेशन का इंतजार है।

आप बहु-विक्रेता - पिछले कार्यालय में उत्पाद खंड में इसका प्रसंस्करण कर सकते हैं।
ईओटी;

$_['ms_mail_admin_subject_withdraw_request_submitted'] = 'अदायगी अनुरोध मॉडरेशन की प्रतीक्षा में है';
$_['ms_mail_admin_withdraw_request_submitted'] = <<<ईओटी
नया भुगतान संबंधी अनुरोध प्रस्तुत कर दिया गया है।

आप बहु-विक्रेता - पिछले कार्यालय में वित्त खंड में इसका प्रसंस्करण कर सकते हैं।
ईओटी;

// Success
$_['ms_success_product_published'] = 'उत्पाद को पब्लिश किया गया';
$_['ms_success_product_unpublished'] = 'उत्पाद को अनपब्लिश किया गया';
$_['ms_success_product_created'] = 'उत्पाद बनाया गया';
$_['ms_success_product_updated'] = 'उत्पाद अपडेट किया गया';
$_['ms_success_product_deleted'] = 'उत्पाद हटाया गया';

// Errors
$_['ms_error_sellerinfo_nickname_empty'] = 'उपनाम खाली नहीं हो सकता';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'उपनाम में केवल अक्षरांकीय प्रतीक हो सकते हैं';
$_['ms_error_sellerinfo_nickname_utf8'] = 'उपनाम में केवल मुद्रण योग्य यूटीएफ-8 प्रतीक हो सकते हैं';
$_['ms_error_sellerinfo_nickname_latin'] = 'उपनाम में केवल अक्षरांकीय प्रतीक और विशेषक हो सकते हैं';
$_['ms_error_sellerinfo_nickname_length'] = 'उपनाम में 4 से 50 के बीच अक्षर होने चाहिए';
$_['ms_error_sellerinfo_nickname_taken'] = 'यह उपनाम पहले से ही ले लिया गया है';
$_['ms_error_sellerinfo_company_length'] = 'कंपनी का नाम 50 अक्षरों से अधिक लंबा नहीं हो सकता';
$_['ms_error_sellerinfo_description_length'] = 'विवरण 1000 अक्षरों से अधिक लंबा नहीं हो सकता';
$_['ms_error_sellerinfo_paypal'] = 'पेपाल पता अमान्य है';
$_['ms_error_sellerinfo_terms'] = 'चेतावनी: आपको %s से सहमत होना होगा!';
$_['ms_error_file_extension'] = 'एक्सटेंशन अमान्य है';
$_['ms_error_file_type'] = 'फ़ाइल प्रकार अमान्य है';
$_['ms_error_file_size'] = 'फ़ाइल बहुत बड़ी है';
$_['ms_error_image_too_small'] = 'छवि फ़ाइल का नाप बहुत छोटा है. न्यूनतम स्वीकृत आकार है: %s x %s (चौड़ाई x ऊँचाई)';
$_['ms_error_image_too_big'] = 'छवि फ़ाइल का नाप बहुत बड़ा है. अधिकतम स्वीकृत आकार है: %s x %s (चौड़ाई x ऊँचाई)';
$_['ms_error_file_upload_error'] = 'फ़ाइल अपलोड करने में त्रुटि';
$_['ms_error_form_submit_error'] = 'फ़ॉर्म प्रस्तुत करते समय त्रुटि हुई. अधिक जानकारी के लिए स्टोर के मालिक से संपर्क करें.';
$_['ms_error_form_notice'] = 'कृपया त्रुटियों के लिए सभी फॉर्म टैब की जांच करें.';
$_['ms_error_product_name_empty'] = 'उत्पाद का नाम रिक्त नहीं हो सकता';
$_['ms_error_product_name_length'] = 'उत्पाद का नाम %s से %s अक्षरों के बीच होना चाहिए';
$_['ms_error_product_description_empty'] = 'उत्पाद विवरण रिक्त नहीं हो सकता';
$_['ms_error_product_description_length'] = 'उत्पाद का विवरण %s से %s अक्षरों के बीच होना चाहिए';
$_['ms_error_product_tags_length'] = 'लाइन बहुत लंबी है';
$_['ms_error_product_price_empty'] = 'अपने उत्पाद के लिए एक मूल्य निर्दिष्ट करें';
$_['ms_error_product_price_invalid'] = 'अमान्य कीमत';
$_['ms_error_product_price_low'] = 'कीमत बहुत कम है';
$_['ms_error_product_price_high'] = 'कीमत बहुत अधिक है';
$_['ms_error_product_category_empty'] = 'कृपया एक श्रेणी चुनें';
$_['ms_error_product_model_empty'] = 'उत्पाद मॉडल खाली नहीं हो सकता';
$_['ms_error_product_model_length'] = 'उत्पाद मॉडल %s से %s अक्षरों के बीच होना चाहिए';
$_['ms_error_product_image_count'] = 'कृपया अपने उत्पाद के लिए कम से कम %s छवियां अपलोड करें';
$_['ms_error_product_download_count'] = 'कृपया अपने उत्पाद के लिए कम से कम %s डाउनलोड प्रस्तुत करें';
$_['ms_error_product_image_maximum'] = '%s से अधिक छवियों की अनुमति नहीं है';
$_['ms_error_product_download_maximum'] = '%s से अधिक डाउनलोड की अनुमति नहीं है';
$_['ms_error_product_message_length'] = 'संदेश 1000 अक्षरों से अधिक लंबा नहीं हो सकता';
$_['ms_error_product_attribute_required'] = 'यह गुण आवश्यक है';
$_['ms_error_product_attribute_long'] = 'यह मूल्य %s प्रतीकों से अधिक नहीं हो सकता';
$_['ms_error_withdraw_amount'] = 'राशि अमान्य है';
$_['ms_error_withdraw_balance'] = 'आपके बैलेंस में पर्याप्त धन नहीं है';
$_['ms_error_withdraw_minimum'] = 'न्यूनतम सीमा से कम नहीं निकाल सकते';
$_['ms_error_contact_email'] = 'कृपया एक मान्य ईमेल पता दें';
$_['ms_error_contact_captcha'] = 'कैप्चा कोड अमान्य है';
$_['ms_error_contact_text'] = 'संदेश 2000 अक्षरों से अधिक लंबा नहीं हो सकता';
$_['ms_error_contact_allfields'] = 'कृपया सभी फील्ड्स को भरें';
$_['ms_error_invalid_quantity_discount_priority'] = 'वरीयता फील्ड में त्रुटि है - कृपया सही मूल्य दर्ज करें';
$_['ms_error_invalid_quantity_discount_quantity'] = 'मात्रा 2 या उससे अधिक होनी चाहिए';
$_['ms_error_invalid_quantity_discount_price'] = 'दर्ज किया गया मात्रा डिस्काउंट मूल्य अमान्य है';
$_['ms_error_invalid_quantity_discount_dates'] = 'मात्रा डिस्काउंट के लिए दिनांक फील्ड भरा जाना चाहिए';
$_['ms_error_invalid_special_price_priority'] = 'वरीयता फील्ड में त्रुटि है - कृपया सही मूल्य दर्ज करें';
$_['ms_error_invalid_special_price_price'] = 'दर्ज किया गया विशेष मूल्य अमान्य है';
$_['ms_error_invalid_special_price_dates'] = 'विशेष कीमतों के लिए तारीख का फील्ड भरा जाना चाहिए';
$_['ms_error_seller_product'] = 'आप अपना खुदका उत्पाद कार्ट में\'नहीं जोड़ सकते';

$_['ms_error_sellerinfo_address_empty'] = 'पता1 खाली नहीं हो सकता';
$_['ms_error_sellerinfo_pincode_empty'] = 'पिनकोड खाली नहीं हो सकता';
$_['ms_error_sellerinfo_city_empty'] = 'शहर खाली नहीं हो सकता';
$_['ms_error_sellerinfo_company_empty'] = 'कंपनी खाली नहीं हो सकती';
$_['ms_error_sellerinfo_pan_empty'] = 'पैन खाली नहीं हो सकता';
$_['ms_error_sellerinfo_tin_empty'] = 'टिन खाली नहीं हो सकता';
$_['ms_error_sellerinfo_bank_ac_holder_name_empty'] = 'खाताधारक नाम खाली नहीं हो सकता';
$_['ms_error_sellerinfo_bank_ac_number_empty'] = 'खाता संख्या खाली नहीं हो सकता';
$_['ms_error_sellerinfo_ifsc_code_empty'] = 'आईएफएससी कोड खाली नहीं हो सकता';
$_['ms_error_sellerinfo_bank_name_empty'] = 'बैंक का नाम खाली नहीं हो सकता';
$_['ms_error_sellerinfo_bank_state_empty'] = 'राज्य रिक्त नहीं हो सकता';
$_['ms_error_sellerinfo_bank_city_empty'] = 'शहर खाली नहीं हो सकता';
$_['ms_error_sellerinfo_bank_branch_empty'] = 'शाखा खाली नहीं हो सकती';



// Account - General
$_['ms_account_unread_pm'] = 'आपके निजी संदेश अपठित है';
$_['ms_account_unread_pms'] = 'आपके %s निजी संदेश अपठित है';
$_['ms_account_register_new'] = 'नया विक्रेता<span class="subhead">(केवल निर्माता)</span>';
$_['ms_account_register_seller'] = 'विक्रेता खाता पंजीकृत करें';
$_['ms_account_register_seller_note'] = 'विक्रेता खाता बनाएं और हमारे स्टोर में अपने उत्पादों की बिक्री शुरू करें!';
$_['ms_account_register_details'] = 'चरण 1: आपका विवरण';
$_['ms_account_register_seller_success_heading'] = 'आपका विक्रेता खाता बनाया गया है!';
$_['ms_account_register_seller_success_message']  = '<p>%s में आपका स्वागत है!</p> <p>बधाई हो! आपका नया विक्रेता खाता सफलतापूर्वक बना दिया गया है!</p> <p>अब आप विक्रेता विशेषाधिकारों का लाभ लेते हुए, हमारे साथ अपने उत्पादों की बिक्री शुरू कर सकते हैं.</p> <p>अगर आपको कोई समस्या हो, तो<a href="%s">हमसे संपर्क करें</a>.</p>';
$_['ms_account_register_seller_success_approval'] = '<p>%s में आपका स्वागत है!</p> <p>आपका विक्रेता खाता पंजीकृत कर दिया गया है और अनुमोदन की प्रतीक्षा में है. एक बार स्टोर के मालिक द्वारा आपका खाता सक्रिय कर देने के बाद आपको ईमेल द्वारा सूचित कर दिया जाएगा.</p><p>अगर आपको कोई समस्या हो, तो <a href="%s">हमसे संपर्क करें</a>.</p>';

$_['ms_seller'] = 'विक्रेता';
$_['ms_seller_forseller'] = 'विक्रेता के लिए';
$_['ms_account_dashboard'] = 'डैशबोर्ड';
$_['ms_account_seller_account'] = 'विक्रेता खाता';
$_['ms_account_customer_account'] = 'ग्राहक खाता';
$_['ms_account_sellerinfo'] = 'विक्रेता का प्रोफ़ाइल';
$_['ms_account_sellerinfo_new'] = 'नया विक्रेता खाता';
$_['ms_account_newproduct'] = 'नया उत्पाद';
$_['ms_account_products'] = 'उत्पाद';
$_['ms_account_transactions'] = 'लेन-देन';
$_['ms_account_orders'] = 'ऑर्डर';
$_['ms_account_withdraw'] = 'अदायगी अनुरोध';
$_['ms_account_stats'] = 'आंकड़े';

// Account - New product
$_['ms_account_newproduct_heading'] = 'नया उत्पाद';
$_['ms_account_newproduct_breadcrumbs'] = 'नया उत्पाद';
//General Tab
$_['ms_account_product_tab_general'] = 'सामान्य';
$_['ms_account_product_tab_specials'] = 'विशेष कीमतें';
$_['ms_account_product_tab_discounts'] = 'छूट की मात्रा';
$_['ms_account_product_name_description'] = 'नाम और विवरण';
$_['ms_account_product_name'] = 'नाम';
$_['ms_account_product_name_note'] = 'अपने उत्पाद के लिए नाम निर्दिष्ट करें';
$_['ms_account_product_description'] = 'विवरण';
$_['ms_account_product_description_note'] = 'अपने उत्पाद के बारे में बताएं';
$_['ms_account_product_meta_description'] = 'मेटा टैग का वर्णन';
$_['ms_account_product_meta_description_note'] = 'अपने उत्पाद के लिए मेटा टैग वर्णन निर्दिष्ट करें';
$_['ms_account_product_meta_keyword'] = 'मेटा टैग कीवर्ड';
$_['ms_account_product_meta_keyword_note'] = 'अपने उत्पाद के लिए मेटा टैग कीवर्ड निर्दिष्ट करें';
$_['ms_account_product_tags'] = 'टैग';
$_['ms_account_product_tags_note'] = 'अपने उत्पाद के लिए टैग निर्दिष्ट करें।';
$_['ms_account_product_price_attributes'] = 'मूल्य और गुण';
$_['ms_account_product_price'] = 'कीमत';
$_['ms_account_product_price_note'] = 'अपने उत्पाद के लिए मूल्य चुनें';
$_['ms_account_product_listing_flat'] = 'इस उत्पाद के लिए लिस्टिंग शुल्क है <span>%s</span>';
$_['ms_account_product_listing_percent'] = 'इस उत्पाद के लिए लिस्टिंग शुल्क उत्पाद की कीमत पर आधारित है. वर्तमान लिस्टिंग शुल्क: <span>%s</span>.';
$_['ms_account_product_listing_balance'] = 'यह राशि की आपके विक्रेता बैलेंस में से कटौती की जाएगी।';
$_['ms_account_product_listing_paypal'] = 'उत्पाद प्रस्तुत करने के बाद आपको पेपाल भुगतान पेज ले जाया जाएगा।';
$_['ms_account_product_listing_itemname'] = '%s पर उत्पाद लिस्टिंग शुल्क';
$_['ms_account_product_listing_until'] = 'इस उत्पाद को %s तक प्रदर्शित किया जाएगा';
$_['ms_account_product_category'] = 'श्रेणी';
$_['ms_account_product_category_note'] = 'अपने उत्पाद के लिए श्रेणी चुनें';
$_['ms_account_product_enable_shipping'] = 'शिपिंग सक्षम करें';
$_['ms_account_product_enable_shipping_note'] = 'निर्दिष्ट करें कि क्या आपके उत्पाद के लिए शिपिंग की आवश्यकता है';
$_['ms_account_product_quantity'] = 'मात्रा';
$_['ms_account_product_quantity_note']    = 'अपने उत्पाद की मात्रा निर्दिष्ट करें';
$_['ms_account_product_files'] = 'फ़ाइलें';
$_['ms_account_product_download'] = 'डाउनलोड';
$_['ms_account_product_download_note'] = 'अपने उत्पाद के लिए फ़ाइलें अपलोड करें। इन एक्सटेंशन की अनुमति है: %s';
$_['ms_account_product_push'] = 'पिछले ग्राहकों को अद्यतन करें';
$_['ms_account_product_push_note'] = 'नए जोड़े गए और अद्यतन डाउनलोड पिछले ग्राहकों के लिए उपलब्ध कराये जायेंगे';
$_['ms_account_product_image'] = 'छवियां';
$_['ms_account_product_image_note'] = 'अपने उत्पाद के लिए छवियों को चुनें। पहली छवि को थम्बनेल के रूप में इस्तेमाल किया जाएगा. आप छवियों को खींचकर उनका क्रम बदल सकते हैं। इन एक्सटेंशन की अनुमति है: %s';
$_['ms_account_product_message_reviewer'] = 'समीक्षक के लिए संदेश';
$_['ms_account_product_message'] = 'संदेश';
$_['ms_account_product_message_note'] = 'समीक्षक के लिए आपका संदेश';
//Data Tab
$_['ms_account_product_tab_data'] = 'डेटा';
$_['ms_account_product_model'] = 'मॉडल';
$_['ms_account_product_sku'] = 'एसकेयू';
$_['ms_account_product_sku_note'] = 'स्टॉक कीपिंग यूनिट';
$_['ms_account_product_upc']  = 'यूपीसी';
$_['ms_account_product_upc_note'] = 'यूनिवर्सल प्रोडक्ट कोड';
$_['ms_account_product_ean'] = 'इएएन.';
$_['ms_account_product_ean_note'] = 'यूरोपीय आर्टिकल नंबर';
$_['ms_account_product_jan'] = 'जेएएन.';
$_['ms_account_product_jan_note'] = 'जापानी आर्टिकल नंबर';
$_['ms_account_product_isbn'] = 'आइएसबिएन';
$_['ms_account_product_isbn_note'] = 'इंटरनेशनल स्टैंडर्ड बुक नंबर';
$_['ms_account_product_mpn'] = 'एमपिएन';
$_['ms_account_product_mpn_note'] = 'मैन्युफैक्चरर पार्ट नंबर';
$_['ms_account_product_manufacturer'] = 'उत्पादक';
$_['ms_account_product_manufacturer_note'] = '(स्वत: पूर्ण)';
$_['ms_account_product_tax_class'] = 'टैक्स क्लास';
$_['ms_account_product_date_available'] = 'उपलब्ध तारीख';
$_['ms_account_product_stock_status'] = 'स्टॉक में नहीं है';
$_['ms_account_product_stock_status_note'] = 'जब किसी उत्पाद का स्टॉक खत्म हो जाए तब स्थिति दिखाएं';
$_['ms_account_product_subtract'] = 'स्टॉक घटाएं';

// Options
$_['ms_account_product_tab_options'] = 'विकल्प';
$_['ms_options_add'] = '+ विकल्प जोड़ें';
$_['ms_options_add_value'] = '+ मूल्य जोड़ें';
$_['ms_options_required'] = 'विकल्प को आवश्यक बनाएं';
$_['ms_options_price_prefix'] = 'मूल्य उपसर्ग बदलें';
$_['ms_options_price'] = 'मूल्य...';
$_['ms_options_quantity'] = 'मात्रा...';


$_['ms_account_product_manufacturer'] = 'उत्पादक';
$_['ms_account_product_manufacturer_note'] = '(स्वत: पूर्ण)';
$_['ms_account_product_tax_class'] = 'टैक्स क्लास';
$_['ms_account_product_date_available'] = 'उपलब्ध तारीख';
$_['ms_account_product_stock_status'] = 'स्टॉक में नहीं है';
$_['ms_account_product_stock_status_note'] = 'जब किसी उत्पाद का स्टॉक खत्म हो जाए तब स्थिति दिखाएं';
$_['ms_account_product_subtract'] = 'स्टॉक घटाएं';

$_['ms_account_product_priority'] = 'वरीयता';
$_['ms_account_product_date_start'] = 'आरंभ करने की तिथि';
$_['ms_account_product_date_end'] = 'अंतिम तारीख़';
$_['ms_account_product_sandbox'] = 'चेतावनी: पेमेन्ट गेटवे \'सैंडबॉक्स मोड में है\'. आपके खाते पर कोई शुल्क नहीं लगेगा.';



// Account - Edit product
$_['ms_account_editproduct_heading'] = 'उत्पाद संपादित करें';
$_['ms_account_editproduct_breadcrumbs'] = 'उत्पाद संपादित करें';

// Account - Clone product
$_['ms_account_cloneproduct_heading'] = 'उत्पाद को क्लोन करें';
$_['ms_account_cloneproduct_breadcrumbs'] = 'उत्पाद को क्लोन करें';

// Account - Relist product
$_['ms_account_relist_product_heading'] = 'उत्पाद को फिर से लिस्ट करें';
$_['ms_account_relist_product_breadcrumbs'] = 'उत्पाद को फिर से लिस्ट करें';

// Account - Seller
$_['ms_account_sellerinfo_heading'] = 'विक्रेता प्रोफ़ाइल';
$_['ms_account_sellerinfo_breadcrumbs'] = 'विक्रेता प्रोफ़ाइल';
$_['ms_account_sellerinfo_nickname'] = 'उपनाम';
$_['ms_account_sellerinfo_nickname_note'] = 'आपके वास्तविक व्यापार का नाम नहीं होना चाहिए';
$_['ms_account_sellerinfo_description'] = 'विवरण';
$_['ms_account_sellerinfo_description_note'] = 'अपने व्यवसाय और गुणवत्ता मानकों का वर्णन करें. अपनी कंपनी का नाम और संपर्क जानकारी शामिल न करें.';
$_['ms_account_sellerinfo_company'] = 'व्यवसाय का नाम';
$_['ms_account_sellerinfo_company_note'] = 'व्यवसाय नाम वही होना चाहिए जो टिन पर है';
$_['ms_account_sellerinfo_country'] = 'देश';
$_['ms_account_sellerinfo_country_dont_display'] = 'मेरा देश प्रदर्शित न करें';
$_['ms_account_sellerinfo_country_note'] = 'अपना देश चुनें।';
$_['ms_account_sellerinfo_zone'] = 'क्षेत्र / राज्य';
$_['ms_account_sellerinfo_zone_select'] = 'क्षेत्र/राज्य चुनें';
$_['ms_account_sellerinfo_zone_not_selected'] = 'कोई क्षेत्र/राज्य का चयन नहीं किया गया है';
$_['ms_account_sellerinfo_zone_note'] = 'सूची में से अपना क्षेत्र/राज्य चुनें।';
$_['ms_account_sellerinfo_avatar'] = 'अवतार';
$_['ms_account_sellerinfo_avatar_note'] = 'अपना अवतार चुनें';
$_['ms_account_sellerinfo_banner'] = 'बैनर';
$_['ms_account_sellerinfo_banner_note'] = 'एक बैनर अपलोड करें जिसे आपके प्रोफाइल पेज पर प्रदर्शित किया जाएगा';
$_['ms_account_sellerinfo_paypal'] = 'पेपाल';
$_['ms_account_sellerinfo_paypal_note'] = 'अपना पेपाल पता निर्दिष्ट करें';
$_['ms_account_sellerinfo_reviewer_message'] = 'समीक्षक के लिए संदेश';
$_['ms_account_sellerinfo_reviewer_message_note'] = 'समीक्षक के लिए आपका संदेश';
$_['ms_account_sellerinfo_terms'] = 'शर्तें स्वीकारें';
$_['ms_account_sellerinfo_terms_note'] = 'मैंने <a class="agree" href="%s" alt="%s"><b>%s</b></a> को पढ़ लिया है और मैं सहमत हूं';
$_['ms_account_sellerinfo_fee_flat'] = 'यह <span>%s</span> साइनअप शुल्क है %s पर एक विक्रेता बनने के लिए.';
$_['ms_account_sellerinfo_fee_balance'] = 'इस राशि की आपकी प्रारंभिक बकाया राशि में से कटौती की जाएगी।';
$_['ms_account_sellerinfo_fee_paypal'] = 'फॉर्म भेजने के बाद आपको पेपाल भुगतान पेज पर ले जाया जाएगा।';
$_['ms_account_sellerinfo_signup_itemname'] = '%s पर विक्रेता खाते का पंजीकरण';
$_['ms_account_sellerinfo_saved'] = 'विक्रेता खाते का डेटा सहेजा गया।';

$_['ms_account_sellerinfo_address1'] = 'पता 1';
$_['ms_account_sellerinfo_address2'] = 'पता 2';
$_['ms_account_sellerinfo_pincode'] = 'पिन कोड';
$_['ms_account_sellerinfo_city'] = 'शहर';
$_['ms_account_sellerinfo_tin'] = 'टिन';
$_['ms_account_sellerinfo_tan'] = 'टीएएन';
$_['ms_account_sellerinfo_pan'] = 'पैन';

$_['ms_account_status'] = 'आपके विक्रेता खाते की स्थिति: ';
$_['ms_account_status_tobeapproved'] = 'स्टोर के मालिक द्वारा मंजूरी मिलते ही आप अपने खाते का उपयोग कर पाएंगे।';
$_['ms_account_status_please_fill_in'] = 'कृपया विक्रेता खाता बनाने के लिए निम्न फॉर्म को पूरा करें.';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'सक्रिय';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'निष्क्रिय';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'अक्षम';
$_['ms_seller_status_' . MsSeller::STATUS_INCOMPLETE] = 'अपूर्ण';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'हटाए गए';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'भुगतान नहीं किया हुआ पंजीकरण शुल्क';

// Account - Products
$_['ms_account_products_heading'] = 'आपके उत्पाद';
$_['ms_account_products_breadcrumbs'] = 'आपके उत्पाद';
$_['ms_account_products_image'] = 'छवि';
$_['ms_account_products_product'] = 'उत्पाद';
$_['ms_account_products_sales'] = 'बिक्री';
$_['ms_account_products_earnings'] = 'कमाई';
$_['ms_account_products_status'] = 'स्थिति';
$_['ms_account_products_date'] = 'तारीख जोड़ी गई';
$_['ms_account_products_listing_until'] = 'इस तारीख तक लिस्टिंग';
$_['ms_account_products_action'] = 'कार्रवाई';
$_['ms_account_products_noproducts'] = 'अभी तक आपके पास\'कोई उत्पाद नहीं हैं!';
$_['ms_account_products_confirmdelete'] = 'आप वाकई अपने उत्पाद को हटाना चाहते हैं?';

$_['ms_not_defined'] = 'परिभाषित नहीं किया गया है';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'सक्रिय';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'निष्क्रिय';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'अक्षम';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'हटाए गए';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'भुगतान नहीं किया हुआ लिस्टिंग शुल्क';

// Account - Conversations and Messages
$_['ms_account_conversations'] = 'बातचीत';
$_['ms_account_messages'] = 'संदेश';

$_['ms_account_conversations_heading'] = 'आपकी बातचीत';
$_['ms_account_conversations_breadcrumbs'] = 'आपकी बातचीत';

$_['ms_account_conversations_status'] = 'स्थिति';
$_['ms_account_conversations_date_created'] = 'बनाने की तिथि';
$_['ms_account_conversations_with'] = 'इनके साथ बातचीत:';
$_['ms_account_conversations_title'] = 'शीर्षक';

$_['ms_conversation_title_product'] = 'उत्पाद के बारे में पूछताछ: %s';
$_['ms_conversation_title'] = '%s से पूछताछ';

$_['ms_account_conversations_read'] = 'पढें';
$_['ms_account_conversations_unread'] = 'अपठित';

$_['ms_account_messages_heading'] = 'संदेश';
$_['ms_account_messages_breadcrumbs'] = 'संदेश';

$_['ms_message_text'] = 'आपका संदेश';
$_['ms_post_message'] = 'संदेश भेजें';

$_['ms_customer_does_not_exist'] = 'ग्राहक खाता हटाया गया';
$_['ms_error_empty_message'] = 'संदेश खाली नहीं छोड़ा जा सकता';

$_['ms_mail_subject_private_message'] = 'नया निजी संदेश प्राप्त हुआ है';
$_['ms_mail_private_message'] = <<<ईओटी
आपको %s  से एक नया निजी संदेश प्राप्त हुआ है!

%s

%s

आप अपने खाते में संदेश भेजने वाले क्षेत्र में उत्तर दे सकते हैं।
ईओटी;

$_['ms_mail_subject_order_updated'] = 'आपके ऑर्डर #%s को %s द्वारा अपडेट किया गया है';
$_['ms_mail_order_updated'] = <<<ईओटी
%s पर आपके आर्डर को %s द्वारा अद्यतन कर दिया गया है:

आर्डर#: %s

उत्पाद:
%s

स्थिति: %s

टिप्पणी:
%s

ईओटी;

$_['ms_mail_subject_seller_vote'] = 'विक्रेता के लिए वोट दें';
$_['ms_mail_seller_vote_message'] = 'विक्रेता के लिए वोट दें';

// Account - Transactions
$_['ms_account_transactions_heading'] = 'आपके वित्त';
$_['ms_account_transactions_breadcrumbs'] = 'आपके वित्त';
$_['ms_account_transactions_balance'] = 'आपकी वर्तमान बैलेंस:';
$_['ms_account_transactions_earnings'] = 'इस तारीख तक आपकी कमाई:';
$_['ms_account_transactions_records'] = 'बैलंस रिकार्ड';
$_['ms_account_transactions_description'] = 'विवरण';
$_['ms_account_transactions_amount'] = 'राशि';
$_['ms_account_transactions_notransactions'] = 'आपने\'अभी तक कोई भी लेनदेन नहीं किया है!';

// Payments
$_['ms_payment_payments'] = 'भुगतान';
$_['ms_payment_order'] = 'ऑर्डर #%s';
$_['ms_payment_type_1'] = 'साइनअप शुल्क';
$_['ms_payment_type_2'] = 'लिस्टिंग शुल्क';
$_['ms_payment_type_3'] = 'मैनुअल भुगतान';
$_['ms_payment_type_4'] = 'भुगतान अनुरोध';
$_['ms_payment_type_5'] = 'पुनरावर्तित भुगतान';
$_['ms_payment_type_6'] = 'बिक्री';

$_['ms_payment_status_1'] = 'भुगतान नहीं किया हुआ';
$_['ms_payment_status_1'] = 'भुगतान किया हुआ';

// Account - Orders
$_['ms_account_orders_heading'] = 'आपके ऑर्डर';
$_['ms_account_orders_breadcrumbs'] = 'आपके ऑर्डर';
$_['ms_account_orders_id'] = 'ऑर्डर #';
$_['ms_account_orders_customer'] = 'ग्राहक';
$_['ms_account_orders_products'] = 'उत्पाद';
$_['ms_account_orders_history'] = 'इतिहास';
$_['ms_account_orders_addresses'] = 'पते';
$_['ms_account_orders_total'] = 'कुल रकम';
$_['ms_account_orders_view'] = 'ऑर्डर देखें';
$_['ms_account_orders_noorders'] = 'अभी आपके पास\'कोई ऑर्डर नहीं हैं!';
$_['ms_account_orders_nohistory'] = 'इस ऑर्डर के लिए अभी तक कोई भी इतिहास नहीं है!';
$_['ms_account_orders_change_status']    = 'ऑर्डर स्थिति बदलें';
$_['ms_account_orders_add_comment']    = 'ऑर्डर में टिप्पणी जोड़ें...';

$_['ms_account_order_information'] = 'ऑर्डर की जानकारी';

// Account - Dashboard
$_['ms_account_dashboard_heading'] = 'विक्रेता डैशबोर्ड';
$_['ms_account_dashboard_breadcrumbs'] = 'विक्रेता डैशबोर्ड';
$_['ms_account_dashboard_orders'] = 'नवीनतम ऑर्डर';
$_['ms_account_dashboard_overview'] = 'ओवरव्यू';
$_['ms_account_dashboard_seller_group'] = 'विक्रेता समूह';
$_['ms_account_dashboard_listing'] = 'लिस्टिंग शुल्क';
$_['ms_account_dashboard_sale'] = 'बिक्री शुल्क';
$_['ms_account_dashboard_royalty'] = 'रॉयल्टी';
$_['ms_account_dashboard_stats'] = 'आँकड़े';
$_['ms_account_dashboard_balance'] = 'वर्तमान बैलेंस';
$_['ms_account_dashboard_total_sales'] = 'कुल बिक्री';
$_['ms_account_dashboard_total_earnings'] = 'कुल कमाई';
$_['ms_account_dashboard_sales_month'] = 'इस महीने बिक्री';
$_['ms_account_dashboard_earnings_month'] = 'इस महीने कमाई';
$_['ms_account_dashboard_nav'] = 'क्विक नेविगेशन';
$_['ms_account_dashboard_nav_profile'] = 'अपना विक्रेता प्रोफ़ाइल संशोधित करें';
$_['ms_account_dashboard_nav_product'] = 'एक नया उत्पाद बनायें';
$_['ms_account_dashboard_nav_products'] = 'अपने उत्पादों का प्रबंधन करें';
$_['ms_account_dashboard_nav_orders'] = 'अपने ऑर्डर देखें';
$_['ms_account_dashboard_nav_balance'] = 'अपने वित्तीय रिकॉर्ड देखें';
$_['ms_account_dashboard_nav_payout'] = 'अपने भुगतान के लिए अनुरोध करें';
$_['ms_account_dashboard_total_orders'] = 'प्राप्त हुए कुल ऑर्डर';
$_['ms_account_dashboard_total_pieces_sold'] = 'कुल बेचे गए नग';
$_['ms_account_dashboard_nav_inventory'] = 'इंवेंटरी का प्रबंधन करें';
$_['ms_account_dashboard_change_password '] = 'पासवर्ड बदलें';

// Account - Request withdrawal
$_['ms_account_withdraw_heading'] = 'भुगतान अनुरोध';
$_['ms_account_withdraw_breadcrumbs'] = 'भुगतान अनुरोध';
$_['ms_account_withdraw_balance'] = 'आपकी वर्तमान बैलेंस:';
$_['ms_account_withdraw_balance_available'] = 'निकालने के लिए इतनी राशि उपलब्ध है:';
$_['ms_account_withdraw_minimum'] = 'न्यूनतम भुगतान राशि:';
$_['ms_account_balance_reserved_formatted'] = '-%s निकालने की लंबित राशि';
$_['ms_account_balance_waiting_formatted'] = '-%s प्रतीक्षा अवधि';
$_['ms_account_withdraw_description'] = 'भुगतान अनुरोध जरिए %s (%s) %s पर';
$_['ms_account_withdraw_amount'] = 'रकम:';
$_['ms_account_withdraw_amount_note'] = 'कृपया भुगतान राशि बताएं';
$_['ms_account_withdraw_method'] = 'भुगतान का तरीका:';
$_['ms_account_withdraw_method_note'] = 'कृपया भुगतान का तरीका चुनें';
$_['ms_account_withdraw_method_paypal'] = 'पेपाल';
$_['ms_account_withdraw_all'] = 'सभी आय वर्तमान में उपलब्ध';
$_['ms_account_withdraw_minimum_not_reached'] = 'आपकी कुल बैलेंस न्यूनतम भुगतान राशि से कम है!';
$_['ms_account_withdraw_no_funds'] = 'निकालने के लिए कोई राशि नहीं है.';
$_['ms_account_withdraw_no_paypal'] = 'कृपया पहले <a href="index.php?route=seller/account-profile">अपना पेपाल पता बताएं!</a>';

// Account - Stats
$_['ms_account_stats_heading'] = 'आंकड़े';
$_['ms_account_stats_breadcrumbs'] = 'आंकड़े';
$_['ms_account_stats_tab_summary'] = 'सारांश';
$_['ms_account_stats_tab_by_product'] = 'उत्पाद द्वारा';
$_['ms_account_stats_tab_by_year'] = 'वर्ष द्वारा';
$_['ms_account_stats_summary_comment'] = 'नीचे आपकी बिक्री का सारांश है';
$_['ms_account_stats_sales_data'] = 'विक्रय डेटा';
$_['ms_account_stats_number_of_orders'] = 'ऑर्डर की संख्या';
$_['ms_account_stats_total_revenue'] = 'कुल आय';
$_['ms_account_stats_average_order'] = 'औसत ऑर्डर';
$_['ms_account_stats_statistics'] = 'आंकड़े';
$_['ms_account_stats_grand_total'] = 'कुल बिक्री';
$_['ms_account_stats_product'] = 'उत्पाद';
$_['ms_account_stats_sold'] = 'बिक्री';
$_['ms_account_stats_total'] = 'कुल योग';
$_['ms_account_stats_this_year'] = 'इस वर्ष';
$_['ms_account_stats_year_comment'] = 'निर्दिष्ट अवधि के लिए <span id="sales_num">%s</span> बिक्री';
$_['ms_account_stats_show_orders'] = 'इन से ऑर्डर दिखाएं: ';
$_['ms_account_stats_month'] = 'महीना';
$_['ms_account_stats_num_of_orders'] = 'ऑर्डर की संख्या';
$_['ms_account_stats_total_r'] = 'कुल आय';
$_['ms_account_stats_average_order'] = 'औसत ऑर्डर';
$_['ms_account_stats_today'] = 'आज, ';
$_['ms_account_stats_yesterday'] = 'बिता कल, ';
$_['ms_account_stats_daily_average'] = 'के लिए दैनिक औसत ';
$_['ms_account_stats_date_month_format'] = 'वित्तीय वर्ष';
$_['ms_account_stats_projected_totals'] = 'के लिए अनुमानित योग ';
$_['ms_account_stats_grand_total_sales'] = 'कुल बिक्री';

// Product page - Seller information
$_['ms_catalog_product_sellerinfo'] = 'विक्रेता की जानकारी';
$_['ms_catalog_product_contact'] = 'इस विक्रेता से संपर्क करें';

$_['ms_footer'] = '<br><a href="http://multimerch.com/">multimerch.com</a> द्वारा मल्टीमर्च मार्केटप्लेस';

// Catalog - Sellers list
$_['ms_catalog_sellers_heading'] = 'विक्रेता';
$_['ms_catalog_sellers_country'] = 'देश:';
$_['ms_catalog_sellers_website'] = 'वेबसाइट:';
$_['ms_catalog_sellers_company'] = 'कंपनी:';
$_['ms_catalog_sellers_totalsales'] = 'बिक्री:';
$_['ms_catalog_sellers_totalproducts'] = 'उत्पाद:';
$_['ms_sort_country_desc'] = 'देश (Z - A)';
$_['ms_sort_country_asc'] = 'देश (A - Z)';
$_['ms_sort_nickname_desc'] = 'नाम (Z - A)';
$_['ms_sort_nickname_asc'] = 'नाम (A - Z)';

// Catalog - Seller profile page
$_['ms_catalog_sellers'] = 'विक्रेता';
$_['ms_catalog_sellers_empty'] = 'अभी तक कोई भी विक्रेता नहीं है।';
$_['ms_catalog_seller_profile'] = 'प्रोफ़ाइल देखें';
$_['ms_catalog_seller_profile_heading'] = '%s\'का प्रोफ़ाइल';
$_['ms_catalog_seller_profile_breadcrumbs'] = '%s\'का प्रोफ़ाइल';
$_['ms_catalog_seller_profile_about_seller'] = 'विक्रेता का परिचय';
$_['ms_catalog_seller_profile_products'] = 'विक्रेता\'के कुछ उत्पाद';
$_['ms_catalog_seller_profile_tab_products'] = 'उत्पाद';

$_['ms_catalog_seller_profile_social'] = 'सोशल प्रोफ़ाइल';
$_['ms_catalog_seller_profile_country'] = 'देश:';
$_['ms_catalog_seller_profile_zone'] = 'क्षेत्र/राज्य:';
$_['ms_catalog_seller_profile_website'] = 'वेबसाइट:';
$_['ms_catalog_seller_profile_company'] = 'कंपनी:';
$_['ms_catalog_seller_profile_totalsales'] = 'कुल बिक्री:';
$_['ms_catalog_seller_profile_totalproducts'] = 'कुल उत्पाद:';
$_['ms_catalog_seller_profile_view_products'] = 'उत्पाद देखें';
$_['ms_catalog_seller_profile_view'] = '%s\'के सभी उत्पाद देखें';

// Catalog - Seller's products list
$_['ms_catalog_seller_products_heading'] = '%s\'के उत्पाद';
$_['ms_catalog_seller_products_breadcrumbs'] = '%s\'के उत्पाद';
$_['ms_catalog_seller_products_empty'] = 'इस विक्रेता के पास\'अभी तक कोई उत्पाद नहीं हैं!';

// Catalog - Seller contact dialog
$_['ms_sellercontact_title'] = 'विक्रेता को संदेश भेजें';
$_['ms_sellercontact_signin'] = '%s से संपर्क करने के लिए कृपया <a href="%s">साइन इन करें</a> ';
$_['ms_sellercontact_sendto'] = '%s को संदेश भेजें';
$_['ms_sellercontact_text'] = 'संदेश: ';
$_['ms_sellercontact_captcha'] = 'कैप्चा';
$_['ms_sellercontact_sendmessage'] = '%s को संदेश भेजें';
$_['ms_sellercontact_close'] = 'बंद करें';
$_['ms_sellercontact_send'] = 'भेजें';
$_['ms_sellercontact_success'] = 'आपका संदेश सफलतापूर्वक भेज दिया गया है';

//
$_['ms_account_sellerinfo_bank_account_holder_name'] = 'खाताधारक का नाम';
$_['ms_account_sellerinfo_bank_account_number'] = 'बैंक खाता नंबर';
$_['ms_account_sellerinfo_retype_account_number'] = 'बैंक खाता नंबर';
$_['ms_account_sellerinfo_bank'] = 'बैंक';
$_['ms_account_sellerinfo_ifsc_code'] = 'आईएफएससी कोड';
$_['ms_account_sellerinfo_state'] = 'राज्य';
$_['ms_account_sellerinfo_city'] = 'शहर';
$_['ms_account_sellerinfo_branch'] = 'शाखा';

//
$_['ms_account_inventory_breadcrumbs'] = 'इंवेंटरी का प्रबंधन करें';

//Account Customers
$_['ms_account_customer_id']            = 'क्रमांक';
$_['ms_account_customer_name']          = 'ग्राहक का नाम';
$_['ms_account_customer_email']         = 'ईमेल';
$_['ms_account_customer_telephone']     = 'टेलीफोन';
$_['ms_account_customer_ip']            = 'आईपी';
$_['ms_account_customer_date']          = 'दिनांक पंजीकृत';
$_['ms_account_customer_breadcrumb']    = 'आपके ग्राहक';

?>
