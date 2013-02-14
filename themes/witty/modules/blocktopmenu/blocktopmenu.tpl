<style>
#menutop{
	border-right: 1px solid #E7E7E7;
    display: block;
    float: left;
    height: 50px;
    margin-left: 40px;
    margin-right: auto;
    margin-top: 0px;
    padding-right: 0;
    padding-top: 12px;
    width: 500px;
}

#menutop li{
	display:inline-block;
	height:100%;
	margin-left:auto;
	margin-right:auto;
}

#menutop li a{
	font-size:16px;
	color:#bebebe;
	display:block;
	margin-top:15px;
	margin-right:45px;
	margin-left:45px;
	font-weight: bold;
}

#menutop li a:link{
	color:#bebebe;
}

#menutop li a:visited{
	color:#bebebe;
}

#menutop li a:active{
	color:#313130;
}

#menutop li a:hover{
	color:#313130;
}

</style>

<nav>
	<ul id="menutop">
		<li><a href="http://www.labonneambiance.com">Accueil</a></li> {* Accueil *}
		<li><a href="{$link->getCategoryLink(3)}">Les jeux</a></li> {* Catégorie des jeux *}
		<li><a href="{$link->getCategoryLink(5)}">Les packs</a></li> {* Catégorie des packs *}
	</ul>
</nav>
