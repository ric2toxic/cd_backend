<?php echo $header; ?>
<title>Citrus Checkout</title>
<link rel="stylesheet" type="text/css" href="https://www.citruspay.com/resources/pg/css/perf/web_default.css">

<script src="http://code.jquery.com/jquery-1.11.1.min.js"> </script>
<script  src="catalog/view/javascript/citrus.js" type="text/javascript"></script>
<script src="https://icp.citruspay.com/js/jquery.payment.min.js"> </script>
<!--[if IE 8]>
<script type="text/javascript" src="/resources/pg/js/perf/nitro_IE8.js"></script>
<![endif]-->
<script type='text/javascript'>
function getSslPageBaseURL(){
var baseURL= "https://www.citruspay.com";
return baseURL;
}
</script>


<div class="DPLContainer" id="DPLoader">
<div class="DPLoaderBody" id="ForgotPsdPopUp">
<img class="DPLoaderImage" id="DPLoaderImage" src="/resources/pg/images/loader.gif" />
</div>
</div>
<div class="transperentContainer" id="transDiv">
</div>
<div class="sslMainBodyContainer">
<div class="merchantBanner">
<div class="merchantBannerImage">
<img title="" src="/resources/upload/5m338cj8k1_MTT001_logo.gif" onerror="imgError(this);"/>
<!--<img title="" src="/resources/pg/images/rechargeitnowBanner.png" /> -->
</div>
</div>

