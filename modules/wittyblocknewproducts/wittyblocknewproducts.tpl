<section class="nouveautes">
	<div class="titreNouveautes" href="{$link->getCategoryLink(3)}" src="" alt=""> </div>
	<div class="spacer"></div>
	{foreach from=$new_products item=product}

	{if $product.pack} {* Si c'est un pack *}
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
		            	<div class="caracteristiques">
		            {foreach from=$product.packItems item=packItem}
					<p class="nom" href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}"> - {$packItem.name|escape:'htmlall':'UTF-8'}
					</p>
					{/foreach}
		                    </div>
		                     </div>
		                    {/if}
		        </div>
		    </a>

				{else} {* Si c'est un produit *}

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

	    {/if}

	{/foreach}
</section>
<div class="spacer"></div>