<section class="nouveautes">
	<a class="titreNouveautes"></a>
	{foreach from=$new_products item=product}
		<article class="blocJeu">
			<a href="{$product.link}">
				<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')}" alt="{$product.legend|escape:html:'UTF-8'}"/>
				<div class="infos">
					<h1 class="titre">{$product.name|strip_tags|escape:html:'UTF-8'}</h1>
					<p class="texte">{$product.description_short|strip_tags:'UTF-8'|truncate:75:'...'}</p>
					<p class="voirFiche">&rArr; Voir la fiche</p>
				</div>
			</a>
			<div class="spacer"></div>
			<div class="filet"></div>
	   </article>
	{/foreach}
</section>