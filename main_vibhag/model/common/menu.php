<?php
/**
* Model class to manage Khufiya vibhag left penal menus. 
* . 
* @author: MSA, 2017
*/
class ModelCommonMenu extends Model {
    
    /*
     * getAdminMenu - Get admin menu details
     * @param $menu_id integer
     * @return Array[] menu details
     * @Author MSA Nov 2017
     *  */
    public function getAdminMenu($menu_id) { 
        
        $sql = "SELECT * FROM `" . DB_PREFIX . "menu_admin` m ";
        $sql.= " WHERE m.id = '" . (int)$menu_id . "' and is_deleted='0' ";
        $query = $this->db->query($sql);
        return $query->row;
    }
    
    /*
     * getAllAdminMenus - Get admin menus for listing page
     * @return Array[] menu list
     * @Author MSA Nov 2017
     *  */
    public function getAllAdminMenu($data = array()) {
        
        $sql = "SELECT * FROM `" . DB_PREFIX . "menu_admin` WHERE is_deleted='0'";
        $sort_data = array(
                        'menu_order',
			'parent',
			'status',
			'date_added'
		);
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
        } else {
                $sql .= " ORDER BY menu_order, parent";
        }
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
        } else {
                $sql .= " ASC";
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    /*
     * getMenusForDropDownOptions - Get admin menus list
     * @return Array[] menu list
     * @Author MSA Nov 2017
     *  */
    public function getMenusForDropDownOptions() {
        
        $sql  = "SELECT * FROM `" . DB_PREFIX . "menu_admin` m ";
        $sql .= " WHERE m.status = '1' and m.is_deleted='0' ";
        $query = $this->db->query($sql);
        $list = array();
        if(!empty($query->rows)) {
           foreach($query->rows as $row){
               $list[] = array(
                   'id'     => $row['id'],
                   'title'  => $row['title'],
                   'parent' => $row['parent'],
               );
           } 
        }
        return $list;
    }
    
    /*
     * getTotalMenus - Get admin parent menus total
     * @return integer menu total
     * @Author MSA Nov 2017
     *  */
    public function getTotalMenus($data = array()) {

        $sql = "SELECT COUNT(*) AS total 
                        FROM `" . DB_PREFIX . "menu_admin`
                        WHERE parent='0' and  is_deleted='0'";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }
    
    /*
     * addMenu - Add admin menu
     * @param Array menu data list
     * @Author MSA Nov 2017
     *  */
    public function addMenu($data) {
        
        $query = "INSERT INTO `" . DB_PREFIX . "menu_admin` SET ";
        $query .= "title = '" . $this->db->escape($data['title']) . "', ";
        $query .= "parent = '" . (int)$data['parent'] . "',  ";
        $query .= "permission = '" . $this->db->escape($data['permission']) . "', ";
        $query .= "link = '" . $this->db->escape($data['link']) . "',  ";
        $query .= "icon = '" . $this->db->escape($data['icon']) . "',  ";
        $query .= "sub_menu = '" . $this->db->escape($data['sub_menu']) . "',  ";
        $query .= "status = '" . (int)$data['status'] . "',  ";
        $query .= "menu_order = '" . (int)$data['menu_order'] . "',  ";
        $query .= "date_added = NOW() ";
        $this->db->query($query);
    }
    
    /*
     * editMenu - Edit admin menu
     * @param integer menu id 
     * @param Array menu data list
     * @Author MSA Nov 2017
     *  */
    public function editMenu($id, $data) {
        
        $query = "UPDATE `" . DB_PREFIX . "menu_admin` SET ";
        $query .= "title = '" . $this->db->escape($data['title']) . "', ";
        $query .= "parent = '" . (int)$data['parent'] . "',  ";
        $query .= "permission = '" . $this->db->escape($data['permission']) . "', ";
        $query .= "link = '" . $this->db->escape($data['link']) . "',  ";
        $query .= "icon = '" . $this->db->escape($data['icon']) . "',  ";
        $query .= "sub_menu = '" . $this->db->escape($data['sub_menu']) . "',  ";
        $query .= "status = '" . (int)$data['status'] . "',  ";
        $query .= "menu_order = '" . (int)$data['menu_order'] . "'  ";
        $query .= "WHERE id = '" . (int)$id . "' ";
        $this->db->query($query);
    }
    
    /*
     * getSubMenuItemsByMenuId - check sub menu items
     * @param integer menu id 
     * @param Array menu data list
     * @Author MSA Nov 2017
     *  */
    public function getSubMenuItemsByMenuId($menu_id) {
        $sql  = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "menu_admin` ";
        $sql .= "WHERE parent = '" . (int) $menu_id . "' and is_deleted='0'";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }
    
    /*
     * deleteMenu - delete menu
     * @param integer menu id 
     * @Author MSA Nov 2017
     *  */
    public function deleteMenu($menu_id) {
        $sql = "UPDATE  " . DB_PREFIX . "menu_admin SET ";
        $sql .= "is_deleted = '1', status='0' WHERE id = '" . (int)$menu_id . "'";
        $this->db->query($sql);
    }
    
    
    /*
     * getAllLeftPenalMenus - Get menu list for left penal
     * @param Array[] user permissions list
     * @return Array[] menu list
     * @Author MSA Nov 2017
     *  */
    public function getAllLeftPenalMenus($access = array()) {
 
        //$menu_data = $this->cache->get('admin_menus'.$this->user->getId());
        
        //if($menu_data)
       // {
               $sql = "SELECT id,title,parent,permission,link,sub_menu,icon,
                            menu_order,is_deleted,status
                    FROM
                    (
                        SELECT id,title,parent,permission,link,sub_menu,icon,
                            menu_order,is_deleted,status,
                        
                        CASE 
                            WHEN 
                                  FIND_IN_SET 
                                    (
                                        SUBSTRING_INDEX(SUBSTRING_INDEX(permission, '/', 2), ',', -1),
                                        '".implode(",",$access)."'
                                    ) > 0 
                                  THEN 
                                  @idlist := CONCAT(IFNULL(@idlist,''),',',parent)

                            WHEN 
                                  FIND_IN_SET(id,@idlist) 
                                  THEN 
                                  @idlist := CONCAT(@idlist,',',parent)

                              END as checkId

                        FROM `" . DB_PREFIX . "menu_admin`

                        ORDER BY id DESC, menu_order ASC
                    ) T

                    WHERE 
                            ( 
                                checkId IS NOT NULL
                                OR
                                title = 'Dashboard'
                            )
                            AND
                            (
                                is_deleted = 0
                                AND
                                status = 1
                            )
                    
                    ORDER BY menu_order ASC, id ASC

                            ";            

                //echo $sql; die;
                
                $query = $this->db->query($sql);
                
                $menu_data = $query->rows;
                
                //$this->cache->set('admin_menus'.$this->user->getId(), $menu_data);
                
                return $menu_data;
                
           // } 
           
            //return $menu_data;
        
        }
    
    
}