<?php

class ControllerTestTest extends Controller {
	
		public function getDataForNeoGrowth() {
			$this->load->model('test/data');
			$telephone = $this->request->get['mobile'];
			
			if (empty($telephone)) {
					echo "Mobile number is missing";
					die;
			}
			
			$this->model_test_data->getDataForNeoGrowth($telephone);
		}
}
