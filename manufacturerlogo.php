<?php
/*
* 2012 - Neuber Designs
*
*  @author Neuber Designs <contato@neuberdesigns.com.br>
*  @copyright	2012 Neuber Designs
*  @version		1.2
*/
if ( !defined( '_PS_VERSION_' ) ) exit;

class ManufacturerLogo extends Module {
	const MODULE_NAME = 'manufacturerlogo';
	
	public function __construct(){
    	$this->name = self::MODULE_NAME;
   		$this->tab = 'front_office_features';
    	$this->author = 'Neuber Oliveira';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
		$this->bootstrap = true;
    	$this->need_instance = 0;

    	parent::__construct();
		
    	//$this->page = basename(__FILE__, '.php');
    	$this->displayName = $this->l( 'Manufacturer Logos' );
    	$this->description = $this->l('Display a list of manufacturers as a carroussel slider.');
    	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		if (!Configuration::get(self::MODULE_NAME))  
			$this->warning = $this->l('No name provided.'); 
		
		$this->defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
    }
    
    function install(){
		if (!parent::install())
			return false;
		
		if ( 
			!$this->registerHook('leftColumn') || 
			!$this->registerHook('rightColumn') || 
			!$this->registerHook('home') || 
			!$this->registerHook('footer') ||
			!$this->registerHook('header')
		)
			return false;
		
		Configuration::updateValue('ND_MANUIMG_TITLE', $this->l('Manufacturers') );
		Configuration::updateValue('ND_MANUIMG_TITLE_ALL', $this->l('Show All') );
		Configuration::updateValue('ND_MANUIMG_SIZE', 'original');
		Configuration::updateValue('ND_MANUIMG_SKIN', 'tango');
		Configuration::updateValue('ND_MANUIMG_ORI', 'vertical');
		Configuration::updateValue('ND_MANUIMG_POSITION', 'leftColumn');
		
		return true;
	}
	
	function uninstall(){
		if ( 
			!$this->unregisterHook('leftColumn') || 
			!$this->unregisterHook('rightColumn') || 
			!$this->unregisterHook('home') || 
			!$this->unregisterHook('footer') ||
			!$this->unregisterHook('header')
		)
			return false;
	}
	
	private function assingValues(){
		global $smarty,$link;
		//Tools::link_rewrite($manufacturers[$i]['name']
		$manu = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'manufacturer ORDER BY name ASC');
		foreach( $manu as $key=>$value) $manu[$key]['link_rewrite'] = Tools::link_rewrite($manu[$key]['name']);
		
		if( Configuration::get('ND_MANUIMG_SIZE')=='original' ) 
			$manu_size = '';
		else 
			$manu_size = Configuration::get('ND_MANUIMG_SIZE');
		
