<?php

if (!defined('_PS_VERSION_'))
	exit;

class WittyBlockSiteInfos extends Module
{
	public function __construct()
	{
		$this->name = 'wittyblocksiteinfos';
		$this->tab = 'witty_font_features';

		parent::__construct();

		$this->displayName = $this->l('Bloc infos du site');
		$this->description = $this->l('Ajoute un bloc qui affiche les liens vers les infos du site (FAQ, Mentions Légales, etc ...');
	}

	public function install()
	{
		return parent::install() &&
			$this->registerHook('footer');
	}

	public function uninstall()
	{
	}

	public function hookFooter($params)
	{
		$this->context->controller->addCSS($this->_path.'static/css/wittyblocksiteinfos.css', 'all');

		return $this->display(__FILE__, 'wittyblocksiteinfos.tpl');
	}
}
