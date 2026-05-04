<?php
// Heading
//$_['heading_title']                = 'खाता लॉग इन';
$_['heading_title']                = 'खाता';

// Text
$_['text_account']                 = 'खाता';
$_['text_returning_customer']      = 'पुनः आने वाले ग्राहक';
$_['text_i_am_returning_customer'] = 'मैं अक्सर आपने वाला ग्राहक हूं';
//$_['text_login']                   = 'लॉग इन करें';
//$_['text_new_customer']            = 'नए ग्राहक';
//$_['text_register']                = 'खाता पंजीकृत करें';
//$_['text_register_account']        = 'खाता बनाकर आप तेजी से शॉपिंग कर पाएंगे, ऑर्डर की स्थिति से\'रह पाएंगे, और अपने पहले के ऑर्डरों को ट्रैक कर पाएंगे.';
$_['text_forgotten']               = 'क्या आप अपना पासवर्ड भूल गए हैं?';

// Entry
$_['entry_email']                  = 'ईमेल पता';
$_['entry_password']               = 'पासवर्ड';
$_['entry_mobile_or']              = '&nbsp;या मोबाइल';


// Error
$_['error_login']                  = 'चेतावनी: ई-मेल पता और / या पासवर्ड से कोई मेल नहीं हुआ.';
$_['error_attempts']               = 'चेतावनी: आपका खाता लॉग इन प्रयासों की अनुमति दी गई सीमा को पार कर गया है। कृपया 1 घंटे में फिर से प्रयास करें.';
$_['error_approved']               = 'चेतावनी: आप लॉग इन कर सकते हैं इससे पहले आपके खाते को अनुमोदन की आवश्यकता है।';



//--------------------------------------------------------------------------------------------------

//For register
// Text
$_['heading_text_register']= 'खाता पंजीकृत करें';
$_['text_your_details']    = 'आपकी व्यक्तिगत जानकारी';
$_['text_your_password']   = 'आपका पासवर्ड';
$_['text_your_address']    = 'आपका पता';
$_['text_newsletter']      = 'न्यूज़लेटर';
$_['text_agree']           = 'मैंने <a href="%s" class="agree"><b>%s</b></a> को पढ़ लिया है और मैं सहमत हूं';
$_['text_account_already'] = '<h3>पहले से एक खाता है?</h3>
                                <h4><a href="%s" >आपके पास पहले से है उस लॉग इन विवरण के साथ साइन इन करें</a>.
                                </h4>
                                <p>
                                    आप अपने मौजूदा खाते से ईमेल पते और पासवर्ड का उपयोग कर सकते हैं।
                                </p>';

// Entry
$_['entry_customer_group'] = 'ग्राहक समूह';
$_['entry_name']           = 'नाम';
$_['entry_firstname']      = 'नाम';
$_['entry_lastname']       = 'सरनेम';
$_['entry_email']          = 'ईमेल';
$_['entry_email_address']  = 'ईमेल पता';
$_['entry_telephone']      = 'मोबाइल';
$_['entry_whatsapp_telephone'] = "वॉट्सएप्प नंबर";
$_['entry_fax']            = 'फैक्स';
$_['entry_company']        = 'व्यवसाय का नाम';
$_['entry_address_1']      = 'पता 1';
$_['entry_address_2']      = 'पता 2';
$_['entry_postcode']       = 'पोस्ट कोड';
$_['entry_city']           = 'शहर';
$_['entry_country']        = 'देश';
$_['entry_zone']           = 'क्षेत्र / राज्य';
$_['entry_newsletter']     = 'सब्सक्राइब करें';
$_['entry_password']       = 'पासवर्ड';
$_['entry_confirm']        = 'पासवर्ड की पुष्टि करें';
$_['entry_name']           = 'नाम';
$_['entry_referred']       = 'रेफरकर्ता';
$_['button_back']           = 'वापस';
$_['button_continue']       = 'जारी रखें';
$_['button_login']          = 'लॉग इन करें';
$_['entry_otp']             = 'Please Enter OTP';
$_['button_verify']         = 'Verify';
//$_['referred_by']       = '';


// Error
//$_['error_exists']         = 'चेतावनी: ई मेल पता पहले से ही पंजीकृत है!'.' <a href="index.php?route=account/forgotten">पासवर्ड भूल गए</a>.';
$_['error_exists']         = 'चेतावनी: ई मेल पता पहले से ही पंजीकृत है!'.' <a href="javascript:void(0);" class="forgot-redirect">पासवर्ड भूल गए</a>.';
$_['error_name']           = 'अपना नाम दें';
$_['error_firstname']      = 'नाम 1 से 32 अक्षरों के बीच होना चाहिए!';
$_['error_lastname']       = 'सरनेम 1 से 32 अक्षरों के बीच होना चाहिए!';
$_['error_email']          = 'प्रतीत होता है ई-मेल पता मान्य नहीं है!';
$_['error_telephone']      = 'मोबाइल 10 नंबर का होना चाहिए!';
$_['error_address_1']      = 'पहला पता 3 से 128 अक्षरों के बीच होना चाहिए!';
$_['error_city']           = 'शहर 2 से 128 अक्षरों के बीच होना चाहिए!';
$_['error_postcode']       = 'पोस्टकोड 2 से 10 अक्षरों के बीच होना चाहिए!';
$_['error_country']        = 'देश चुनें!';
$_['error_zone']           = 'कोई क्षेत्र / राज्य चुनें!';
$_['error_custom_field']   = '%s आवश्यक है!';
$_['error_password']       = 'पासवर्ड 4 से 20 अक्षरों के बीच होना चाहिए!';
$_['error_confirm']        = 'पासवर्ड की पुष्टि पासवर्ड से मेल नहीं खाती है!';
$_['error_agree']          = 'चेतावनी: आपको %s से सहमत होना होगा!';
//$_['error_exists_phone']   = 'चेतावनी: मोबाइल नंबर पहले से ही पंजीकृत है!'.' <a href="index.php?route=account/forgotten">पासवर्ड भूल गए</a>.';
$_['error_exists_phone']   = 'चेतावनी: मोबाइल नंबर पहले से ही पंजीकृत है!'.' <a href="javascript:void(0);" class="forgot-redirect">पासवर्ड भूल गए</a>.';



//---------------------------------------------------------------------------------------------------------------------------

//For Forget Password
// Text
$_['heading_text_forget_password']  = 'क्या आप पासवर्ड भूल गए?';
$_['text_account']                  = 'खाता';
$_['text_forgotten']                = 'क्या आप अपना पासवर्ड भूल गए हैं?';
$_['text_your_email']               = 'आपका ईमेल पता';
$_['text_email']                    = 'आपके खाते से संबंधित ई-मेल पता दर्ज करें. सबमिट पर क्लिक करें ताकि आपको पासवर्ड ई-मेल किया जा सके.';
$_['text_success']                  = 'सफल: आपके ई-मेल पते पर एक नया पासवर्ड भेज दिया गया है.';
$_['text_mobile_success']           = 'सफल: नया पासवर्ड आपके मोबाइल पर भेज दिया गया है.';

// Entry
$_['entry_email']                   = 'ईमेल पता या मोबाइल नंबर';

// Error
$_['error_email']                   = 'चेतावनी: ई-मेल एड्रेस या मोबाइल नंबर हमारे रिकॉर्ड में नहीं मिला, कृपया पुन: प्रयास करें!';