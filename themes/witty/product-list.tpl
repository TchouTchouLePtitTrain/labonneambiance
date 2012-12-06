{if isset($products)}
	<!-- Products list -->
	<section class="liste">
		{foreach from=$products item=product name=products}
			<article class="jeu">
				<a href="{$product.link|escape:'htmlall':'UTF-8'}">
					<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
				</a>
				
				<div class="infos">
					<a href="{$product.link|escape:'htmlall':'UTF-8'}">
						<h2 class="titre">{$product.name|escape:'htmlall':'UTF-8'}</h2>
					</a>
					<ul class="caracteristiques">
						{foreach from=$product.features item=feature}
							{if isset($feature.value) and ( ($feature.name == 'Age') or ($feature.name == 'Nb. de joueurs') or ($feature.name == 'Durée') )}
								<li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> : {$feature.value|escape:'htmlall':'UTF-8'}</li>
							{/if}
						{/foreach}
					</ul>
					<p class="texte">{$product.description_short|strip_tags:'UTF-8'|truncate:85:'...'}</p>
				</div>
				<a class="voirFiche" href="{$product.link|escape:'htmlall':'UTF-8'}">&#8658; Voir la fiche</a>
				<p class="prix">{convertPrice price=$product.price}</p>
			</article>
			{if ($smarty.foreach.products.index + 1) % 3 == 0}
				<div class="spacer"></div>
			{/if}
		{/foreach}
	</section>
	<!-- /Products list -->
{/if}
<div class="spacer"></div>