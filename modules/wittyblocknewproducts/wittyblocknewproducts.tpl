<section class="nouveautes">
	<a class="titreNouveautes"></a>
	{foreach from=$new_products item=product}
		<article class="blocJeu">
			<a href="{$product.link}">
				<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|truncate:27:'...'|escape:'htmlall':'UTF-8'}"/>
				<div class="infos">
					<h1 class="titre">{$product.name|strip_tags|escape:html:'UTF-8'}</h1>
					<p class="texte">{$product.description_short|strip_tags:'UTF-8'|truncate:85:'...'}</p>
					<p class="voirFiche">&rArr; Voir la fiche</p>
				</div>
			</a>
			<div class="spacer"></div>
			<div class="filet"></div>
	   </article>
	{/foreach}
</section>