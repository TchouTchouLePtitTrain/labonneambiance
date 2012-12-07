<section class="meilleuresVentes">
	<a class="titreMeilleuresVentes" href="{$base_dir}category.php?id_category=3"></a>
	{foreach from=$products item=product name=myLoop}
		<article class="blocJeu">
			<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|truncate:27:'...'|escape:'htmlall':'UTF-8'}"/>
			<div class="infos">
				<h1 class="titre" href="{$product.link}">{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}</h1>
				<p class="texte">{$product.description_short|strip_tags|truncate:85:'...'}</p>
				<p class="voirFiche">&#8658; Voir la fiche</p>
				<p class="prix">{convertPrice price=$product.price}</p>
			</div>
			<div class="spacer"> </div>
			<div class="filet"></div>
	   </article>
	{/foreach}
</section>
