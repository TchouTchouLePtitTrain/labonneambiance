<style>
#menutop{
	display:block;
	height:50px;
	background-color:#fff;
	margin-left:auto;
	margin-right:auto;
	margin-top:10px;
	float: right;
	position: relative;
	z-index: 1;
}

#menutop li{
	display:inline-block;
	height:100%;
	margin-left:auto;
	margin-right:auto;
}

#menutop li a{
	font-size:16px;
	color:#999;
	display:block;
	margin-top:40px;
	margin-right:60px;
	margin-left:10px;
	font-weight: bold;
	height: 100px;
	width: 100px;
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

<nav>
	<ul id="menutop">
		<li><a href="http://www.labonneambiance.com">ACCUEIL</a></li> {* Accueil *}
		<li><a href="{$link->getCategoryLink(3)}">LES JEUX</a></li> {* Catégorie des jeux *}
		<li><a href="{$link->getCategoryLink(5)}">LES PACKS</a></li> {* Catégorie des packs *}
		<li><a href="">CATÉGORIES</a></li> {* Catégories *}
	</ul>
</nav>
