<?php echo $header; ?>
<input type="hidden" value="<?php echo $this->session->data['ctoken']; ?>" name="ctoken" id="ctoken" />
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"> <?php echo $content_top; ?>
      <h3><?php echo $text_edit_address; ?></h3>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal address">
          <input type="hidden" name="popup" value="<?php if($popup){ echo $popup; }else{ echo "";}?>" >
        <fieldset>
         <!-- <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
              <?php if ($error_firstname) { ?>
              <div class="text-danger"><?php echo $error_firstname; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
            <div class="col-sm-10">
              <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
              <?php if ($error_lastname) { ?>
              <div class="text-danger"><?php echo $error_lastname; ?></div>
              <?php } ?>
            </div>
          </div>-->
          <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo $firstname .' '. $lastname; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                    <?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
                    <?php } ?>
                </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-company"><?php echo $entry_company; ?></label>
            <div class="col-sm-10">
              <input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
            <div class="col-sm-10">
              <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control" />
              <?php if ($error_address_1) { ?>
              <div class="text-danger"><?php echo $error_address_1; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-address-2"><?php echo $entry_address_2; ?></label>
            <div class="col-sm-10">
              <input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control" />
            </div>
          </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="input-payment-telephone"><?php echo $entry_telephone; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="address_telephone" value="<?php echo $address_telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-address-payment-telephone" class="form-control" />
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" data-parent_class="address" class="form-control post_code" />
                    <?php if ($error_postcode) { ?>
                    <div class="text-danger"><?php echo $error_postcode; ?></div>
                    <?php } ?>
                </div>
            </div>


            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
                <div class="col-sm-10">
                    <select name="country_id" id="input-country" class="form-control">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php foreach ($countries as $country) { ?>
                        <?php if ($country['country_id'] == $country_id) { ?>
                        <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                    <?php if ($error_country) { ?>
                    <div class="text-danger"><?php echo $error_country; ?></div>
                    <?php } ?>
                </div>
            </div>

           <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
            <div class="col-sm-10">
              <select name="zone_id" id="input-zone" class="form-control zone">
              </select>
              <?php if ($error_zone) { ?>
              <div class="text-danger"><?php echo $error_zone; ?></div>
              <?php } ?>
            </div>
          </div>

           <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
            <div class="col-sm-10">
              <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control city" />
              <?php if ($error_city) { ?>
              <div class="text-danger"><?php echo $error_city; ?></div>
              <?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_default; ?></label>
            <div class="col-sm-10">
              <?php if ($default) { ?>
              <label class="radio-inline">
                <input type="radio" name="default" value="1" checked="checked" />
                <?php echo $text_yes; ?></label>
              <label class="radio-inline">
                <input type="radio" name="default" value="0" />
                <?php echo $text_no; ?></label>
              <?php } else { ?>
              <label class="radio-inline">
                <input type="radio" name="default" value="1" />
                <?php echo $text_yes; ?></label>
              <label class="radio-inline">
                <input type="radio" name="default" value="0" checked="checked" />
                <?php echo $text_no; ?></label>
              <?php } ?>
            </div>
          </div>
        </fieldset>
        <div class="buttons clearfix">
            <?php if($popup != true){ ?>
          <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
            <?php } ?>
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-small btn-primary"  style="font-size:inherit; padding-top:5px; height:auto;"/>
          </div>
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script type="text/javascript"><!--
// Sort the custom fields
$('.form-group[data-sort]').detach().each(function() {
	if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('.form-group').length) {
		$('.form-group').eq($(this).attr('data-sort')).before(this);
	}

	if ($(this).attr('data-sort') > $('.form-group').length) {
		$('.form-group:last').after(this);
	}

	if ($(this).attr('data-sort') < -$('.form-group').length) {
		$('.form-group:first').before(this);
	}
});
//--></script>
<script type="text/javascript"><!--
$('button[id^=\'button-custom-field\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload&ctoken=<?php echo $this->session->data["ctoken"];?>',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$(node).parent().find('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>

<script type="text/javascript">
 
       var ajax = null;
       $('[name=\'postcode\']').blur(function(e) {
        e.preventDefault();
        if($(this).val() == "") { $('[name=\'postcode\']').prev('label').animate({'margin-top': '20px'}); }
       
       if($(this).val().length == 6)
       {
         ajax = $.ajax({
                url: "<?php echo $this->url->link('common/login_popup/pincodeAddress', '', 'SSL'); ?>",
                type: 'post',
                dataType: 'json',
                data: { postcode: $('[name=\'postcode\']').val() },
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                xhrFields: { withCredentials: true },
                crossDomain: true,
                beforeSend : function(xhr)
                {
                  xhr.setRequestHeader("Cookie", "session=xxxyyyzzz");
                  if(ajax != null) { ajax.abort(); }
                },
                success: function(data)
                {
                  if(!data['error'])
                  {
                    if(data['zone_id'] == '')
                    {
                      $("[name=\'country_id\'] option[value='99'").prop('selected',true);
                      $('[name=\'city\']').val('');
                      $('[name=\'city\']').prev('label').css('margin-top', '7px');
                      get_state_list_mobile();
                    }
                    else
                    {
                      $('[name=\'city\']').prev('label').css('margin-top', '-10px');
                      $('[name=\'city\']').val(data['city']);
                      $("[name=\'country_id\'] option[value=" + data['country_id'] + "]").prop("selected",true);
                      get_state_list_mobile(data['zone_id']);
                      
                    }
                  }
                },
               complete: function(data){
                var ajax = null;
               }
         });
        }

          e.stopImmediatePropagation();
          return false;
       });


 
        $('select[name=\'country_id\']').on('change', function() {
            //country_id = $('input[name=\'country_id\']').attr('value');
           // country_id = this.value ? this.value : <?php echo $country_id ?>;
             get_state_list_mobile();
             
        });
        $('select[name=\'country_id\']').trigger('change');
  


    function  get_state_list_mobile(zone_id=0) {

          country_id = $('select[name=\'country_id\']').val();
            $.ajax({
                url: 'index.php?route=account/account/country&ctoken=<?php echo $this->session->data["ctoken"];?>&country_id=' + country_id,
                dataType: 'json',
                beforeSend: function() {
                    $('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
                },
                complete: function() {
                    $('.fa-spin').remove();
                },
                success: function(json) {
                    if (json['postcode_required'] == '1') {
                        $('input[name=\'postcode\']').parent().parent().addClass('required');
                    } else {
                        $('input[name=\'postcode\']').parent().parent().removeClass('required');
                    }

                    html = '<option value=""><?php echo $text_select; ?></option>';
                    
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                            if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
                                html += ' selected="selected"';
                            }

                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }
                    $('select[name=\'zone_id\']').html(html);

                     if(zone_id > 0) { $("select[name=\'zone_id\'] option[value=" + zone_id + "]").prop("selected",true); }

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });

       }


//--></script>
<?php echo $footer; ?>

<script type="text/javascript">
$('#input-address-payment-telephone').tagsinput({
           confirmKeys: [13, 44, 32]
        });
  <?php /* if (isset($international_store)) { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a420d52bbdfe97b137fd4ba/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php } else { ?>

var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/55faa35605ceaf627695ea99/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

<?php }*/ ?>
</script>
