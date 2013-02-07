<?php

class exportLengow extends Module
{
	public function __construct()
	{
		$this->name = 'exportLengow';
		$this->tab = 'export';
		$this->version = '1.4.7';
		$this->author = 'Lengow';
		$this->module_key = 'bcd1ee55ed2b87f765ef63114a2f58eb';

		parent::__construct();

		$this->displayName = $this->l('Lengow');
		$this->description = $this->l('Export your product catalog to the solution Lengow.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall the module Lengow?');
	}

	public function install()
	{
		if(!parent::install()
		|| !$this->registerHook('leftColumn')
		|| !Configuration::updateValue('MOD_EXPORT_LENGOW_URL', 'http://www.lengow.com/view/images/logo_transparant.png'))
		return false;
		return true;
	}

	public function uninstall()
	{
		$sql = "DROP TABLE `".constant('_DB_PREFIX_')."parametre_lengow`";
		Db::getInstance()->ExecuteS($sql);
		if(!parent::uninstall()
		|| !Configuration::deleteByName('MOD_EXPORT_LENGOW_URL'))
		return false;
		return true;
	}


	public function getContent()
	{
		$html = '';
		$exportLengow_url = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'modules/exportLengow/export.php';
		$exportFullLengow_url = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'modules/exportLengow/export.php?mode=full';
		$html .= '<h2>'.$this->l('Lengow: export your product catalog').' (v'.$this->version.')</h2>
    <fieldset>
<legend>'.$this->l('Informations  :').'</legend>'.$this->l("Lengow is a SaaS solution to enable e-shopping optimize its product catalogs to price comparison, affiliation but also marketplaces and sites of Cashback.").'<br />	
	<br />'.$this->l('The principle is that the solution recovers the merchant\'s product catalog, configure, optimize and track information campaigns to restore market for e-trading statistics in the form of dashboards and charts.').' 
	<br />'.$this->l('This process allows e-merchants to optimize their flow and their cost of acquisition on each channel.').'
	<br clear="all" />
	<br clear="all" />
	<a href="http://www.lengow.com" target="_blank"><div style="background-color:#2e85c9;text-align:center;padding:10px;border:1px solid #DDD"><img src="http://www.lengow.fr/img/slide_all_new.png" alt="'.$this->l('Lengow Solution').'" border="0" /></div></a>
	
    </fieldset>
<br clear="all" />
<fieldset>
<legend>'.$this->l('URL of your Product Catalog :').'</legend>
        <a href="'.$exportLengow_url.'" target="_blank" style="font-family:Courier">'.$exportLengow_url.'</a>
</fieldset>
<br clear="all" />
<fieldset>
<legend>'.$this->l('URL of your Product Catalog Full :').'</legend>
        <a href="'.$exportFullLengow_url.'" target="_blank" style="font-family:Courier">'.$exportFullLengow_url.'</a>
</fieldset>
<br clear="all" />
<fieldset>
<legend>'.$this->l('URL optional(s) parameter(s):').'</legend>
		<b>CUR</b>: '.$this->l('Use Prestashop currency conversion tool to convert your products prices using isocode.').'<br/><br/>
		'.$this->l('Example: convert your prices in $ (isocode: USD)').': <br/>
        <a href="'.$exportFullLengow_url.'&CUR=USD" target="_blank" style="font-family:Courier">'.$exportFullLengow_url.'&CUR=USD</a><br/>
		'.$this->l('If not set, EUR is used as default value.').'<br/><br/>
		<b>lang</b>: '.$this->l('Use Prestashop translation tool using language iso2 code to translate your products titles and descriptions.').'<br/><br/>
		'.$this->l('Example: translate in Spanish (ES)').': <br/>
        <a href="'.$exportFullLengow_url.'&lang=ES" target="_blank" style="font-family:Courier">'.$exportFullLengow_url.'&lang=ES</a><br/>
		'.$this->l('If not set below, FR is used as default value.').'<br/><br/><br/>
		'.$this->l('Both optional parameters can be used').': <br/>
		'.$this->l('Example: convert your prices in &pound; (GBP) and translate in English').': <br/>
		<a href="'.$exportFullLengow_url.'&lang=EN&CUR=GBP" target="_blank" style="font-family:Courier">'.$exportFullLengow_url.'&lang=EN&CUR=GBP</a><br/>
</fieldset>';

		return $html.$this->displayForm();
	}

	public function secureDir($dir)
	{
		define('_LENGOW_DIR_',_SEPARATOR_.'exportLengow');
		define('MSG_ALERT_MODULE_NAME',$this->l('Module Lengow should not be renamed'));

		if($dir != _LENGOW_DIR_)
		{
			echo utf8_decode(MSG_ALERT_MODULE_NAME);
			exit();
		}
	}

	public function displayForm()
	{
		$output = '';
		ob_start();
		include('formLengow.php');
		$output = ob_get_clean();
		return $output;
		ob_end_clean();
	}
}
?>
