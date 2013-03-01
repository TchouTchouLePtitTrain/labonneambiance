<?php

class IndexController extends IndexControllerCore
{
	public function initContent()
	{
		parent::initContent();

		$category = new Category(3, $this->context->language->id);
		$products = $category->getProducts($this->context->language->id, 0, 5000, null, null);

		$this->context->smarty->assign(array('HOOK_HOME' => Hook::exec('displayHome'), 'products' => $products));
		$this->setTemplate(_PS_THEME_DIR_.'index.tpl');
	}
}

