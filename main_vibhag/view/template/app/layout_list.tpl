<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
                <?php if(!empty($layout)) { ?>
                    <div class="form-group pull-right">
                        <a class="btn btn-default" href="<?php echo $create_new_layout; ?>"><?php echo $text_create_new_layout; ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel-body">
            <?php if(empty($layout)) { ?>
                <div class="form-group col-sm-7 pull-right">
                    <a class="btn btn-default" href="<?php echo $create_new_layout; ?>"><?php echo $text_create_new_layout; ?></a>
                </div>
            <?php } else { ?>
            <div class="well">
                <div class="row">
                    <?php foreach($layout as $lay_out) { ?>
                        <div class="form-group">
                            <?php $layout_form_link = $this->url->link('app/dynamic_layout/layoutForm', 'token='.$this->session->data['token'].'&layout_index='.$lay_out['layout_index'], 'SSL'); ?>
                            <a href="<?php echo $layout_form_link; ?>">
                                <div class="col-sm-12">
                                    <span class="col-sm-3">
                                        Layout Title: <?php echo $lay_out['layout']['layout_title']; ?>
                                    </span>
                                    <span class="col-sm-3">
                                        Is Show Layout Title: <?php echo $lay_out['layout']['is_show_layout_title']; ?>
                                    </span>
                                    <span class="col-sm-3">
                                        Is Single Image: <?php echo $lay_out['layout']['is_single_image']; ?>
                                    </span>
                                    <span class="col-sm-3">
                                        Status: <?php echo $lay_out['layout']['status']; ?>
                                    </span>
                                </div>
                            </a>
                            <div class="clearfix"> </div>
                            <hr>
                        </div >
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo $footer; ?>