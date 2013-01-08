<style>
#menutop{
	display:block;
	height:50px;
	width:100%;
	background-color:#fff;
	margin-left:auto;
	margin-right:auto;
	border-bottom: solid 4px #313130;
	margin-top:10px;
}

#menutop li{
	display:inline-block;
	height:100%;
	margin-left:auto;
	margin-right:auto;
}

#menutop li a{
	font-size:15px;
	color:#999;
	display:block;
	margin-top:15px;
	margin-right:30px;
	margin-left:10px;
}

#menutop li a:link{
	color:#313130;
}

#menutop li a:visited{
	color:#313130;
}

#menutop li a:active{
	color:#D8331D;
}

#menutop li a:hover{
	color:#D8331D;
}

</style>

<div class="spacer"></div>

<nav>
	<ul id="menutop">
		<li><a href="http://www.labonneambiance.com">Accueil</a></li> {* Accueil *}
		<li><a href="{$link->getCategoryLink(3)}">Les jeux</a></li> {* Catégorie des jeux *}
		<li><a href="{$link->getCategoryLink(5)}">Les packs</a></li> {* Catégorie des packs *}
	</ul>
</nav>
