<?php

class ModelLocalisationZone extends Model {

    public function getZone($zone_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int) $zone_id . "' AND status = '1'");

        return $query->row;
    }

    public function getZoneName($zone_id) {
        $query = $this->db->query("SELECT name FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int) $zone_id . "' AND status = '1'");

        return $query->row['name'];
    }

    public function getZonesByCountryId($country_id) {
        $zone_data = $this->cache->get('zone.' . (int) $country_id);

        if (!$zone_data) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int) $country_id . "' AND status = '1' ORDER BY name");

            $zone_data = $query->rows;

            $this->cache->set('zone.' . (int) $country_id, $zone_data);
        }

        return $zone_data;
    }

    public function getZoneIdByName($name) {
        $sql = "SELECT zone_id FROM " . DB_PREFIX . "zone WHERE LOWER(name) like '" . strtolower($name) . "%' AND status = '1' LIMIT 0, 1";
        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            return $query->row['zone_id'];
        } else {
            return 0;
        }
    }

    public function getAllZones() {
        $zone_data = $this->cache->get('zone.status');

        if (!$zone_data) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE status = '1' ORDER BY name");

            $zone_data = $query->rows;

            $this->cache->set('zone.status', $zone_data);
        }

        return $zone_data;
    }

    public function getGeoZoneId($zone_id) {
        $sql = "SELECT geo_zone_id
                 FROM " . DB_PREFIX . "zone_to_geo_zone
                 WHERE zone_id = '" . (int) $zone_id . "'"
        ;

        $query = $this->db->query($sql);

        if (!empty($query->row['geo_zone_id'])) {
            return $query->row['geo_zone_id'];
        } else {
            return false;
        }
    }

    /*
     * Function is use to get Zone GST code by zode_ids as string $zone_id=10,20,30;
     * nilesh,2017
     */

    public function getZoneGSTStateCode($zone_id) {
        if (empty($zone_id))
            return false;

        $sql = "SELECT zone_id, gst_state_code,code FROM " . DB_PREFIX . "zone WHERE zone_id in (" . $zone_id . ")";
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return false;
        }
    }

}
