<?php
// Text
$_['text_title']				= 'क्लर्ना चालान - 14 दिनों के भीतर भुगतान करें';
$_['text_terms_fee']			= '<span id="klarna_invoice_toc"></span> (+%s)<script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', ईआईडी: \'%s\', देश: \'%s\', शुल्क: %s});</script>';
$_['text_terms_no_fee']			= '<span id="klarna_invoice_toc"></span><script type="text/javascript">var terms = new Klarna.Terms.Invoice({el: \'klarna_invoice_toc\', ईआईडी: \'%s\', देश: \'%s\'});</script>';
$_['text_additional']			= 'आपका ऑर्डर पर प्रक्रिया करने से पहले क्लार्ना चालान को कुछ अतिरिक्त जानकारी चाहिए.';
$_['text_male']					= 'पुरुष';
$_['text_female']				= 'स्त्री';
$_['text_year']					= 'वर्ष';
$_['text_month']				= 'महीना';
$_['text_day']					= 'दिन';
$_['text_comment']				= 'क्लार्ना\' की चालान आईडी: %s. ' . "\n" . '%s/%s: %.4f';

// Entry
$_['entry_gender']				= 'लिंग';
$_['entry_pno']					= 'निजी नंबर';
$_['entry_dob']					= 'जन्म तारीख';
$_['entry_phone_no']			= 'फोन नंबर';
$_['entry_street']				= 'सड़क';
$_['entry_house_no']			= 'मकान नंबर';
$_['entry_house_ext']			= 'हाउस एक्सटेंशन';
$_['entry_company']				= 'कंपनी पंजीकरण नंबर';

// Help
$_['help_pno']					= 'कृपया यहां अपना सामाजिक सुरक्षा नंबर दर्ज करें.';
$_['help_phone_no']				= 'कृपया अपना फोन नंबर दर्ज करें.';
$_['help_street']				= 'कृपया ध्यान दें कि पंजीकृत पते पर वितरण तभी हो सकती है जब क्लार्ना से भुगतान किया जाए.';
$_['help_house_no']				= 'कृपया अपने घर का नंबर दर्ज करें.';
$_['help_house_ext']			= 'कृपया यहां अपने घर का एक्सटेंशन दें. उदाहरण के लिए ए, बी, सी, लाल, नीला आदि.';
$_['help_company']				= 'कृपया अपनी कंपनी का\'पंजीकरण नंबर दर्ज करें';

// Error
$_['error_deu_terms']			= 'आपको क्लार्ना की गोपनीयता नीति\'से सहमत होना होगा (Datenschutz)';
$_['error_address_match']		= 'अगर आपको क्लार्ना चालान का उपयोग करना चाहते हैं तो बिलिंग और शिपिंग पता मेल खाना चाहिए';
$_['error_network']				= 'क्लार्ना को जोडते समय त्रुटि हो गई. कृपया बाद में पुन: प्रयास करें.';