<div class="ForgotPsdBody" id="ForgotPsdBody">
<div class="ForgotPsdPopUp" id="ForgotPsdPopUp">
<img class="FgtPassclosePopUp" id="fgtPassClose" src="/resources/pg/images/crossIcon.png" />
<div class="forgotPassContent" id="forgotPassContent">
<h1>Forgot Your Password ?</h1>
<p> Please Enter Your Registered Email Address </p>
<div style="text-align:center;">
<input class="ForgotEmailInput" id="ForgotEmailInput" type="text" placeholder="Email">
</div>
<div class="forgotPswbtn" >
<p id="fgtPassErrMsg" class="fgtPassErrMsg">please verify your email address</p>
<button id="fgtPasswordMail">
<span>Continue</span>
</button>
</div>
</div>
<div class="cnfFrgtPass" id="cnfFrgtPass">
<p class="ForgotPsdNoti">Your password has been sent to your Email address </p>
</div>
</div>
</div>
<div class="sslMainContainer">
	<div class='header'>
		<div>
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-2">
							<span style="color:black">* </span>Name
						</div>
						<div class="col-sm-5"><input type="text" id="citrusFirstName" placeholder="First Name" value='<?php echo $first_name;?>' mandatory="y" class="form-control"></div>
						<div class="col-sm-5"><input type="text" id="citrusLastName" placeholder="Last Name" value='<?php echo $last_name;?>' mandatory="y" class="form-control"></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-2">
							<span style="color:black">* </span>Name
						</div>
						<div class="col-sm-5"><input type="text" id="citrusFirstName" placeholder="First Name" value='<?php echo $first_name;?>' mandatory="y" class="form-control"></div>
						<div class="col-sm-5"><input type="text" id="citrusLastName" placeholder="Last Name" value='<?php echo $last_name;?>' mandatory="y" class="form-control"></div>
					</div>
					<div class="col-sm-12">2</div>
					<div class="col-sm-12">3</div>
					<div class="col-sm-12">4</div>
					<div class="col-sm-12">5</div>
				</div>
			</div>
			
			
			
			<table>
				<tr style="border-collapse:separate; border-spacing:5em;">
					<td rowspan="1" class="paramName">
						<span style="color:black">* </span>Name
					</td>
					<td><input type="text" id="citrusFirstName" placeholder="First Name" value='<?php echo $first_name;?>' mandatory="y" style="margin: -5px;" class="form-control"></td>
					<td><input type="text" id="citrusLastName" placeholder="Last Name" value='<?php echo $last_name;?>' mandatory="y" class="form-control"></td>
				</tr>
				
				<tr>
					<td class="paramName">
						<span style="color:black">* </span>Email
					</td>
					<td colspan="2"><input type="text" id="citrusEmail" placeholder="Email Id" value='<?php echo $email;?>' mandatory="y" style="margin: -5px;" class="form-control"></td>
				</tr>
				
				<tr>
					<td rowspan="3" class="paramName">
						<span style="color:black">* </span>Address
					</td>
					<td><input type="text" id="citrusStreet1" placeholder="Address Street1" value='<?php echo $address1;?>' mandatory="y" style="margin: -5px;" class="form-control"></td>
					<td><input type="text" id="citrusStreet2" placeholder="Address Street2" value='<?php echo $address2;?>' mandatory="N"  class="form-control"></td>
				</tr>
				<tr>
					<td><input type="text" id="citrusCity" placeholder="City" value='<?php echo $city;?>' mandatory="y" style="margin: -5px;" class="form-control"></td>
					<td><input type="text" id="citrusState" placeholder="State" value='<?php echo $state;?>' mandatory="y" class="form-control"></td>
				</tr>
				<tr>
					<td>
						<input type="text" id="citrusCountry" placeholder="State" value='<?php echo $country;?>' mandatory="y"  class="form-control">
					</td>
					<td><input type="text" id="citrusZip" placeholder="ZipCode" maxlength="10" value='<?php echo $pin_code;?>' mandatory="y" class="form-control"></td>
				</tr>
				<tr>
					<td class="paramName">
						<span style="color:black">* </span>Mobile
					</td>
					<td colspan="2"><input type="text" id="citrusMobile" placeholder="Mobile No." maxlength="10" value='<?php echo $mobile;?>' mandatory="y" style="margin: -5px;" class="form-control"></td>
				</tr>
			</table>
		</div>
	</div>
	<input type="text" class="form-control" readonly id="citrusAmount" value="<?php echo $order_amount; ?>" />
	<input type="text" class="form-control" readonly id="citrusMerchantTxnId" value="<?php echo $txnID; ?>" />
	<input type="hidden" readonly id="citrusSignature" value="<?php echo $securitySignature; ?>" />
	<input type="hidden" readonly id="citrusReturnUrl" value="<?php echo $returnURL; ?>" />
	<input type="hidden" readonly id="citrusNotifyUrl" value="<?php echo $notifyUrl; ?>" />

 

	<div class="tab">
		<ul>
			<li>
				
				<input type="radio" name="paymentMode" id="CREDIT_CARD" checked /><label for="CREDIT_CARD">Credit/Debit<br>Card</label>
				<div>
					<div class="creditCradSection">
				
						 <select id="citrusCardType" class="form-control">
							<option selected="selected" value="credit">Credit</option>
							<option value="debit">Debit</option>
						</select>
						<select id="citrusScheme" class="form-control">
							<option selected="selected" value="VISA">VISA</option>
							<option value="mastercard">MASTER</option>
						</select>
						<!--<input type="text" id="citrusNumber" value=""/>-->
						<div>
							<input placeholder="Card Number" type="text" id="citrusNumber" name="citrusNumber" autocomplete="off" class="FailureMsgHide form-control" value=""/>
							<!-- Below one fields are just to avoid autofill in browser -->
							<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
						</div>
						<!--<input type="text" id="citrusCardHolder" value=""/>-->
						<div class="Namecard_div">
							<input placeholder="Name on Card" type="text" id="citrusCardHolder" autocomplete="off" class="nameOnCard FailureMsgHide form-control" onkeypress="return onlyAlphabets(event,this);" value=""/>
							<!-- Below one fields are just to avoid autofill in browser -->
							<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
						</div>
						<!--<input type="text" id="citrusExpiry" value=""/>-->
						<div class="expCvvDiv">
							<input placeholder="Expiry Date" type="text" id="citrusExpiry" autocomplete="off" class="nameOnCard FailureMsgHide form-control" value=""/>
							<!-- Below one fields are just to avoid autofill in browser -->
							<input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
						</div>
						<!--<input type="text" id="citrusCvv" value=""/>-->
						<input placeholder="CVV" type="password" id="citrusCvv" name="citrusCvv" class="FailureMsgHide form-control" autocomplete="off" value=""/>
						<input type="button" value="Pay Now" id="citrusCardPayButton" class="form-control"/>		
					</div>
				</div>
			
			</li>
			<li>
				
				    
						
				<input type="radio" name="paymentMode" id="DEBIT_CARD" /><label for="DEBIT_CARD">Net Banking</label>
				<div>
					<div class="debitCradSection">
					<select id="citrusAvailableOptions">
					</select>
					<input type="button" value="Pay by netbanking" id="citrusNetbankingButton" class="form-control" />	
					</div>
				 </div>
			</li>
		</ul>
	</div>
</div>
<!--
<div class="footer">
-->
</div>
</div>
</div>

 <script type="text/javascript">
	 
	CitrusPay.Merchant.Config = {
        // Merchant details
        Merchant: {
            accessKey: 'B5XEE12BI2G85CIXBX3Q', //Replace with your access key
            vanityUrl: 'gazt5rwaqh'  //Replace with your vanity URL
        }
    };	 
    
       fetchPaymentOptions();

    function handleCitrusPaymentOptions(citrusPaymentOptions) {
        if (citrusPaymentOptions.netBanking != null)
            for (i = 0; i < citrusPaymentOptions.netBanking.length; i++) {
                var obj = document.getElementById("citrusAvailableOptions");
                var option = document.createElement("option");
                option.text = citrusPaymentOptions.netBanking[i].bankName;
                option.value = citrusPaymentOptions.netBanking[i].issuerCode;
                obj.add(option);
            }
    }
     function citrusServerErrorMsg(errorResponse) {
        alert(errorResponse);
        console.log(errorResponse);
    }
    function citrusClientErrMsg(errorResponse) {
        alert(errorResponse);
        console.log(errorResponse);
    }
    
      //Net Banking
        $('#citrusNetbankingButton').on("click", function () { makePayment("netbanking") });
        //Card Payment
        $("#citrusCardPayButton").on("click", function () { makePayment("card") });
        
    </script>
<?php exit;?>
