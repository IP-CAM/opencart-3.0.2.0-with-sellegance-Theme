<?php
class ControllerModuleCategoryAccordion extends Controller {
	protected $category_id = 0;
	protected $parent_id = 0;
	protected $path = array();
	
	/*------------------------------------------------------------------------------*/

	public function index() {
		$this->load->language('module/category_accordion');
			$data['heading_title'] = $this->language->get('heading_title');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['path'])) {
			$this->path = explode('_', $this->request->get['path']);
			
			$this->category_id = end($this->path);
		}
		
		$data['category_accordion'] = $this->loadCategories(0, '', $this->category_id);
		$data['category_accordion_cid'] = $this->category_id;
		$data['category_accordion_jquery_path'] = $this->config->get('config_url') . '/catalog/view/javascript/jquery';

		return $this->load->view('module/category_accordion', $data);


	}
	
	/*------------------------------------------------------------------------------*/

	protected function loadCategories($parent_id, $current_path = '', $cid = '') {
		$category_id = array_shift($this->path);
		$this->getCategoriesFromdb($category_id);
		
		$results = $this->model_catalog_category->getCategories($parent_id);
		
		$ret_string = '';
		if ($results) { 
			if ($parent_id == 0) {
				$ret_string .= '<ul id="cat_accordion">'; 
			} else {
				$ret_string .= '<ul>'; 
			}
		}
		
		foreach ($results as $result) {	
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			if ($cid == $result['category_id']) {
				$classactive = 'active';
			} else {
				$classactive = '';
			}
			
			$ret_string .= '<li class="cid' . $result['category_id'] . ' '.$classactive.'">';
			
			$children = '';
			$children = $this->loadCategories($result['category_id'], $new_path);
			
			
			$filter_data = array('filter_category_id' => $result['category_id'], 'filter_sub_category' => true);
			
			if(isset($result['linkto']) && $result['linkto'] != '') {
				if ($result['sort_order'] >= 0 && $result['sort_order'] <= 1000) {
					if ($children) { 
						$ret_string .= '<a class="havechild ' . $classactive . '" href="' . $result['linkto'] . '">' . $result['name'] . ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')</a> <span class="expand"></span>';
					} else { 
						$ret_string .= '<a class="nochild ' . $classactive . '" href="' . $result['linkto'] . '">' . $result['name'] . ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')</a>';
					}
				}
			} else {
				if ($children) { 
					$ret_string .= '<a class="havechild ' . $classactive . '" href="' . $this->url->link('product/category','path=' .  $new_path)  . '">' . $result['name'] . ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')</a> <span class="expand"></span>';
				} else { 
					$ret_string .= '<a class="nochild ' . $classactive . '" href="' . $this->url->link('product/category','path=' .  $new_path)  . '">' . $result['name'] . ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')</a>';
				}
			}
			
					$ret_string .= $children;
					$ret_string .= '</li>'; 
		}
		
		if ($results) $ret_string .= '</ul>'; 
		return $ret_string;
	}
	
	/*------------------------------------------------------------------------------*/
	
	protected function getCategoriesFromdb($category_id) {
		if($category_id <=0) return false;
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		if (isset($query->row['parent_id'])) {
			if ($query->row['parent_id'] == 0) {
				$this->parent_id = $category_id;
			} else { 
				$this->getCategoriesFromdb($query->row['parent_id']);
			}
		}
		
	}	
}
?>