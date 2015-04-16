<?php
/*
* 2012 - Neuber Designs
*
*  @author Neuber Designs <contato@neuberdesigns.com.br>
*  @copyright	2012 Neuber Designs
*  @version		1.2
*/
if ( !defined( '_PS_VERSION_' ) ) exit;

class NdManufactureImgs extends Module {
	public function __construct(){
    	$this->name = 'ndmanufactureimgs';
   		$this->tab = 'front_office_features';
    	$this->version = 1.0;
    	$this->author = 'Neuber Designs';
    	$this->need_instance = 0;

    	parent::__construct();
		
    	$this->page = basename(__FILE__, '.php');
    	$this->displayName = $this->l( 'Fabricantes por Imagem' );
    	$this->description = $this->l('[neuber designs] Exibe a lista de fabricantes com imagens dos fabricantes');
    }
    
    function install(){
		if (!parent::install())
			return false;
		
		if ( !$this->registerHook('leftColumn') || !$this->registerHook('header') || !$this->registerHook('home') )
			return false;
		
		Configuration::updateValue('ND_MANUIMG_TITLE', 'Fabricantes');
		Configuration::updateValue('ND_MANUIMG_SIZE', 'original');
		Configuration::updateValue('ND_MANUIMG_SKIN', 'mm');
		Configuration::updateValue('ND_MANUIMG_ORI', 'vertical');
		return true;
	}
	
	private function assingValues(){
		global $smarty,$link;
		//Tools::link_rewrite($manufacturers[$i]['name']
		$manu = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'manufacturer ORDER BY name ASC');
		foreach( $manu as $key=>$value) $manu[$key]['link_rewrite'] = Tools::link_rewrite($manu[$key]['name']);
		
		if( Configuration::get('ND_MANUIMG_SIZE')=='original' ) $manu_size = '';
		else $manu_size = Configuration::get('ND_MANUIMG_SIZE');
		
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
	
	public function hookHeader($params){
		Tools::addJS($this->_path.'js/lib/jquery.jcarousel.min.js');
		Tools::addJS($this->_path.'js/interface.js');
		Tools::addCSS($this->_path.'css/manufacturer_img.css','all');
		
		Tools::addCSS($this->_path.'css/skins/'.Configuration::get('ND_MANUIMG_SKIN').'/skin.css','all');
	}
	
	public function getContent(){
		$html = '';
		$title;
		$size;
		if(Tools::isSubmit('submit_nd_mi')){
			if( Validate::isGenericName(Tools::getValue('ndmi_title') ) ){
				Configuration::updateValue('ND_MANUIMG_TITLE', Tools::getValue('ndmi_title') );
				$html .= $this->displayConfirmation( $this->l('Titulo Atualizado') );
				$title = Configuration::get('ND_MANUIMG_TITLE');
			}else{
				$html .= $this->displayError( $this->l('Digite um titulo para o bloco') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ndmi_title_all') ) ){
				Configuration::updateValue('ND_MANUIMG_ALL', Tools::getValue('ndmi_title_all') );
				$html .= $this->displayConfirmation( $this->l('Link mostrar todos Atualizado') );
				$title_all = Configuration::get('ND_MANUIMG_ALL');
			}else{
				$html .= $this->displayError( $this->l('Digite um titulo o link mostrar todos') );
			}
			
			if( Validate::isGenericName(Tools::getValue('ndmi_size') ) ){
				Configuration::updateValue('ND_MANUIMG_SIZE', Tools::getValue('ndmi_size') );
				$html .= $this->displayConfirmation( $this->l('Tamanho Atualizado') );
				$size = Configuration::get('ND_MANUIMG_SIZE');
			}else{
				$html .= $this->displayError( $this->l('Escolha um tamanho para a imagem') );
			}
		}else{
			$title = Configuration::get('ND_MANUIMG_TITLE');
			$title_all = Configuration::get('ND_MANUIMG_ALL');
			$size = Configuration::get('ND_MANUIMG_SIZE');
		}
		
		$html .= '
			<h2>Fabricantes por Imagens [neuber designs]</h2>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>
					<legend>'.$this->l('Configurações').'</legend>
					
					<label>'.$this->l('Titulo do bloco').'</label>
					<div class="margin-form">
						<input type="text" name="ndmi_title" size="20" value="'.$title.'" />
					</div>
					
					<label>'.$this->l('Titulo para o link mostrar todos').'</label>
					<div class="margin-form">
						<input type="text" name="ndmi_title_all" size="20" value="'.$title_all.'" />
					</div>
					
					<label>'.$this->l('Tamanho da imagem').'</label>
					<div class="margin-form">
						<select name="ndmi_size">
							<option value="original" >Original</option>
							<option value="-large" >Grande 300x300</option>
							<option value="-medium" >Media 80x80</option>
							<option value="-small" >Pequena 45x45</option>
						</select>
					</div>
					
					<label>'.$this->l('Template do Slider').'</label>
					<div class="margin-form">
						<select name="ndmi_size">';
						foreach(glob(dirname(__FILE__).'/css/skins/*', GLOB_ONLYDIR) as $k=>$template){
							$file_name = basename($template);
							$html .= '<option value="'.strtolower($file_name).'" >'.ucWords($file_name).'</option>';
						}
					$html .= '
						</select>
					</div>
					
					<label>'.$this->l('Posição').'</label>
					<div class="margin-form">
						<select name="ndmi_position">
							<option value="leftColumn" >Coluna Esquerda</option>
							<option value="rightColumn" >Coluna Direita</option>
							<option value="home" >Home</option>
							<option value="header" >Topo</option>
							<option value="footer" >Rodape</option>
						</select>
					</div>
					
					<div class="clear center">
						<p>&nbsp;</p>
						<input class="button" type="submit" name="submit_nd_mi" value="'.$this->l('   Salvar   ').'" />
					</div>
				</fieldset>
			</form>';
			return $html;
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
