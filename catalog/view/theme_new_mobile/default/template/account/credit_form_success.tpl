<!DOCTYPE html>
<style type="text/css">
    label.ellipsis{
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap; 
        overflow: hidden;
    }
</style>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="google-play-app" content="app-id=in.wholesalebox">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <!--Import Google Icon Font-->

      <!--Import materialize.css-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
     <script src="catalog/view/javascript/jquery/jquery-3.2.0.min.js" type="text/javascript"></script>

     <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style type="text/css">
        .nopadding {
            padding: 0px !important;
        }
        .top_heading
        {
            width: 100%
        }
        .top_heading p,
        .checklists ul li,
        .courier p {
            color: #636363;
        }
        
        .checklists {
            margin-top: 20px;
            float: left;
            width: 100%
        }
        
        .checklists ul {
            padding-left: 10px;
        }
        
        .checklists ul li {
            text-decoration: none;
            list-style-type: none;
            display: block;
        }
        
        .checklists ul li i {
            color: green;
        }
        
        .copy_btn img {
            ​
        }
        
        .copy_btn {
            margin-top: 20px;
            text-align: center;
        }
        
        .btn_group div button {
            background-color: #fff;
            border: 1px solid #133596;
            margin-top: 10px;
            color: #133596;
            padding: 10px;
            font-size: 15px;
        }
        
        .btn_group div button:hover {
            background-color: #133596;
            border: 1px solid #133596;
            margin-top: 10px;
            color: #fff;
            ​
        }
        .float_none{float: none;}
        .p_text1{text-align: center; font-size: 24px;}
        .p_text2{text-align: center; font-size: 20px;margin: 25px 0;}
        .p_text4{font-size: 20px;margin: 25px 0;}
        .fontawesome_style{color: #1ab41a;font-size: 70px;}
        .parent_container{padding: 20px; border: 1px solid;margin: 25px auto;}
        .back_link{text-align: center; margin: 20px auto; font-size: 16px; }
        .back_link a{color: #133596 !important;}
        .back_link{text-align: center; margin: 20px 0 0 0; font-size: 16px; }
        .kyc_form_link a{color: #133596;}
        .kyc_form_link a:hover{color: #fff;}

        .kyc_form_link a {
            background-color: #fff;
            border: 1px solid #133596;
            margin-top: 10px;
            color: #133596;
            padding: 10px;
            font-size: 15px;
            text-decoration: none;
            text-align: center;
            padding: 10px 30px;
            width: 250px;
            display: inline-block;
        }
        
        .kyc_form_link a:hover {
            background-color: #133596;
            border: 1px solid #133596;
            margin-top: 10px;
            color: #fff;
            ​
        }

        .courier, .btn_group, .kyc_form_link
        {
            float: left;
            width: 100%;
            margin: auto;
        }
        .back_button_new
            {
                width: 100%;
                text-align: center;
            }
            .kyc_form_link
            {
                margin-top: 20px;
            }



    </style>
    <title><?php echo $credit_language['heading_title']; ?></title>
</head>
<body >
    <?php //echo $approval_message; exit;?>


    <?php if(!empty($data_save) && $data_save=='1'){?>
        <div id="content">
                <div class="container parent_container">
                    <div class="wizard-container">
                        <div class="card wizard-card" data-color="orange" id="wizardProfile">
                            <div class="tab-content-popup">

                                <div id="application_form" class="tab-pane application_form  form_section">

                                    <div class="container nopadding top_heading">

                                        <div class="col-sm-12 col-xs-12">

                                            <p class="p_text1"><?php echo $credit_language['congratulation']; ?>!!</p>

                                        </div>

                                        <div class="col-sm-12 col-xs-12">

                                            <p style="text-align: center;"><i class="fas fa-check-circle fontawesome_style"></i></p>

                                        </div>

                                        <div class="col-sm-12 col-xs-12 nopadding">

                                            <p class="p_text2">
                                                <?php echo $approval_message;?>
                                            </p>

                                        </div>


                                        <div class="kyc_form_link">
                                        <div class="back_button_new nopadding class="align-middle""><a href="<?php echo $next_button;?>"><?php echo $credit_language['submit_kyc_and_bank_statement']; ?></a></div>
                                        </div>

                                        <div class="btn_group">


                                            <div class="col-xs-12 nopadding">

                                                <div class="col-xs-12 back_link"><a href="<?php echo $back_button;?>"><?php echo $credit_language['back_to_personal_detail']; ?></a></div>
                                                <div class="col-xs-12 back_link"><a href="<?php echo $home;?>"><?php echo $credit_language['back_to_website']; ?></a></div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="form-group-outer" style="text-align: center;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <?php }else if(!empty($data_save) && $data_save=='2'){?>
        <div id="content">
                <div class="container parent_container">
                    <div class="wizard-container">
                        <div class="card wizard-card" data-color="orange" id="wizardProfile">
                            <div class="tab-content-popup">

                                <div id="application_form" class="tab-pane application_form  form_section">

                                    <div class="container nopadding top_heading">

                                        <div class="col-sm-12 col-xs-12 nopadding">

                                            <p class="p_text4"><?php echo $kyc_in_progress_message;?></p>

                                        </div>

                                            <div class="col-sm-12 col-xs-12 nopadding checklists">

        <label><?php echo $credit_language['checklist_documents_title']; ?></label>

        <ul>
            <?php foreach($documents_to_be_couriered as $key=>$value){ ?>
                <li><i class="fas fa-check"></i> <?php echo $value;?></li>
            <?php } ?>

        </ul>

    </div>

    <div class="courier">

        <div class="col-sm-12 col-xs-12 nopadding">

            <label><?php echo $credit_language['kindly_courier_to']; ?> :</label>

            <p><?php echo $courier_address;?></p>

        </div>

    </div>
                                        <div class="kyc_form_link">
                                        <div class="back_button_new nopadding class="align-middle""><a href="<?php echo $next_button;?>"><?php echo $credit_language['button_back']; ?></a></div>
                                        </div>

                                        <div class="btn_group">


                                            <div class="col-xs-12 nopadding">

                                                <div class="col-xs-12 back_link"><a href="<?php echo $back_button;?>"><?php echo $credit_language['back_to_personal_detail']; ?></a></div>
                                                <div class="col-xs-12 back_link"><a href="<?php echo $home;?>"><?php echo $credit_language['back_to_website']; ?></a></div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="form-group-outer" style="text-align: center;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <?php }else if(!empty($data_save) && $data_save=='4'){?>
        <div id="content">
                <div class="container parent_container">
                    <div class="wizard-container">
                        <div class="card wizard-card" data-color="orange" id="wizardProfile">
                            <div class="tab-content-popup">

                                <div id="application_form" class="tab-pane application_form  form_section">

                                    <div class="container nopadding top_heading">

                                        <div class="col-sm-12 col-xs-12">

                                            <p class="p_text1"><?php echo $credit_language['login_first_to_complete_kyc']; ?></p>

                                        </div>

                                            <div class="col-sm-12 col-xs-12 checklists">


    </div>


                                        
<div class="kyc_form_link">
                                        <div class="back_button_new nopadding class="align-middle""><a href="<?php echo $login_btton;?>"><?php echo $credit_language['button_login']; ?></a></div>
                                        </div>
                                        <div class="btn_group">

                                            <div class="col-xs-12">

                                                

                                                <div class="col-xs-12 back_link"><a href="<?php echo $back_button;?>"><?php echo $credit_language['back_to_personal_detail']; ?></a></div>
                                                <div class="col-xs-12 back_link"><a href="<?php echo $home;?>"><?php echo $credit_language['back_to_website']; ?></a></div>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="form-group-outer" style="text-align: center;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <?php } ?>
</body>
</html>
