{*
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
*  @version  Release: $Revision: 6625 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{include file="$tpl_dir./errors.tpl"}
{if $errors|@count == 0}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction|floatval}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var product_specific_price = new Array();
{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
	product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
{/foreach}
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};

{if !$priceDisplay || $priceDisplay == 2}
	{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 2)}
	{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
	{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 2)}
	{assign var='productPriceWithoutRedution' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}

var productPriceWithoutRedution = '{$productPriceWithoutRedution}';
var productPrice = '{$productPrice}';

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
	{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
	customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
	customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
	{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
		combinationImages[{$combinationId}] = new Array();
		{foreach from=$combination item='image' name='f_combinationImage'}
			combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
		{/foreach}
	{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
	{foreach from=$images item='image' name='f_defaultImages'}
		combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
	{/foreach}
{/if}

// Translations
var doesntExist = '{l s='This combination does not exist for this product. Please choose another.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please wait...' js=1}';
var fieldRequired = '{l s='Please fill in all required fields, then save the customization.' js=1}';
{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
		var specific_price_combination = new Array();
		specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
		specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
		specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
		specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
		addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, '{$combination.available_date}', specific_price_combination);
	{/foreach}
{/if}

{if isset($attributesCombinations)}
	// Combinations attributes informations
	var attributesCombinations = new Array();
	{foreach from=$attributesCombinations key=id item=aC}
		tabInfos = new Array();
		tabInfos['id_attribute'] = '{$aC.id_attribute|intval}';
		tabInfos['attribute'] = '{$aC.attribute}';
		tabInfos['group'] = '{$aC.group}';
		tabInfos['id_attribute_group'] = '{$aC.id_attribute_group|intval}';
		attributesCombinations.push(tabInfos);
	{/foreach}
{/if}
//]]>
</script>

{* include file="$tpl_dir./breadcrumb.tpl" *}
<div id="primary_block" class="clearfix">

	{if isset($adminActionDisplay) && $adminActionDisplay}
		<div id="admin-action">
			<p>{l s='This product is not visible to your customers.'}
			<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad}', 0)"/>
			<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad}', 1)"/>
			</p>
			<p id="admin-action-result"></p>
			</p>
		</div>
	{/if}

	{if isset($confirmation) && $confirmation}
		<p class="confirmation">
			{$confirmation}
		</p>
	{/if}
	



<div class="fiche">

	{*
	<!-- right infos-->
	<div id="pb-right-column">
		<!-- product img-->
		<div id="image-block">
			{if $have_image}
				<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}" {if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic"/>
			{else}
				<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
			{/if}
		</div>
		*}
	
	<!-- product img-->
	<div class="galerie">

		{if $have_image}
			<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}" {if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic"/>
		{else}
			<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}" />
		{/if}
	
	
		{if isset($images) && count($images) > 0}
			<!-- thumbnails -->
			<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
			{if isset($images) && count($images) > 3}<span class="view_scroll_spacer"><a id="view_scroll_left" class="hidden" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Previous'}</a></span>{/if}
			<div id="thumbs_list">
				<ul id="thumbs_list_frame">
					{if isset($images)}
						{foreach from=$images item=image name=thumbnails}
						{assign var=imageIds value="`$product->id`-`$image.id_image`"}
						<li id="thumbnail_{$image.id_image}">
							<a href="{$link->getImageLink($product->link_rewrite, $imageIds, thickbox_default)}" rel="other-views" class="thickbox {if $smarty.foreach.thumbnails.first}shown{/if}" title="{$image.legend|htmlspecialchars}">
								<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" />
							</a>
						</li>
						{/foreach}
					{/if}
				</ul>
			</div>
			{if isset($images) && count($images) > 3}<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">{l s='Next'}</a>{/if}
		</div>
		{/if}
		{if isset($images) && count($images) > 1}<p class="resetimg clear"><span id="wrapResetImages" style="display: none;"><img src="{$img_dir}icon/cancel_11x13.gif" alt="{l s='Cancel'}" width="11" height="13"/> <a id="resetImages" href="{$link->getProductLink($product)}" onclick="$('span#wrapResetImages').hide('slow');return (false);">{l s='Display all pictures'}</a></span></p>{/if}
		<!-- usefull links-->
		<ul id="usefull_link_block">
			{if $HOOK_EXTRA_LEFT}{$HOOK_EXTRA_LEFT}{/if}
			{* <li class="print"><a href="javascript:print();">{l s='Print'}</a></li> *}
			{if $have_image && !$jqZoomEnabled}
			{/if}
		</ul>
	</div>

	<section class="ficheJeu">

		<h1 class="titre">{$product->name|escape:'htmlall':'UTF-8'}</h1>
		
		<div class="filet"></div>
		
		<article class="achat">
			{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
				<!-- add to cart form-->
				{* <form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')}" method="post"> *}
				<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')}" method="post">

					<!-- hidden datas -->
					<input type="hidden" name="token" value="{$static_token}" />
					<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
					<input type="hidden" name="add" value="1" />
					<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
					<div class="gauche">
						<p class="prix">{convertPrice price=$productPrice}</p>
				
						<!-- prices -->
						{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
						
							{if $product->online_only}
								<p class="online_only">{l s='Online only'}</p>
							{/if}
							<br/>
							{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
								{* Il y a une marge sur les p, va savoir pourquoi, du coup pour le moment on change en balise <a> <p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p> *}
								<a class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></a>
							{/if}
							<p class="stock">Stock : Disponible</p>
						{/if}
					</div>

					{* Bouton d'ajout au panier *}
					{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}
						<p class="indisponible">Ce jeu n'est pas disponible</p>
					{else}
						<p class="stock">Stock : Disponible</p>
						<input id="add_to_cart" type="submit" name="Submit" class="btn_ajouterPanier" value=""/>
					{/if}
					{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}
				</form>
			{/if}
		</article>
		<div class="spacer"></div>
		<div class="filet"></div>
		<article class="info">	
			
			{if $packItems|@count > 0} {* Si on affiche un pack *}
				<p class="texte" style="font-weight:bold;">composé de :</p>
				<p class="texte">
					{foreach from=$packItems item=packItem}
					- <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}">{$packItem.name|escape:'htmlall':'UTF-8'}</a><br/>
					{/foreach}
					</p>
					{else}
					<ul class="gauche"> <!-- Mettre ici le nombre de joueurs, l'age et la durée de jeu -->
						{foreach from=$features item=feature}
							{if (isset($feature.value) and ($feature.name == 'Nb. de joueurs' or $feature.name == 'Age' or $feature.name == 'Durée'))}
								<li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> : {$feature.value|escape:'htmlall':'UTF-8'}</li>
							{/if}
						{/foreach}
					</ul>
					<ul class="droite"> <!-- Mettre ici le theme, l'éditeur et l'auteur -->
						{foreach from=$features item=feature}
							{if (isset($feature.value) and ($feature.name == 'Editeur' or $feature.name == 'Thème' or $feature.name == 'Auteur'))}
								<li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> : {$feature.value|escape:'htmlall':'UTF-8'}</li>
							{/if}
						{/foreach}
					</ul>
			{/if}
			
			
		</article>
			
			
        <div class="spacer"></div>
        <div class="filet"></div>
        {$product->description}
	</section>
	<div class="spacer"></div>
</div>	

    <div class="spacer"> </div>
</div>

{if isset($packItems) && $packItems|@count > 0}
	<div id="blockpack">
		<div class="bandeau_titre"></div>
		<!-- Products list -->
		{foreach from=$packItems item=product}
			<a class="jeu" href="{$product.link}">
	               <img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
	            <div class="bloc">	
	            	<h2 class="titre">{$product.name|escape:'htmlall':'UTF-8'}</h2>
	            	<div class="bas">
	                	<p class="prix">{convertPrice price=$product.price}</p>
	                	<div class="ajouter" src="" alt=""> </div> 
	            	</div>
	            </div>
		        <div class="hover">
		            {if isset($product.features)}
		            <div class="infos">
		                        <ul class="caracteristiques">
		                            {foreach from=$product.features item=feature}
		                                {if isset($feature.value) and ( ($feature.name == 'Age') or ($feature.name == 'Nb. de joueurs') or ($feature.name == 'Durée') )}
		                                    <li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> : {$feature.value|escape:'htmlall':'UTF-8'}</li>
		                                {/if}
		                            {/foreach}
		                        </ul>
		                    </div>
		                    {/if} 
		        </div>
		    </a>
		    {/foreach}
		<!-- /Products list -->
	</div>
{/if}
<div class="spacer"> </div>

{/if}

