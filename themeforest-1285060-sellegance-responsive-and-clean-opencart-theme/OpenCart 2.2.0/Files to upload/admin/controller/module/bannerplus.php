<?php
class ControllerModuleBannerplus extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/bannerplus');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('bannerplus', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			if (!isset($this->request->get['module_id'])) {
				$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'] . '&type=module', true));
			} else {
				$this->response->redirect($this->url->link('module/bannerplus&submit=true&module_id=' . $this->request->get['module_id'], 'token=' . $this->session->data['token'], 'true'));
			}

		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_columns'] = $this->language->get('entry_columns');
		$data['text_col1'] = $this->language->get('text_col1');
		$data['text_col2'] = $this->language->get('text_col2');
		$data['text_col3'] = $this->language->get('text_col3');
		
		$data['entry_background'] = $this->language->get('entry_background');
		$data['entry_visibility'] = $this->language->get('entry_visibility');
		$data['entry_css'] = $this->language->get('entry_css');
		$data['entry_bgcolor'] = $this->language->get('entry_bgcolor');
		$data['entry_content'] = $this->language->get('entry_content');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_help'] = $this->language->get('entry_help');
		$data['entry_help_content'] = $this->language->get('entry_help_content');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'true')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'true')
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/bannerplus', 'token=' . $this->session->data['token'], 'true')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/bannerplus', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'true')
			);			
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('module/bannerplus', 'token=' . $this->session->data['token'], 'true');
		} else {
			$data['action'] = $this->url->link('module/bannerplus', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'true');
		}
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'true');
	
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['module_title'])) {
			$data['module_title'] = $this->request->post['module_title'];
		} elseif (!empty($module_info)) {
			$data['module_title'] = $module_info['module_title'];
		} else {
			$data['module_title'] = '';
		}
		
		if (isset($this->request->post['title_visibility'])) {
			$data['title_visibility'] = $this->request->post['title_visibility'];
		} elseif (!empty($module_info)) {
			$data['title_visibility'] = $module_info['title_visibility'];
		} else {
			$data['title_visibility'] = '';
		}
		
		if (isset($this->request->post['columns'])) {
			$data['columns'] = $this->request->post['columns'];
		} elseif (!empty($module_info)) {
			$data['columns'] = $module_info['columns'];
		} else {
			$data['columns'] = '';
		}
		
		if (isset($this->request->post['mod_id'])) {
			$data['mod_id'] = $this->request->post['mod_id'];
		/*} elseif (!empty($module_info)) {
			$data['mod_id'] = $module_info['mod_id'];*/
		} elseif (isset($this->request->get['module_id'])) {
			$data['mod_id'] = $this->request->get['module_id'];
		} else {
			$data['mod_id'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		        
        if (isset($this->request->post['sections'])) {
			$data['sections'] = $this->request->post['sections'];
		} elseif (!empty($module_info)) {
			$sections = $module_info['sections'];
		} else {
			$sections = array();
		}
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$data['sections'] = array();
		
		foreach ($sections as $section) {
			
			$data['sections'][] = array(
				'block'   => $section['block'],
				'url'   => $section['url'],
				'bgcolor'   => $section['bgcolor'],
				'visibility'   => $section['visibility'],
				'css'   => $section['css'],
				'thumb_image'   => $section['thumb_image'],
				'image' => $this->model_tool_image->resize($section['thumb_image'], 100, 100)
				
			);
		}
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/bannerplus', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/bannerplus')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}