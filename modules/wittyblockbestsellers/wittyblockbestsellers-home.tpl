<section class="produitsPhares">
	<a class="titreProduitsPhares"></a>
	{foreach from=$best_sellers item=product name=myLoop}
		<article class="blocJeu">
			<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|truncate:27:'...'|escape:'htmlall':'UTF-8'}"/>
			<div class="infos">
				<h1 class="titre" href="{$product.link}">{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}</h1>
				<p class="texte">{$product.description_short|strip_tags|truncate:130:'...'}</p>
				<p class="voirFiche">&#8658; Voir la fiche</p>
			</div>
			<div class="spacer"> </div>
			<div class="filet"></div>
			<div class="spacer"> </div>
	   </article>
	{/foreach}
</section>
