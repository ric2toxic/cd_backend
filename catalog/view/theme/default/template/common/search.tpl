
<div id="search1" class="input-group search_box">

  <table class="header-search-table">
    <tr>
      <td class="mahoday">
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" id="header-cat-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" value="<?php echo isset($data['category_id'])?$data['category_id']:''; ?>">
            <?php if(isset($category_name) && $category_name != ""){ ?>
                <?php echo strtoupper($category_name); ?>
            <?php } else { echo "ALL CATEGORIES"; }?>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="header-cat-dropdown">
          <?php // echo "<pre>";print_r($cats); die; ?>
            <li>
              <a href="javascript:void(0);" data-value="CATEGORIES" data-cat=" ">ALL CATEGORIES</a>
            </li>
            <?php foreach ($cats as $key => $value) { ?>     
            <li>
              <a href="javascript:void(0);" data-value="<?php echo $value['name']; ?>" data-cat="<?php echo $value['category_id']; ?>"><?php echo strtoupper($value['name']); ?></a>
            </li>
          <?php } ?>
          </ul>
        </div>
      </td>
      <td>
        <input type="text" id="mainsearch" name="search" size="100" value="<?php echo $search; ?>" placeholder="<?php echo $text_search; ?>" class="form-control search_input ui-autocomplete-input" autocomplete="off"/>
      </td>
      <td>
        <span class="input-group-btn">
          <button type="button" class="btn btn-default btn-search search-button" id="search_magnifier"><i class="fa fa-search"></i></button>
        </span>
      </td>
    </tr>
  </table>
</div>  

