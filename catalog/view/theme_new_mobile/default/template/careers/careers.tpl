<?php echo $header; ?>
            <div class="career_image">
                <img src="<?php echo $careerbanner_m; ?>" style="width: 100%;">
            </div>
<div class="container"> <?php //echo "<pre>";print_r( $all_data); echo "</pre>";?>
    <div class="row">
        <div class="col-sm-12 career-page" id="content">
            <?php if(!empty($all_data)){ ?>
            <div class="top_heading"><strong><h3><?php //echo $text_top_heading; ?></h3></strong></div>
            <div class="panel-group" id="accordion">
                <?php
                    foreach($all_data as $detail){
                    ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $detail['job_id'];?>">
                        <div class="jobtitle_icons pull-right">
                            <img src="<?php echo HTTP_SERVER.'image/career/'.$detail['job_icon']; ?>">
                        </div>
                        <h4 class="panel-title">
                                <span class="glyphicon glyphicon-plus"></span>
                                <?php echo $detail['title'];?> (<?php echo $detail['location'];?>)
                        </h4>
                        <p> <?php echo $detail['job_type'];?></p>
                        </a>
                        </div>
                    <div id="collapse<?php echo $detail['job_id'];?>" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php echo $detail['description']; ?>
                            <?php /* if(!empty($detail['email_to'])){ ?>
                            <p><?php echo $text_email_label; ?> <?php echo $detail['email_to'];?> </p>
                            <?php } */ ?>
                        </div>
                        <div class="apply-buttons">
                            <a id="apply_job<?php echo $detail['job_id'];?>" data-id="<?php echo $detail['job_id'];?>" class="btn btn-primary apply_job" href="#apply_job_form<?php echo $detail['job_id'];?>">Apply Job</a>
                        </div>
                    </div>
                </div>
                <div class="apply_job_form" id="apply_job_form<?php echo $detail['job_id'];?>">
                    <div class="">
                        <div class="col-sm-12">
                            <h3><?php echo $title_popup; ?>"<?php echo $detail['title']; ?>"</h3>
                            <form class="form-horizontal" id="apply_job_form<?php echo $detail['job_id']; ?>" data-id="<?php echo $detail['job_id']; ?>" name="apply_job_form" action="<?php echo $form_action; ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="input-name"><?php echo $text_name; ?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="your_name" value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_name; ?>" id="input-name<?php echo $detail['job_id'];?>" class="form-control input-name"/>
                                        <span id="input-name-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="input-email"><?php echo $text_email; ?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="email" name="email" value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_email; ?>" id="input-email<?php echo $detail['job_id'];?>" class="form-control input-email"/>
                                        <span id="input-email-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="input-mobile"><?php echo $text_mobile; ?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" name="mobile"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_mobile; ?>" id="input-mobile<?php echo $detail['job_id'];?>" class="form-control input-mobile"/>
                                        <span id="input-mobile-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label" for="input-current_ctc"><?php echo $text_current_ctc; ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" name="current_ctc"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_current_ctc; ?>" id="input-current_ctc<?php echo $detail['job_id'];?>" class="form-control input-current_ctc"/>
                                            <span id="input-current_ctc-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <label class="control-label" for="input-expected_ctc"><?php echo $text_expected_ctc; ?></label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" name="expected_ctc"  value="<?php //echo $searchTextValue; ?>" placeholder="<?php echo $text_expected_ctc; ?>" id="input-expected_ctc<?php echo $detail['job_id'];?>" class="form-control input-expected_ctc"/>
                                            <span id="input-expected_ctc-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                        </div>
                                    </div>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="input-cover-letter"><?php echo $text_cover_letter; ?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea name="cover_letter" placeholder="<?php echo $text_cover_letter; ?>" id="input-cover-letter<?php echo $detail['job_id'];?>" class="form-control input-cover-letter" /><?php //echo $searchTextValue; ?></textarea>
                                        <span id="input-cover-letter-error<?php echo $detail['job_id'];?>" class=" error"></span>
                                    </div>
                                </div>
                                <div class="form-group uploading_resume" id="uploading_resume<?php echo $detail['job_id'];?>">
                                    <div class="col-sm-3">
                                        <label class="control-label" for="input-resume"><?php echo $text_resume; ?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" name="resume" data-value="<?php echo $detail['job_id'];?>" id="input-resume<?php echo $detail['job_id'];?>" class="input-resume"/>
                                        <!--<div class="uploaded_file"></div>-->
                                        <!--
                                        <div class="uploading_button">
                                            <a href="javascript:$('#input-resume<?php echo $detail['job_id'];?>').uploadify('upload','*');" class="btn btn-success upload-button">Upload</a>
                                        </div>
                                        -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <div class="upload-message" id="upload-message<?php echo $detail['job_id'];?>"></div>
                                        <span id="input-resume-error<?php echo $detail['job_id'];?>" class=" error"><?php //echo $error_file_extension; ?></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="job_id" value="<?php echo $detail['job_id']; ?>" class="btn btn-primary"/>
                                        <input type="hidden" name="member_email" value="<?php echo $detail['email_to']; ?>" class="btn btn-primary">
                                        <input type="hidden" name="uploading_resume" class="btn btn-primary uploading_data" id="uploading_data<?php echo $detail['job_id'];?>">
                                        <input type="submit"  value="Send" id="job_submit<?php echo $detail['job_id']; ?>" data-id="<?php echo $detail['job_id']; ?>" class="job_submit btn btn-primary"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php }?>
            </div>
            <?php }else{ ?>
            <div class="empty_heading"><strong><h3><?php echo $text_empty; ?></h3></strong></div>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.collapse').on('shown.bs.collapse', function(){
        $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    }).on('hidden.bs.collapse', function(){
        $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });

    $(document).ready(function(){

        function containsAny(str, substrings){
            for (var i = 0; i != substrings.length; i++) {
                var substring = substrings[i];
                if (str.indexOf(substring) != - 1) {
                    return substring;
                }
            }
            return null;
        }
        function phonenumber(inputtxt){
            var flag = 1;
            var phoneno = /^\d{10}$/;
            var mobile = inputtxt;
            var res = mobile.charAt(0);
            var result = containsAny(res, ["9", "8", "7"]);

            var flc = (mobile.match(new RegExp(res, "g")) || []).length;
            if(flc > 9){
                flag = 0;
            }
            if(!result){
                flag = 0;
            }
            if(flag == '1'){
                if(inputtxt.match(phoneno))
                {
                    return true;
                }
                else
                {
//                $("div.removePopError").remove();
//                $("div.apply_job_form form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
                    $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                    return false;
                }
            }
            else
            {
//            $("div.removePopError").remove();
//            $("div.apply_job_form form").append("<div class='removePopError' style='color:#f03140;'>Please Enter Valid Mobile Number</div>");
                $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                return false;
            }
        }


        var values = '';
        var name = ''; var email = '';
        var mobile= ''; var cover_letter = '';
        var resume = ''; var ext = '';
        var hidden_file = '';

        $(".apply_job").on('click',function(e){
            values = $(this).attr('data-id');
            e.preventDefault();
            $.fancybox({
                'href'          : '#apply_job_form'+values,
                'titleShow': false,
                'transitionIn': 'elastic',
                'transitionOut': 'elastic',
                'minWidth':'280',
                'hideOnContentClick': false
            });
        });


        $('.job_submit').click(function(){
            values = $(this).attr('data-id');
            name = $('#input-name'+values).val();
            email = $('#input-email'+values).val();
            mobile = $('#input-mobile'+values).val();
            current_ctc =$('#input-current_ctc'+values).val();
            expected_ctc =$('#input-expected_ctc'+values).val();
            cover_letter = $('#input-cover-letter'+values).val();
            resume = $('#input-resume'+values).val();
            //ext = $('#input-resume'+values).val().split('.').pop().toLowerCase();
            hidden_file = $('#hidden-files-name').val(resume);
            //console.log( $( this ).serialize() );

            flag = 0;
            if(name==''){
                $('#input-name-error'+values).text('Please enter your name !');
                flag = 1;
            }else{
                $('#input-name-error'+values).remove();
            }
            if(email==''){
                $('#input-email-error'+values).text('Please enter your email id !');
                flag = 1;
            }else{
                $('#input-email-error'+values).remove();
            }
            if(mobile==''){
                $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                flag = 1;
            }else{

                phone_value = phonenumber(mobile);
                if(phone_value == true){
                    $('#input-mobile-error'+values).remove();
                }else{
                    $('#input-mobile-error'+values).text('Please enter your mobile Number !');
                    flag = 1;
                }
            }
            if(current_ctc==''){
                $('#input-current_ctc-error'+values).text('Please enter your Current CTC !');
                flag = 1;
            }else{
                $('#input-current_ctc-error'+values).remove();
            }
            if(expected_ctc==''){
                $('#input-expected_ctc-error'+values).text('Please enter your Expected CTC !');
                flag = 1;
            }else{
                $('#input-expected_ctc-error'+values).remove();
            }
            if(cover_letter==''){
                $('#input-cover-letter-error'+values).text('Please enter cover letter !');
                flag = 1;
            }else{
                $('#input-cover-letter-error'+values).remove();
            }

            if(resume==''){
                $('#input-resume-error'+values).text('Please select resume !');
                flag = 1;
            }else{
                if(!/(\.txt|\.pdf|\.rtf|\.doc)$/i.test(resume))
                {
                    $('#input-resume-error'+values).text('Please select resume in doc, pdf, txt and rtf !');
                    flag = 1;
                }else{
                    $('#input-resume-error'+values).remove();
                }
            }

            if(flag) {
                return false;
            }

        });

    });
</script>
<!--
<script type="text/javascript">

    $(function() {
        var upload_value = '';
        $('.apply_job').click(function(){
            upload_value = $(this).attr('data-id');
            $('#input-resume'+upload_value).uploadify({
                'auto'          : false,
                'fileSizeLimit' : '10240KB',
                'method'        : 'post',
                'fileTypeExts'  : '*.doc; *.pdf; *.rtf; *.txt;',
                'swf'           : 'catalog/view/javascript/jquery/uploadify.swf',
                'uploader'      : 'index.php?route=careers/careers/uploadResumeBeforeSubmit',
                'onCancel' : function(file, data, response) {
                    $('#upload-message'+upload_value).text('Your Uploading File Cancel.').fadeOut(2000);
                },
                'onUploadSuccess' : function(file,data,response) {
                    //alert(data);
                    $('#upload-message'+upload_value).text(file.name +' Upload Successfully !.');
                    $('#uploading_data'+upload_value).val(file.name);
                    $('#uploading_resume'+upload_value).remove();
                    $('#input-resume-error'+upload_value).remove();
                },
            });
        });
    });

</script>
-->
<?php echo $footer; ?>

<script type="text/javascript">
  <?php /*if (isset($international_store)) { ?>

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