		$smarty->assign('mod_name',$this->name);
		$smarty->assign('manufacturers',$manu);
		$smarty->assign('link',$link);
		$smarty->assign('manu_title',Configuration::get('ND_MANUIMG_TITLE'));
		$smarty->assign('manu_size',$manu_size);
		$smarty->assign('all_manu_title',Configuration::get('ND_MANUIMG_ALL'));
		$smarty->assign('skin',Configuration::get('ND_MANUIMG_SKIN'));
		$smarty->assign('orientation',Configuration::get('ND_MANUIMG_ORI'));
	}
	
	public function hookRightColumn($params){
		return $this->hookLeftColumn($params);
	}
	
	public function hookLeftColumn($params){
		$this->assingValues();
		return $this->display(__FILE__,'manufacturer-ver-list.tpl');
	}
	
	public function hookHome($params){
		$this->assingValues();
		//return $this->display(__FILE__,'manufacturer-hor-list.tpl');
	}
	
	public function hookTop($params){
		$this->assingValues();
		return $this->display(__FILE__,'manufacturer-hor-list.tpl');
	}
	
	public function hookHeader($params){
		Tools::addJS($this->_path.'js/lib/jquery.jcarousel.min.js');
		Tools::addJS($this->_path.'js/interface.js');
		Tools::addCSS($this->_path.'css/manufacturer_img.css','all');
		
		Tools::addCSS($this->_path.'css/skins/'.Configuration::get('ND_MANUIMG_SKIN').'/skin.css','all');
	}
	
	public function getContent(){
		$output = null;
		if( Tools::isSubmit('submit_nd_mi') ){
			if( Validate::isGenericName(Tools::getValue('ND_MANUIMG') ) ){
				Configuration::updateValue('ND_MANUIMG_TITLE', Tools::getValue('ND_MANUIMG_TITLE') );
			}else{
				$output .= $this->displayError( $this->l('Fill the title') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ND_MANUIMG_ALL') ) ){
				Configuration::updateValue('ND_MANUIMG_ALL', Tools::getValue('ND_MANUIMG_ALL') );
			}else{
				$output .= $this->displayError( $this->l('Fill the title for link "Show All"') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ND_MANUIMG_SIZE') ) ){
				Configuration::updateValue('ND_MANUIMG_SIZE', Tools::getValue('ND_MANUIMG_SIZE') );
			}else{
				$output .= $this->displayError( $this->l('Choose a size for images') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ND_MANUIMG_SKIN') ) ){
				Configuration::updateValue('ND_MANUIMG_SKIN', Tools::getValue('ND_MANUIMG_SKIN') );
			}else{
				$output .= $this->displayError( $this->l('Choose a skin') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ND_MANUIMG_POSITION') ) ){
				Configuration::updateValue('ND_MANUIMG_POSITION', Tools::getValue('ND_MANUIMG_POSITION') );
			}else{
				$output .= $this->displayError( $this->l('Choose the position') );
			}
		}
		
		$skins = array();
		foreach(glob(dirname(__FILE__).'/css/skins/*', GLOB_ONLYDIR) as $k=>$template){
			$file_name = basename($template);
			$skins[] = array('id_option'=>strtolower($file_name), 'name'=>ucWords($file_name) );
		}
		
		$helper = new HelperForm();
		 
		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		 
		// Language
		$helper->default_form_language = $this->defaultLang;
		$helper->allow_employee_form_lang = $this->defaultLang;
		
		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit_nd_mi';
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		
		
		$sizeOptions = array(
			'id'=>'id_option',
			'name'=>'name',
			'query'=>array(
				array('id_option'=>'original', 'name'=>$this->l('Original') ),
				array('id_option'=>'large', 'name'=>$this->l('Large').' 300x300'),
				array('id_option'=>'medium', 'name'=>$this->l('Medium').' 80x80'),
				array('id_option'=>'small', 'name'=>$this->l('Small').' 45x45'),
			),
		);
		
		$skinsOptions = array(
			'id'=>'id_option',
			'name'=>'name',
			'query'=>$skins,
		);
		
		$positionOptions = array(
			'id'=>'id_option',
			'name'=>'name',
			'query'=>array(
				array(
					'id_option' => 'leftColumn',
					'name' => $this->l('Left Column'),
				),
				array(
					'id_option' =>'rightColumn',
					'name' =>$this->l('Right Column')
				),
				array(
					'id_option' =>'home',
					'name' =>$this->l('Home')
				),
				array(
					'id_option' =>'top',
					'name' =>$this->l('Top')
				),
				array(
					'id_option' =>'footer',
					'name' =>$this->l('Footer')
				),
			),
		);
		
		//var_dump($formGeneral);exit;
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Block Title'),
					'name' => 'ND_MANUIMG_TITLE',
					'required' => true,
				),
				
				array(
					'type' => 'text',
					'label' => $this->l('Title for the link "Show All"'),
					'name' => 'ND_MANUIMG_ALL',
					'required' => true,
				),
				
				array(
					'type' => 'select',
					'label' => $this->l('Image Size'),
					'name' => 'ND_MANUIMG_SIZE',
					'required' => true,
					'options'=>$sizeOptions,
				),
				
				array(
					'type' => 'select',
					'label' => $this->l('Carroussel skin'),
					'name' => 'ND_MANUIMG_SKIN',
					'required' => true,
					'options'=>$skinsOptions,
				),
				
				array(
					'type' => 'select',
					'label' => $this->l('Position'),
					'name' => 'ND_MANUIMG_POSITION',
					'required' => true,
					'options'=>$positionOptions,
				),
			),
			
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
		
		$helper->fields_value['ND_MANUIMG_TITLE'] = Configuration::get('ND_MANUIMG_TITLE');
		$helper->fields_value['ND_MANUIMG_ALL'] = Configuration::get('ND_MANUIMG_ALL');
		$helper->fields_value['ND_MANUIMG_SIZE'] = Configuration::get('ND_MANUIMG_SIZE');
		$helper->fields_value['ND_MANUIMG_SKIN'] = Configuration::get('ND_MANUIMG_SKIN');
		$helper->fields_value['ND_MANUIMG_POSITION'] = Configuration::get('ND_MANUIMG_POSITION');
		
		$output .= $helper->generateForm($fields_form);
		return $output;
	}
}

/**
	 * Connect module to a hook
	 *
	 * @param string $hook_name Hook name
	 * @param array $shop_list List of shop linked to the hook (if null, link hook to all shops)
	 * @return boolean result
	 */
#	public function registerHook($hook_name, $shop_list = null)



/**
	  * Unregister module from hook
	  *
	  * @param mixed $id_hook Hook id (can be a hook name since 1.5.0)
	  * @param array $shop_list List of shop
	  * @return boolean result
	  */
#	public function unregisterHook($hook_id, $shop_list = null)

?>
