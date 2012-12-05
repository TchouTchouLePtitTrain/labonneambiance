{if isset($products)}
	<!-- Products list -->
	{foreach from=$products item=product name=products}
        <article class="jeu">
			{if $product.id_product == 1}
				<img class="image" src="img_temp/pong_grand.png" width="124" height="124" />
			{elseif $product.id_product == 2}
				<img class="image" src="img_temp/zibi_grand.png" width="124" height="124" />
			{elseif $product.id_product == 3}
				<img class="image" src="img_temp/chronos_grand.png" width="124" height="124" />
			{elseif $product.id_product == 4}
				<img class="image" src="img_temp/ice3_grand.png" width="124" height="124" />
			{elseif $product.id_product ==5}
				<img class="image" src="img_temp/empathy_grand.png" width="124" height="124" />
			{elseif $product.id_product ==6}
				<img class="image" src="img_temp/temple_grand.png" width="124" height="124" />
			{elseif $product.id_product ==7}
				<img class="image" src="img_temp/timesup_grand.png" width="124" height="124" />
			{elseif $product.id_product ==8}
				<img class="image" src="img_temp/dobble_grand.png" width="124" height="124" />
			{elseif $product.id_product ==9}
				<img class="image" src="img_temp/packNoel_grand.png" width="124" height="124" />
			{elseif $product.id_product ==10}
				<img class="image" src="img_temp/ice3_grand.png" width="124" height="124" />
			{elseif $product.id_product ==11}
				<img class="image" src="img_temp/dobble_grand.png" width="124" height="124" />
			{elseif $product.id_product ==12}
				<img class="image" src="img_temp/timesup_grand.png" width="124" height="124" />
			{/if}
		
			{* En attendant 
				<img class="image" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
            *}
			
			<div class="infos">
                <h1 class="titre">{$product.name|escape:'htmlall':'UTF-8'}</h1>
                <p class="texte">{$product.description_short|strip_tags:'UTF-8'}</p>
             </div>
              <div class="spacer"></div> 
            <a class="voirFiche" href="{$product.link|escape:'htmlall':'UTF-8'}">&#8658; Voir la fiche</a>
            <p class="prix">{convertPrice price=$product.price}</p>
        </article>
	{/foreach}
	<!-- /Products list -->
{/if}