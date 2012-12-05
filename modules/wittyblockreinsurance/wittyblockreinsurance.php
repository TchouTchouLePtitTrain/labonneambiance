<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class WittyBlockReinsurance extends Module
{
	public function __construct()
	{
		$this->name = 'wittyblockreinsurance';
		$this->tab = 'witty_font_features';

		parent::__construct();

		$this->displayName = $this->l('Bloc garanties');
		$this->description = $this->l('Ajoute un bloc qui affiche les garanties offertes par le site pour rassurer les utilisateurs');
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
		// Check if not a mobile theme
		if ($this->context->getMobileDevice() != false)
			return false;

		$this->context->controller->addCSS($this->_path.'static/css/wittyblockreinsurance.css', 'all');

		return $this->display(__FILE__, 'wittyblockreinsurance.tpl');
	}
}
