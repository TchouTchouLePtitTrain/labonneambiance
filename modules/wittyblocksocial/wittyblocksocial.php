<?php

if (!defined('_PS_VERSION_'))
	exit;

class WittyBlockSocial extends Module
{
	public function __construct()
	{
		$this->name = 'wittyblocksocial';
		$this->tab = 'witty_font_features';
		$this->version = '1';
		$this->author = 'Thomas Chambon';

		parent::__construct();

		$this->displayName = $this->l('Bloc social');
		$this->description = $this->l('Ajoute un bloc qui affiche les liens sociaux');
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
		$this->context->controller->addCSS($this->_path.'static/css/wittyblocksocial.css', 'all');

		return $this->display(__FILE__, 'wittyblocksocial.tpl');
	}
}
