<!-- MODULE Block footer -->
<div class="spacer"></div>
<ul class="infos_footer">
	<li class="item"><a href="{$link->getPageLink($contact_url, true)}" title="{l s='Contact us' mod='blockcms'}">{l s='Contact us' mod='blockcms'}</a></li>
	{foreach from=$cmslinks item=cmslink}
		{if $cmslink.meta_title != '' and ($cmslink.meta_title == 'Mentions légales' or $cmslink.meta_title == 'FAQ') or ($cmslink.meta_title == 'Conditions d'utilisation')}
			<li class="item"><a href="{$cmslink.link|addslashes}" title="{$cmslink.meta_title|escape:'htmlall':'UTF-8'}">{$cmslink.meta_title|escape:'htmlall':'UTF-8'}</a></li>
		{/if}
	{/foreach}
	<li class="item copyright">© 2012 La bonne ambiance - Tous droits réservés</li>
</ul>
<!-- /MODULE Block footer -->
