<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button style="display:none;" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-store').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-store">
          <div class="table-responsive">
            <table class="table table-bordered table-hover" style="display:">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"></td>
                  <td class="text-left"><?php echo $column_title; ?></td>
                  <td class="text-left"><?php echo $column_permission; ?></td>
                  <td class="text-left"><?php echo $column_link; ?></td>
                  <td class="text-left"><?php echo $column_status; ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($menus) { ?>
                <?php foreach ($menus as $key => $menu) { 
                    $menuid = $menu['id'];
                ?>
                <tr>
                <td class="text-center">
                  <?php if(isset($menu['children']) && $menu['sub_menu']) { ?>
                    <i class="fa fa-plus ischild" data-id="<?php echo $menuid?>" style=" cursor:pointer;" title="click to open sub-menu items"></i>
                  <?php } ?>
                </td>
                  <td class="text-left"><?php echo $menu['title']; ?></td>
                  <td class="text-left"><?php echo $menu['permission']; ?></td>
                  <td class="text-left"><?php echo $menu['link']; ?></td>
                  <td class="text-left"><?php echo ($menu['status']) ? 'Enabled' : 'Disabled'; ?></td>
                  <td class="text-right">
                        <a href="<?php echo $this->url->link('setting/menu/edit', 'token=' . $this->session->data['token'] . '&id=' . $menu['id'], 'SSL'); ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        <a onclick="return confirm('Are you sure, you want to delete this menu?')" href="<?php echo $this->url->link('setting/menu/delete', 'token=' . $this->session->data['token'] . '&delid=' . $menu['id'], 'SSL'); ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                 </td>
                </tr>
                    <!-- 2nd level menu items -->
                    <?php 
                    if(isset($menu['children']))
                      {
                      
                      $sub_menu = $menu['children'];
                      
                        ?>
                           <tr id="child_<?php echo $menuid; ?>" style="display:none;">
                                <td class="text-center"  colspan="6">
                                    <table class="table table-bordered">
                                      
                                    <?php 
                                        foreach($sub_menu as $sub_key => $item) { 
                                        $item_menuid = $item['id'];
                                    ?>
                                        <tr>
                                            <td class="text-center">
                                               <?php if(isset($item['children']) && $item['sub_menu']) { ?>
                                                <i class="fa fa-plus ischild" data-id="<?php echo $item_menuid?>" style=" cursor:pointer;" title="click to open sub-menu items"></i>
                                              <?php } ?>
                                            </td>
                                            <td class="text-left"><?php echo $item['title']; ?></td>
                                            <td class="text-left"><?php echo $item['permission']; ?></td>
                                            <td class="text-left"><?php echo $item['link']; ?></td>
                                            <td class="text-left"><?php echo ($item['status']) ? 'Enabled' : 'Disabled';; ?></td>
                                            <td class="text-right">
                                                <a href="<?php echo $this->url->link('setting/menu/edit', 'token=' . $this->session->data['token'] . '&id=' . $item['id'], 'SSL') ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                <a onclick="return confirm('Are you sure, you want to delete this menu?')" href="<?php echo $this->url->link('setting/menu/delete', 'token=' . $this->session->data['token'] . '&delid=' . $item['id'], 'SSL'); ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        
                                            <!-- third level menu items -->
                                                 <?php 
                                                    if(isset($item['children']))
                                                      {

                                                        $sub_item_menu = $item['children'];

                                                        ?>
                                                        
                                                            <tr id="child_<?php echo $item_menuid; ?>" style="display:none;">
                                                                <td class="text-center"  colspan="6">
                                                                    <table class="table table-bordered">
                                                                        
                                                                        <?php 
                                                                            foreach($sub_item_menu as $sub_item_key => $sub_item) { 
                                                                                $sub_item_menuid = $sub_item['id'];
                                                                         ?>
                                                                                <tr>
                                                                                    <td class="text-center">
                                                                                       <?php if(isset($sub_item['children']) && $sub_item['sub_menu']) { ?>
                                                                                        <i class="fa fa-plus ischild" data-id="<?php echo $sub_item_menuid?>" style=" cursor:pointer;" title="click to open sub-menu items"></i>
                                                                                      <?php } ?>
                                                                                    </td>
                                                                                    <td class="text-left"><?php echo $sub_item['title']; ?></td>
                                                                                    <td class="text-left"><?php echo $sub_item['permission']; ?></td>
                                                                                    <td class="text-left"><?php echo $sub_item['link']; ?></td>
                                                                                    <td class="text-left"><?php echo ($sub_item['status']) ? 'Enabled' : 'Disabled';; ?></td>
                                                                                    <td class="text-right">
                                                                                        <a href="<?php echo $this->url->link('setting/menu/edit', 'token=' . $this->session->data['token'] . '&id=' . $sub_item['id'], 'SSL') ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                                                        <a onclick="return confirm('Are you sure, you want to delete this menu?')" href="<?php echo $this->url->link('setting/menu/delete', 'token=' . $this->session->data['token'] . '&delid=' . $sub_item['id'], 'SSL'); ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                                                                    </td>
                                                                                </tr>
                                                                                
                                                                                <!-- fourth level menu items -->
                                                                                    
                                                                                    <?php 
                                                                                        if(isset($sub_item['children']))
                                                                                          {

                                                                                            $sub_item_menu = $sub_item['children'];

                                                                                            ?>
                                                                                                <tr id="child_<?php echo $sub_item_menuid; ?>" style="display:none;">
                                                                                                    <td class="text-center"  colspan="6">
                                                                                                        <table class="table table-bordered">
                                                                                                            <?php 
                                                                                                                foreach($sub_item_menu as $sub_inner_item_key => $sub_inner_item) { 
                                                                                                                   $sub_inner_item_menuid = $sub_inner_item['id'];
                                                                                                                 ?>    
                                                                                                                    <tr>
                                                                                                                        <td class="text-center">
                                                                                                                           <?php if(isset($sub_inner_item['children']) && $inner_item['sub_menu']) { ?>
                                                                                                                            <i class="fa fa-plus ischild" data-id="<?php echo sub_inner_item_menuid?>" style=" cursor:pointer;" title="click to open sub-menu items"></i>
                                                                                                                          <?php } ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-left"><?php echo $sub_inner_item['title']; ?></td>
                                                                                                                        <td class="text-left"><?php echo $sub_inner_item['permission']; ?></td>
                                                                                                                        <td class="text-left"><?php echo $sub_inner_item['link']; ?></td>
                                                                                                                        <td class="text-left"><?php echo ($sub_inner_item['status']) ? 'Enabled' : 'Disabled';; ?></td>
                                                                                                                        <td class="text-right">
                                                                                                                            <a href="<?php echo $this->url->link('setting/menu/edit', 'token=' . $this->session->data['token'] . '&id=' . $sub_inner_item['id'], 'SSL') ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                                                                                            <a onclick="return confirm('Are you sure, you want to delete this menu?')" href="<?php echo $this->url->link('setting/menu/delete', 'token=' . $this->session->data['token'] . '&delid=' . $sub_inner_item['id'], 'SSL'); ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-primary"><i class="fa fa-trash"></i></a>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                <?php } ?>
                                                                                                        </table>
                                                                                                    </td>
                                                                                                </tr>
                                                                                         <?php } ?>   
                                                                                            
                                                                                <!-- fourth level menu items -->
                                                                        
                                                                        <?php }?>
                                                                     </table>
                                                                </td>
                                                            </tr>
                                                        
                                                   <?php }  ?>     
                                                        
                                            <!-- third level menu items -->
                                            
                                            
                                    <?php }  ?>
                                       
                                    </table>
                                </td>
                           </tr>
                
                    <?php } ?>
                    
                    <!-- 2nd level menu items -->
                    
                <?php } ?>
                
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
          <div class="row">
            <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
            <div class="col-sm-6 text-right"><?php echo $results; ?></div>
          </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
<script>
    $(document).ready(function(){
    
        $('.ischild').click(function(){
        
            child_div = $(this).attr('data-id');
            
            $('#child_'+child_div).toggle();
            
            $(this).toggleClass("fa fa-plus fa fa-minus");
    
        })
        
    })
</script>