<!-- MODULE Block footer -->
<div class="spacer"></div>
<ul>
	<li class="item"><a href="{$link->getPageLink($contact_url, true)}" title="{l s='Contact us' mod='blockcms'}">{l s='Contact us' mod='blockcms'}</a></li>
	{foreach from=$cmslinks item=cmslink}
		{if $cmslink.meta_title != '' and ($cmslink.meta_title == 'FAQ' or $cmslink.meta_title == 'Mentions légales')}
			<li class="item"><a href="{$cmslink.link|addslashes}" title="{$cmslink.meta_title|escape:'htmlall':'UTF-8'}">{$cmslink.meta_title|escape:'htmlall':'UTF-8'}</a></li>
		{/if}
	{/foreach}
	<li id="header_link_sitemap"><a href="{$link->getPageLink('sitemap')}" title="{l s='sitemap' mod='blockpermanentlinks'}">{l s='Sitemap' mod='blockpermanentlinks'}</a></li>
	{if $display_poweredby}<li class="last_item">{l s='Powered by' mod='blockcms'} <a href="http://www.prestashop.com">PrestaShop</a>&trade;</li>{/if}
</ul>
<h2 class="copyright">© 2012 La bonne ambiance - Tous droits réservés</h2>
<!-- /MODULE Block footer -->
