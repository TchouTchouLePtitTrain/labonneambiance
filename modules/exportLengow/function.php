<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// ENREGISTREMENT //////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function set_Lang_id($lang_id)
{
	//insertion en BDD si il nexiste pas, update sinon
	$sql_langue_bdd = 'SELECT parametre_valeur FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="lang_id"';
	$lang_id_bdd = Db::getInstance()->getRow($sql_langue_bdd);
	$lang_id_bdd = $lang_id_bdd['parametre_valeur'];

	if($lang_id_bdd !='')
	{
		$sql_Lang_id = 'UPDATE '.constant('_DB_PREFIX_').'parametre_lengow SET parametre_valeur='.mysql_escape_string($lang_id).' WHERE parametre_nom ="lang_id"';
		Db::getInstance()->Execute($sql_Lang_id);
	}
	else
	{
		$sql_Lang_id = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("lang_id", '.mysql_escape_string($lang_id).')';
		Db::getInstance()->ExecuteS($sql_Lang_id);
	}
}

function set_Groupid_lengow($Groupid_lengow)
{
	//insertion en BDD si il nexiste pas, update sinon
	$id = get_Groupid_lengow();
	if($id !='' && $id !=0)
	{
		$sql_Groupid = 'UPDATE '.constant('_DB_PREFIX_').'parametre_lengow SET parametre_valeur='.mysql_escape_string($Groupid_lengow).' WHERE parametre_nom ="group_id"';
		Db::getInstance()->Execute($sql_Groupid);
	}
	else
	{
		$sql_Groupid = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("group_id", '.mysql_escape_string($Groupid_lengow).')';
		Db::getInstance()->ExecuteS($sql_Groupid);
	}
}

function set_Tracking_lengow($Tracking_lengow)
{
	//insertion en BDD si il nexiste pas, update sinon
	$Tracking = get_Tracking_lengow();
	if($Tracking !='')
	{
		$sql_Tracking = 'UPDATE '.constant('_DB_PREFIX_').'parametre_lengow SET parametre_valeur="'.mysql_escape_string($Tracking_lengow).'" WHERE parametre_nom ="Tracking"';
		Db::getInstance()->Execute($sql_Tracking);
	}
	else
	{
		$sql_Tracking = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("Tracking", "'.mysql_escape_string($Tracking_lengow).'")';
		Db::getInstance()->ExecuteS($sql_Tracking);
	}
}

function set_Clientid_lengow($Clientid_lengow)
{
	//insertion en BDD si il nexiste pas, update sinon
	$id = get_Clientid_lengow();
	$idGroup = get_Groupid_lengow();
	$Tracking = get_Tracking_lengow();
	
	if($id !='' && $id !=0)
	{
		$sql_Clientid = 'UPDATE '.constant('_DB_PREFIX_').'parametre_lengow SET parametre_valeur='.mysql_escape_string($Clientid_lengow).' WHERE parametre_nom ="client_id"';
		Db::getInstance()->Execute($sql_Clientid);
	}
	else
	{
		$sql_Clientid = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("client_id", '.mysql_escape_string($Clientid_lengow).')';
		Db::getInstance()->ExecuteS($sql_Clientid);
	}

	//ecriture dans le template
	$emplacement_template =_PS_THEME_DIR_."order-confirmation.tpl";
	
	//Nouveau Tag
	// echo "=>Tracking:".$Tracking."<br/>";
	if($Tracking == 'TagCapsule')
	{
		$tag_lengow = '<!-- Tag_Lengow --><script type="text/javascript">var page = \'payment\';var order_amt = \'{$total}\';var order_id = \'{$id_order}\';var product_ids = \'\';var ssl = \'false\';</script>';
		$tag_lengow .= '<script type="text/javascript" src="https://tracking.lengow.com/tagcapsule.js?lengow_id='.$Clientid_lengow.'&idGroup='.$idGroup.'"></script><!-- /Tag_Lengow -->';
	}
	elseif($Tracking == 'SimpleTag')
	{
		$tag_lengow = '<!-- Tag_Lengow -->';
		$tag_lengow .= '<img src="https://tracking.lengow.com/lead.php?idClient='.$Clientid_lengow.'&idGroup='.$idGroup.'&price={$total}&idCommande={$id_order}" alt="" border="0" />';
		$tag_lengow .= '<img src="https://tracking.lengow.com/leadValidation.php?idClient='.$Clientid_lengow.'&idGroup='.$idGroup.'&idCommande={$id_order}" alt="" border="0" />';
		$tag_lengow .= '<!-- /Tag_Lengow -->';

	}
	else
	{
		$tag_lengow = '<!-- Tag_Lengow -->';
		$tag_lengow .= '<!-- /Tag_Lengow -->';
	}

	if(!$id_template = fopen($emplacement_template, 'r'))
	{
		echo "<br /><span style='color:red;'>Erreur ouverture template, v&eacute;rifier les droits du fichier: ".$emplacement_template."</span>";
	}
	else
	{
		//recherche du tag Lengow
		$template ='';
		while (!feof($id_template))
		{ //on parcourt toutes les lignes
			$template .= fgets($id_template, 4096);
		}
		fclose($id_template);
		$pattern = '`<!-- Tag_Lengow -->(.*)<!-- /Tag_Lengow -->`Us';
		$recherche_tag_lengow = preg_match_all($pattern,$template,$resultat);

		if($resultat[0][0]!='')
		{
			//remplacement du tag
			$remplacement = $tag_lengow;
			$new_template = preg_replace($pattern, $remplacement, $template, -1, $count);

			//reouverture du template et re-écriture complete
			if(!$id_template = fopen($emplacement_template, 'w'))
			{
				echo "<br /><span style='color:red;'>Erreur ouverture template, v&eacute;rifier les droits du fichier: ".$emplacement_template."</span>";
			}
			else
			{
				fputs($id_template, "");
				fclose($id_template);
				$id_template = fopen($emplacement_template, 'r+');
				fputs($id_template, $new_template);
				fclose($id_template);
			}
		}
		else
		{
			//pose du tag
			if(!$id_template = fopen($emplacement_template, 'a+'))
			{
				echo "<br /><span style='color:red;'>Erreur ouverture template, v&eacute;rifier les droits du fichier: ".$emplacement_template."</span>";
			}
			else
			{
				fputs($id_template, $tag_lengow);
				fclose($id_template);
			}
		}

		set_TagCapsuleFooter();

	}
}

function set_TagCapsuleFooter()
{
	$idClient = get_Clientid_lengow();
	$idGroup = get_Groupid_lengow();
	$Tracking = get_Tracking_lengow();

	if($idClient !='' && $idClient >0 && $idGroup !='' && $idGroup >0)
	{
		//ecriture dans le footer
		$emplacement_footer =_PS_THEME_DIR_."footer.tpl";
		chmod($emplacement_footer, 0777);
		
		if($Tracking == 'TagCapsule')
		{
			$tag_lengow = '<!-- Tag_Lengow --><script type="text/javascript">var page = \'page\';var order_amt = \'\';var order_id = \'\';var product_ids = \'\';var ssl = \'false\';</script>';
			$tag_lengow .= '<script type="text/javascript" src="https://tracking.lengow.com/tagcapsule.js?lengow_id='.$idClient.'&idGroup='.$idGroup.'"></script><!-- /Tag_Lengow -->';
		}
		else
		{
			$tag_lengow = '<!-- Tag_Lengow --><!-- /Tag_Lengow -->';
		}

		if(!$id_footer = fopen($emplacement_footer, 'r'))
		{
			echo "<br /><span style='color:red;'>Erreur ouverture footer, v&eacute;rifier les droits du fichier: ".$emplacement_footer."</span>";
		}
		else
		{
			//recherche du tag Lengow
			$footer ='';
			while (!feof($id_footer))
			{ //on parcourt toutes les lignes
				$footer .= fgets($id_footer, 4096);
			}
			fclose($id_footer);
			$pattern = '`<!-- Tag_Lengow -->(.*)<!-- /Tag_Lengow -->`Us';
			$recherche_tag_lengow = preg_match_all($pattern,$footer,$resultat);

			if($resultat[0][0]!='')
			{
				//remplacement du tag
				$remplacement = $tag_lengow;
				$new_footer = preg_replace($pattern, $remplacement, $footer, -1, $count);
				
				//reouverture du footer et re-écriture complete
				if(!$id_footer = fopen($emplacement_footer, 'w'))
				{
					echo "<br /><span style='color:red;'>Erreur ouverture footer, v&eacute;rifier les droits du fichier: ".$emplacement_footer."</span>";
				}
				else
				{
					fputs($id_footer, "");
					fclose($id_footer);
					$id_footer = fopen($emplacement_footer, 'w');
					fputs($id_footer, $new_footer);
					fclose($id_footer);
				}
			}
			else
			{
				//pose du tag
				if(!$id_footer = fopen($emplacement_footer, 'r+'))
				{
					echo "<br /><span style='color:red;'>Erreur ouverture footer, v&eacute;rifier les droits du fichier: ".$emplacement_footer."</span>";
				}
				else
				{
					//il faut ecrire le TagCapsule avant le </body>.
					$delimiter = '</body>';
					$tab_footer = explode($delimiter, $footer);
					$new_footer = $tab_footer[0].$tag_lengow."\n".$delimiter.$tab_footer[1];
					
					fputs($id_footer, "");
					fclose($id_footer);
					$id_footer = fopen($emplacement_footer, 'r+');
					fputs($id_footer, $new_footer);
					fclose($id_footer);
				}
			}
		}
	}
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// RECUPERATION ////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function getProductFeature($id_product, $id_lang)
{
	$product_feature ='';
	$liste_colonne_feature = array();
	$sql_colonne_feature = 'SELECT fl.name, fl.id_feature FROM '.constant('_DB_PREFIX_').'feature_lang fl WHERE fl.id_lang='.mysql_escape_string($id_lang);
	$sql_colonne_feature .= ' ORDER BY fl.id_feature ASC ';
	$liste_colonne_feature = Db::getInstance()->ExecuteS($sql_colonne_feature);
	$product_feature = "";
	foreach($liste_colonne_feature as $data_colonne_feature)
	{
		$id_feature = $data_colonne_feature['id_feature'];
		$liste_value = array();
		$sql = 'SELECT fp.id_feature_value,fvl.value
		FROM '.constant('_DB_PREFIX_').'feature_product fp 
		LEFT JOIN '.constant('_DB_PREFIX_').'feature_value_lang fvl ON fp.id_feature_value = fvl.id_feature_value 
		WHERE 
		fp.id_feature='.mysql_escape_string($id_feature).' AND id_product="'.mysql_escape_string($id_product).'" 
		AND fvl.id_lang="'.mysql_escape_string($id_lang).'"
		';
		$liste_value = Db::getInstance()->ExecuteS($sql);
		if(count($liste_value)>0)
		{
			$product_feature .= '"'.trim(nl2br(netoyage_html($liste_value[0]['value']))).'"|';
		}
		else
		{
			$product_feature .= '""|';
		}
	}
	return $product_feature;
}

function getProductType($id_product)
{
	$retour = 'parent';
	
	$sql_declinaison_produit = 'SELECT pa.id_product_attribute,p.id_product ';
	$sql_declinaison_produit .='FROM '.constant('_DB_PREFIX_').'product p ';
	$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute pa ON p.id_product=pa.id_product ';
	$sql_declinaison_produit .='WHERE p.active=1 AND p.id_product="'.mysql_escape_string($id_product).'" AND pa.id_product_attribute > 0';

	$liste_declinaison =  Db::getInstance()->ExecuteS($sql_declinaison_produit);
	if(count($liste_declinaison) > 0)
		$retour = 'parent';
	else
		$retour = 'simple';
	return $retour;
}

function getProductVariation($id_declinaison, $id_lang, $liste_attribute_declinaison)
{
	$retour = '';
	if($id_declinaison > 0)
	foreach($liste_attribute_declinaison as $attribute)
	{
		$liste_attribute_produit = array();
		$sql_attribute_declinaison_produit = 'SELECT agl.name, al.name AS valeur FROM '.constant('_DB_PREFIX_').'product_attribute pa ';
		$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute';
		$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute a ON pac.id_attribute = a.id_attribute';
		$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_lang al ON al.id_attribute = pac.id_attribute';
		$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group';
		$sql_attribute_declinaison_produit .= ' WHERE al.id_lang ='.mysql_escape_string($id_lang).' AND agl.id_lang ='.mysql_escape_string($id_lang).' AND pa.id_product_attribute ='.mysql_escape_string($id_declinaison);
		$sql_attribute_declinaison_produit .= ' AND agl.id_attribute_group='.mysql_escape_string($attribute['id_attribute_group']);
		$liste_attribute_produit =  Db::getInstance()->ExecuteS($sql_attribute_declinaison_produit);
		$liste_attribute_produit = $liste_attribute_produit[0];

		if(count($liste_attribute_produit>0) && !empty($liste_attribute_produit))
		{
			$retour .= trim(nl2br($liste_attribute_produit['name'])).',';
		}
		else
		{
			$retour .= '';
		}
	}
	return $retour;
}

function getProductAttributs($id_declinaison, $id_lang, $liste_attribute_declinaison)
{
	$product_attibute = '';
	if($id_declinaison == 0)
	{
		foreach($liste_attribute_declinaison as $attribute_declinaison)
		{
			$product_attibute .= '""|';
		}
	}
	else
	{
		foreach($liste_attribute_declinaison as $attribute)
		{
			$liste_attribute_produit = array();
			$sql_attribute_declinaison_produit = 'SELECT agl.name, al.name AS valeur FROM '.constant('_DB_PREFIX_').'product_attribute pa ';
			$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute';
			$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute a ON pac.id_attribute = a.id_attribute';
			$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_lang al ON al.id_attribute = pac.id_attribute';
			$sql_attribute_declinaison_produit .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group';
			$sql_attribute_declinaison_produit .= ' WHERE al.id_lang ='.mysql_escape_string($id_lang).' AND agl.id_lang ='.mysql_escape_string($id_lang).' AND pa.id_product_attribute ='.mysql_escape_string($id_declinaison);
			$sql_attribute_declinaison_produit .= ' AND agl.id_attribute_group='.mysql_escape_string($attribute['id_attribute_group']);
			$liste_attribute_produit =  Db::getInstance()->ExecuteS($sql_attribute_declinaison_produit);
			$liste_attribute_produit = $liste_attribute_produit[0];

			if(count($liste_attribute_produit>0) && !empty($liste_attribute_produit))
			{
				$product_attibute .= '"'.trim(nl2br(netoyage_html($liste_attribute_produit['valeur']))).'"|';
			}
			else
			{
				$product_attibute .= '""|';
			}
		}
	}
	return $product_attibute;
}


function getParentCategoy($catID, $langID, $nom_cat='')
{
	if($nom_cat =='')
	{
		$nom_cat = getNomCategorie($catID, $langID);
	}
	else
	{
		$nom_cat = getNomCategorie($catID, $langID).' > '.$nom_cat;
	}
	$sql_categorie_parent .='SELECT id_parent FROM '.constant('_DB_PREFIX_').'category  WHERE id_category='.mysql_escape_string($catID);
	$data_categorie_parent = Db::getInstance()->ExecuteS($sql_categorie_parent);
	if(isset($data_categorie_parent) && ($data_categorie_parent[0]['id_parent'] !=0))
	{
		$nom_cat = getParentCategoy($data_categorie_parent[0]['id_parent'], $langID, $nom_cat);
	}
	return $nom_cat;
}

function fdp_prix($mode, $data, $price, $frais_manut, $free_prix, $tax_conf)
{
	$fdp = 0;
	if(isset($data[0]['delimiter1']))
	{
		foreach($data as $info)
		{
			if($price >= $info['delimiter1'] && $price < $info['delimiter2'])
			{
				//echo "******************".$info['rate']."**************\n";
					if($info['rate']!='')
					{
						$taux_tva =$info['rate'];
					}
					else
					{
						$taux_tva =$tax_conf;
					} 
					$fdp_HT = $info['price']+$frais_manut;
					$tva = ($fdp_HT*$taux_tva)/100;
					$fdp = $fdp_HT+$tva;
					$fdp = round($fdp, 2);
					return $fdp;
			}

		}
	}
	else
	{
		if($price<=$free_prix)
		{
			$fdp_HT = $frais_manut;
			$tva = ($fdp_HT*$tax_conf)/100;
			$fdp = $fdp_HT+$tva;
			$fdp = round($fdp, 2);
			return $fdp;
		}
		else
		{
			$fdp = 0;
			return $fdp;
		}
	}
	return $fdp;
}
function fdp_poid($mode, $data, $weight, $frais_manut, $free_poid, $tax_conf)
{
	$weight = number_format($weight, 2);
	if(isset($data[0]['delimiter1']))
	{
		foreach($data as $info)
		{
			if($weight >= $info['delimiter1'] && $weight < $info['delimiter2'])
			{
					if($info['rate']!='')
					{
						$taux_tva =$info['rate'];
					}
					else
					{
						$taux_tva =$tax_conf;
					} 
					// echo "weight:".$weight."\n";
					// echo "taux_tva:".$taux_tva."\n";
					// echo "price:".$info['price']."\n";
					
					$fdp_HT = $info['price']+$frais_manut;
					// echo "fdp_HT:".$fdp_HT."\n";

					$tva = ($fdp_HT*$taux_tva)/100;
					// echo "tva:".$tva."\n";

					$fdp = $fdp_HT+$tva;
					// echo "fdp:".$fdp."\n";

					$fdp = number_format($fdp, 2);
					// echo "fdp number_format:".$fdp."\n";

					return $fdp;
			}
		}
	}
	else
	{
		if($weight<=$free_poid)
		{
			$fdp_HT = $frais_manut;
			$tva = ($fdp_HT*$tax_conf)/100;
			$fdp = $fdp_HT+$tva;
			$fdp = round($fdp, 2);
			return $fdp;
		}
		else
		{
			$fdp = 0;
			return $fdp;
		}
	}
	return $fdp;
}

function get_Clientid_lengow()
{
	$sql_client_id = 'SELECT parametre_valeur from '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="client_id"';
	$resultat = Db::getInstance()->getRow($sql_client_id);
	return intval($resultat['parametre_valeur']);
}

function get_Groupid_lengow()
{
	$sql_group_id = 'SELECT parametre_valeur from '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="group_id"';
	$resultat = Db::getInstance()->getRow($sql_group_id);
	return intval($resultat['parametre_valeur']);
}

function get_Tracking_lengow()
{
	$sql_Tracking_id = 'SELECT parametre_valeur from '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="Tracking"';
	$resultat = Db::getInstance()->getRow($sql_Tracking_id);
	return $resultat['parametre_valeur'];
}

function getIdLang($code='')
{
	$sql = "CREATE TABLE IF NOT EXISTS `".constant('_DB_PREFIX_')."parametre_lengow` (
	`id_parametre` INT NOT NULL AUTO_INCREMENT ,
	`parametre_nom` TEXT NOT NULL ,
	`parametre_valeur` TEXT NOT NULL ,
	PRIMARY KEY ( `id_parametre` )
	) DEFAULT CHARSET=utf8 ENGINE = MYISAM";
	Db::getInstance()->Execute($sql);

	if($code == '')
	{
		$sql_langue_bdd = 'SELECT parametre_valeur FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="lang_id"';
		$lang_id_bdd = Db::getInstance()->getRow($sql_langue_bdd);
		$id_lang = $lang_id_bdd['parametre_valeur'];

		if($id_lang=='')
		{
			$id_lang= getIdLang('fr');
			return $id_lang;
		}
		else
		{
			return $id_lang;
		}
	}
	else
	{
		$sql = 'SELECT id_lang FROM '.constant('_DB_PREFIX_').'lang WHERE iso_code="'.mysql_escape_string($code).'"';
		$data = Db::getInstance()->getRow($sql);
		$id_lang = $data['id_lang'];
		return $id_lang;
	}
}

function getMultiShop()
{
	$retour = false;
	//verification de l'activation du multiboutique
	$sql_SHOP_DEFAULT = 'SELECT value FROM '.constant('_DB_PREFIX_').'configuration WHERE name="PS_MULTISHOP_FEATURE_ACTIVE"';
	// echo "sql_SHOP_DEFAULT:".$sql_SHOP_DEFAULT."\n";
	$result = Db::getInstance()->getRow($sql_SHOP_DEFAULT);
	if(isset($result))
	{
		$SHOP_DEFAULT = $result['value'];
		if($SHOP_DEFAULT == 1)
			$retour = true;
	}

	return $retour;
}

function getIdShop($IdShop = '')
{
	$code_shop = '';
	$SHOP_DEFAULT = '';
	if($IdShop == '')
	{
		//verification de l'activation du multiboutique
		if(getMultiShop())
		{
			$sql_SHOP_DEFAULT = 'SELECT value FROM '.constant('_DB_PREFIX_').'configuration WHERE name="PS_SHOP_DEFAULT"';
			//echo "sql_SHOP_DEFAULT:".$sql_SHOP_DEFAULT."\n";
			$result = Db::getInstance()->getRow($sql_SHOP_DEFAULT);
			if(isset($result))
			{
				$SHOP_DEFAULT = $result['value'];
			}
			//echo "SHOP_DEFAULT:".$SHOP_DEFAULT."\n";
			$code_shop = $SHOP_DEFAULT;
		}
	}
	else
	{
		$code_shop = $IdShop;
	}
	return $code_shop;
}


function getIdCurrency($code='')
{
	$idCurrency = '';
	if($code == '')
	{
		/*
		$sql = 'SELECT id_currency FROM '.constant('_DB_PREFIX_').'currency WHERE iso_code="EUR" AND deleted=0';
		$data = Db::getInstance()->getRow($sql);
		$idCurrency =$data['id_currency'] ;
		*/
		$sql_idCurrency = 'SELECT value FROM '.constant('_DB_PREFIX_').'configuration WHERE name="PS_CURRENCY_DEFAULT"';
		$result = Db::getInstance()->getRow($sql_idCurrency);
		
		if(isset($result))
		{
			$idCurrency = $result['value'];
		}
	}
	else
	{
		$sql = 'SELECT id_currency FROM '.constant('_DB_PREFIX_').'currency WHERE iso_code="'.mysql_escape_string($code).'" AND deleted=0';
		//echo "sql currency: ".$sql."\n";
		$data = Db::getInstance()->getRow($sql);
		if(count($data) >0)
			$idCurrency = $data['id_currency'];
			
		if($idCurrency=='')
		{
			$idCurrency= getIdCurrency('EUR');
			return $idCurrency;
		}
		else
		{
			return $idCurrency;
		}
	}
	return $idCurrency;
}

function getCurrencyCode($idCurrency)
{
	$code = '';
	$sql = 'SELECT iso_code FROM '.constant('_DB_PREFIX_').'currency WHERE id_currency="'.mysql_escape_string($idCurrency).'" AND deleted=0';
	//echo "sql currency: ".$sql."\n";
	$data = Db::getInstance()->getRow($sql);
	if(count($data) >0)
		$code = $data['iso_code'];

	return $code;
}

function getLegacyImage()
{
	$LegacyImage = 0;
	$sql_LegacyImage = 'SELECT value FROM '.constant('_DB_PREFIX_').'configuration WHERE name="PS_LEGACY_IMAGES"';
	$result = Db::getInstance()->getRow($sql_LegacyImage);
	
	if(isset($result))
	{
		$LegacyImage = $result['value'];
	}

	return $LegacyImage;
}

function check_tab_parametre_lengow()
{
	//test de la prénce de la table parametre_lengow
	//creation de la table parametre_lengow
	$sql = "CREATE TABLE IF NOT EXISTS `".constant('_DB_PREFIX_')."parametre_lengow` (
	`id_parametre` INT NOT NULL AUTO_INCREMENT ,
	`parametre_nom` TEXT NOT NULL ,
	`parametre_valeur` TEXT NOT NULL ,
	PRIMARY KEY ( `id_parametre` )
	) DEFAULT CHARSET=utf8 ENGINE = MYISAM";
	Db::getInstance()->Execute($sql);
	
	
	$list_category = array();
	$list_parametre ='';

	$sql_parametre = 'SELECT count(*) as nb from '.constant('_DB_PREFIX_').'parametre_lengow';
	$list_parametre = Db::getInstance()->getRow($sql_parametre);

	if($list_parametre !='')
	{
		$sql_categorie = 'SELECT parametre_valeur from '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="cat_id"';
		$resultat = Db::getInstance()->ExecuteS($sql_categorie);
		foreach($resultat as $key=>$value)
		{
			$list_category[$key]=$value['parametre_valeur'];
		}
	}

	return $list_category;
}

function check_produit_parametre_lengow()
{
	$list_produit = array();
	$sql_produit = 'SELECT parametre_valeur from '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="product_id"';
	$resultat = Db::getInstance()->ExecuteS($sql_produit);
	foreach($resultat as $key=>$value)
	{
		$list_produit[$key]=$value['parametre_valeur'];
	}
	return $list_produit;
}


function getTree($tab_cat_selected, $tab_produit_selected, $traduction_produit, $traduction_check_all)
{
	$version = _PS_VERSION_;
	$sub_verison = substr($version, 0, 3);
	$compareVersion = version_compare($sub_verison, '1.4');

	$img_dir = constant('_PS_ADMIN_IMG_');

	if(isset($_GET['id_lang']) && $_GET['id_lang']!='')
	{
		$id_lang = $_GET['id_lang'];
	}
	else
	{
		$id_lang = getIdLang();
	}

	$html ='';
	$html .= "<a href='#' onclick='checkAllBox(this);return false;' style='color:blue;text-decoration:none;font-size:11px'>";
	$html .= $traduction_check_all;
	$html .= "</a><br /><br />\n";

	$sql_category = 'SELECT c.id_category, c.level_depth, cl.name from '.constant('_DB_PREFIX_').'category c LEFT JOIN '.constant('_DB_PREFIX_').'category_lang cl ON c.id_category = cl.id_category ';
	$sql_category .= ' WHERE c.level_depth=1  and cl.id_lang='.$id_lang.' and c.active=1';
	if($compareVersion == 1 && getMultiShop())
	{
		$sql_category .= ' GROUP BY c.id_category';
	}

	$list_category = Db::getInstance()->ExecuteS($sql_category);

	foreach($list_category as $category)
	{
		$nb_product = getNbProductInCategory($category['id_category']);
		$html .= "<span><input style='vertical-align:middle' type='checkbox' value='".$category['id_category']."' name=id_cat[] id='cat_".$category['id_category']."' onclick='check_produit_cat(this, \"produit_cat_".$category['id_category']."\")'";
		if(count($tab_cat_selected)>0 && in_array($category['id_category'], $tab_cat_selected))
		{
			$html .= " checked";
		}
		else
		{
			$html .= "";
		}
		$html .= ">&nbsp;<label for='cat_".$category['id_category']."' style=' float:none; padding:0; text-align:left; width:auto;'>".cleanNomCategorie($category['name'])."</label>\n";
		$html .= "<a href='#' onclick='toggle_produit_cat(\"product_cat_".$category['id_category']."\");return false;' style='color:blue;text-decoration:none;font-size:11px'>";

		if($nb_product > 1)
		$html .= " (id : ".$category['id_category']." - <u>".$nb_product." ".$traduction_produit."s)</u>\n";
		else
		$html .= " (id : ".$category['id_category']." - <u>".$nb_product." ".$traduction_produit.")</u>\n";

		$html .= "</a>\n";
		$html .= getProductInCategory($category['id_category'], $id_lang, $category['level_depth'], $tab_produit_selected)."</span><br />";

		$html .= " </span><br /> \n";
		$html .= getBranch($category['id_category'],1, $id_lang, $tab_cat_selected, $tab_produit_selected, $traduction_produit);
	}
	return $html;
}

function getBranch($idCat, $level, $id_lang, $tab_cat_selected, $tab_produit_selected, $traduction_produit)
{
	$version = _PS_VERSION_;
	$sub_verison = substr($version, 0, 3);
	$compareVersion = version_compare($sub_verison, '1.4');

	$img_dir = constant('_PS_ADMIN_IMG_');
	$next_category_level = $level+1;
	$sql_category = 'SELECT c.id_category, c.level_depth, cl.name from '.constant('_DB_PREFIX_').'category c LEFT JOIN '.constant('_DB_PREFIX_').'category_lang cl ON c.id_category = cl.id_category ';
	$sql_category .= ' WHERE c.level_depth='.mysql_escape_string($next_category_level).' and c.id_parent = '.mysql_escape_string($idCat).' and cl.id_lang='.mysql_escape_string($id_lang).' and c.active=1';
	if($compareVersion == 1 && getMultiShop())
	{
		$sql_category .= ' GROUP BY c.id_category';
	}

	//echo $sql_category."<br />";
	$list_category = Db::getInstance()->ExecuteS($sql_category);
	$html = "";
	$getBranch = "";
	foreach($list_category as $category)
	{
		$nb_product = getNbProductInCategory($category['id_category']);
		$getBranch = getBranch($category['id_category'],$category['level_depth'],$id_lang, $tab_cat_selected, $tab_produit_selected, $traduction_produit);
		$html .= str_repeat('&nbsp;', $category['level_depth']*3)." <span>";
		$html .= "<input type='checkbox' style='vertical-align:middle;border:1px solid #DDD' value='".$category['id_category']."' name=id_cat[] id='cat_".$category['id_category']."' onclick='check_produit_cat(this, \"produit_cat_".$category['id_category']."\")'";

		if(count($tab_cat_selected)>0 && in_array($category['id_category'], $tab_cat_selected))
		{
			$html .= " checked";
		}
		else
		{
			$html .= "";
		}
		$html .= ">&nbsp;<label for='cat_".$category['id_category']."' style=' float:none; padding:0; text-align:left; width:auto;'>".cleanNomCategorie($category['name'])."</label>";
		$html .= "<a href='#' onclick='toggle_produit_cat(\"product_cat_".$category['id_category']."\");return false;' style='color:blue;text-decoration:none;font-size:11px'>";

		if($nb_product > 1)
		$html .= " (id : ".$category['id_category']." - <u>Voir les ".$nb_product." ".$traduction_produit."s)</u>\n";
		else
		$html .= " (id : ".$category['id_category']." - <u>".$nb_product." ".$traduction_produit.")</u>\n";

		$html .= "</a><br />\n";
		$html .= getProductInCategory($category['id_category'], $id_lang, $category['level_depth'], $tab_produit_selected)."</span><br />";
		$html .= $getBranch;
	}
	return $html;
}

function getProductInCategory($idCat, $id_lang, $level, $tab_produit_selected)
{
	$sql_product = 'SELECT DISTINCT pl.name, cp.id_product ';
	$sql_product.= 'FROM '.constant('_DB_PREFIX_').'category_product cp LEFT JOIN '.constant('_DB_PREFIX_').'product p ON cp.id_product=p.id_product ';
	$sql_product .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
	$sql_product.= 'WHERE p.id_category_default='.mysql_escape_string($idCat).' AND pl.id_lang='.mysql_escape_string($id_lang).' GROUP BY cp.id_product';

	$list_produit = Db::getInstance()->ExecuteS($sql_product);

	$html ='<div id="product_cat_'.$idCat.'" style="display: none;line-height:25px">';
	foreach($list_produit as $produit)
	{
		$html .= str_repeat('&nbsp;', $level*8)." <input type='checkbox' style='vertical-align:middle' class='produit_cat_".$idCat."' value='".$produit['id_product']."' name=id_produit[] id='product_".$produit['id_product'];
		$html .= "' onclick='if(this.checked)check_cat(\"cat_".$idCat."\");'";
		if(count($tab_produit_selected)>0 && in_array($produit['id_product'], $tab_produit_selected))
		{
			$html .= " checked";
		}
		else
		{
			$html .= "";
		}
		$html .= ">&nbsp;<label for='product_".$produit['id_product']."' style=' float:none; padding:0; text-align:left; width:auto; font-weight: normal;'>".$produit['name'].' (id : '.$produit['id_product'].')</label><br />';
	}
	$html .='</div>';
	return $html;
}

function getProduct($tab_produit_selected, $traduction_check_all)
{
	$id_lang = getIdLang();
	$sql_product = 'SELECT DISTINCT pl.name, cp.id_product ';
	$sql_product.= 'FROM '.constant('_DB_PREFIX_').'category_product cp LEFT JOIN '.constant('_DB_PREFIX_').'product p ON cp.id_product=p.id_product ';
	$sql_product .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
	$sql_product.= 'WHERE pl.id_lang='.mysql_escape_string($id_lang).' GROUP BY cp.id_product';

	$list_produit = Db::getInstance()->ExecuteS($sql_product);

	$html .= "<a href='#' onclick='checkAllBox(this);return false;' style='color:blue;text-decoration:none;font-size:11px'>";
	$html .= $traduction_check_all;
	$html .= "</a><br /><br />\n";
	$html .='<div id="product_cat_'.$idCat.'" style="display: bloc;line-height:25px">';
	foreach($list_produit as $produit)
	{
		$html .= " <input type='checkbox' style='vertical-align:middle' class='produit_cat_".$idCat."' value='".$produit['id_product']."' name=id_produit[] id='product_".$produit['id_product'];
		$html .= "' onclick='if(this.checked)check_cat(\"cat_".$idCat."\");'";
		if(count($tab_produit_selected)>0 && in_array($produit['id_product'], $tab_produit_selected))
		{
			$html .= " checked";
		}
		else
		{
			$html .= "";
		}
		$html .= ">&nbsp;<label for='product_".$produit['id_product']."' style=' float:none; padding:0; text-align:left; width:auto; font-weight: normal;'>".$produit['name'].' (id : '.$produit['id_product'].')</label><br />';
	}
	$html .='</div>';
	return $html;
}

function getNbProductInCategory($idCat)
{
	$sql_product = 'SELECT COUNT( DISTINCT cp.id_product) as nb_product ';
	$sql_product.= 'FROM '.constant('_DB_PREFIX_').'category_product cp LEFT JOIN '.constant('_DB_PREFIX_').'product p ON cp.id_product=p.id_product ';
	$sql_product.= 'WHERE p.id_category_default='.mysql_escape_string($idCat);
	$count_product = Db::getInstance()->ExecuteS($sql_product);
	$count_product = $count_product[0];
	if(count($count_product)>0)
	{
		$retour = $count_product['nb_product'];
	}
	else
	{
		$retour = 0;
	}
	// echo "nb:".$retour."<br />";
	return $retour;
}

function getNomCategorie($catID, $langID)
{
	$sql_categorie_nom .='SELECT cl.name  as category_name ';
	$sql_categorie_nom .='FROM '.constant('_DB_PREFIX_').'category_lang cl ';
	$sql_categorie_nom .='WHERE cl.id_lang='.mysql_escape_string($langID).' AND cl.id_category='.mysql_escape_string($catID);

	$data_categorie = Db::getInstance()->ExecuteS($sql_categorie_nom);
	$nom_cat = cleanNomCategorie($data_categorie[0]['category_name']);
	return trim($nom_cat);
}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// TRAITEMENT DONNEES///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getCleanData($myStr)
{
	$myStr = trim($myStr);
	$myStr = str_replace("\\","_",$myStr);
	$myStr = str_replace("/","_",$myStr);
	$myStr = str_replace("&","_",$myStr);
	$myStr = str_replace("’","_",$myStr);
	$myStr = str_replace(":","_",$myStr);
	$myStr = str_replace("*","_",$myStr);
	$myStr = str_replace("?","_",$myStr);
	$myStr = str_replace("\"","_",$myStr);
	$myStr = str_replace("<","_",$myStr);
	$myStr = str_replace(">","_",$myStr);
	$myStr = str_replace("|","_",$myStr);
	$myStr = str_replace("_/_","_",$myStr);
	
	$myStr = str_ireplace("(","",$myStr);
	$myStr = str_ireplace(")","",$myStr);
	$myStr = str_ireplace(":","",$myStr);
	$myStr = str_ireplace(" ","_",$myStr);
	$myStr = str_ireplace("'","_",$myStr);
	$myStr = str_ireplace("×","",$myStr);
	$myStr = str_ireplace(".","",$myStr);
	$myStr = str_ireplace(",","",$myStr);
	
	$myStr = str_ireplace("Ø","",$myStr);
	$myStr = str_ireplace("°","",$myStr);
	$myStr = str_ireplace("%","",$myStr);

	$myStr = str_ireplace("à","a",$myStr);
	$myStr = str_ireplace("è","e",$myStr);
	$myStr = str_replace("é","e",$myStr);

	$myStr = str_ireplace("ù","u",$myStr);

	$myStr = str_ireplace("â","a",$myStr);
	$myStr = str_ireplace("ê","e",$myStr);
	$myStr = str_ireplace("û","u",$myStr);
	$myStr = str_ireplace("î","i",$myStr);
	$myStr = str_ireplace("ô","o",$myStr);

	$myStr = str_ireplace("ä","a",$myStr);
	$myStr = str_ireplace("ë","e",$myStr);
	$myStr = str_ireplace("ü","u",$myStr);
	$myStr = str_ireplace("ï","i",$myStr);
	$myStr = str_ireplace("ö","o",$myStr);

	$myStr = str_ireplace("ç","c",$myStr);

	$myStr = str_ireplace("À","A",$myStr);
	$myStr = str_ireplace("É","E",$myStr);
	$myStr = str_ireplace("È","E",$myStr);
	$myStr = str_ireplace("Ù","u",$myStr);

	$myStr = str_ireplace("Â","A",$myStr);
	$myStr = str_ireplace("Ê","E",$myStr);
	$myStr = str_ireplace("Û","U",$myStr);
	$myStr = str_ireplace("Î","I",$myStr);
	$myStr = str_ireplace("Ô","O",$myStr);

	$myStr = str_ireplace("Ä","A",$myStr);
	$myStr = str_ireplace("Ë","E",$myStr);
	$myStr = str_ireplace("Ü","U",$myStr);
	$myStr = str_ireplace("Ï","I",$myStr);
	$myStr = str_ireplace("Ö","O",$myStr);

	$myStr = str_ireplace("Ç","C",$myStr);

	//ES
	$myStr = str_ireplace("ó","o",$myStr);
	$myStr = str_ireplace("Õ","O",$myStr);
	$myStr = str_ireplace("í","i",$myStr);
	$myStr = str_ireplace("Í","I",$myStr);
	$myStr = str_ireplace("ñ","n",$myStr);
	$myStr = str_ireplace("Ñ","N",$myStr);
	$myStr = str_ireplace("á","a",$myStr);
	$myStr = str_ireplace("Á","A",$myStr);
	$myStr = str_ireplace("ú","u",$myStr);
	$myStr = str_ireplace("Ú","U",$myStr);

	//taille max des champs: 58 char
	$myStr = substr($myStr, 0, 58);

	return strtoupper($myStr);
}



function decomposeIdImage($idImage)
{
	// echo "idImage:".$idImage."\n";
	$retour = '';
	for($i=0; $i < strlen($idImage); $i++)
	{
		// echo "***".$idImage[$i]."\n";
		$retour .= '/'.$idImage[$i];
	}
	$retour .= '/'.$idImage;
	// echo "\n".$retour."\n";
	return $retour;
}

function getPrice($prix, $tax=0, $devise)
{
	if($tax > 0)
	{
		$price = ($prix)+($prix*$tax/100);
	}
	else
	{
		$price = $prix;
	}

	$price = Tools::convertPrice($price, Currency::getCurrency(intval($devise)));

	$price = number_format(round($price, 2),2, '.', '');

	// echo "devise getPrice:" .$devise."- prix:".$prix." -- price:".$price."<br>";
	return $price;
}

function getPourcentage($idProduit)
{
	$today = date("Y-m-d H:i:s");
	
	//recupération des prix speciaux pour un produit
	$sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.'  ORDER BY id_specific_price DESC';
	$liste_special_price = Db::getInstance()->ExecuteS($sql_special_price);

	if(count($liste_special_price) > 1)
	{
		// $sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.' AND ( ( (`to`<= "'.$today.'"  OR `to` = "0000-00-00 00:00:00") AND (`from` >= "'.$today.'" OR `from` = "0000-00-00 00:00:00")) OR (`to`=`from`)) ORDER BY id_specific_price DESC';
		$sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.' AND  (`from` = "0000-00-00 00:00:00" OR "'.$today.'" >= `from`)  AND  (`to` = "0000-00-00 00:00:00" OR "'.$today.'" <= `to`) ORDER BY id_specific_price DESC';
		$liste_special_price = Db::getInstance()->ExecuteS($sql_special_price);
	}
	$reduction = $liste_special_price[0]['reduction']*100;

	return $reduction;
}

function getSpecialPrice($idProduit, $prix, $tax=0, $reduction, $reduction_type, $devise)
{
	$today = date("Y-m-d H:i:s");
	$timestamp_today = strtotime($today);
	
	//recupération des prix speciaux pour un produit
	$sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.'  ORDER BY id_specific_price DESC';
	$liste_special_price = Db::getInstance()->ExecuteS($sql_special_price);

	if(count($liste_special_price) > 1)
	{
		// $sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.' AND ( ( (`to`<= "'.$today.'"  OR `to` = "0000-00-00 00:00:00") AND (`from` >= "'.$today.'" OR `from` = "0000-00-00 00:00:00")) OR (`to`=`from`)) ORDER BY id_specific_price DESC';
		$sql_special_price = 'SELECT * FROM '.constant('_DB_PREFIX_').'specific_price WHERE id_product='.$idProduit.' AND  (`from` = "0000-00-00 00:00:00" OR "'.$today.'" >= `from`)  AND  (`to` = "0000-00-00 00:00:00" OR "'.$today.'" <= `to`) ORDER BY id_specific_price DESC';
		$liste_special_price = Db::getInstance()->ExecuteS($sql_special_price);
	}
	// print_r($liste_special_price);
	// echo "sql_special_price:" .$sql_special_price." \n";
	
	$debut = $liste_special_price[0]['from'];
	$timestamp_debut = strtotime($debut);

	$fin = $liste_special_price[0]['to'];
	$timestamp_fin = strtotime($fin);
	$reduction = $liste_special_price[0]['reduction'];
	$reduction_type = $liste_special_price[0]['reduction_type'];

	// echo "idProduit:" .$idProduit." \n";
	// echo "reduction:" .$reduction." \n";
	// echo "reduction_type:" .$reduction_type." \n";
	// echo "debut:" .$debut." -- fin: ".$fin."\n";
	// echo "timestamp_debut:" .$timestamp_debut." -- timestamp_fin: ".$timestamp_fin."\n\n";
	if( ($reduction > 0) && ($reduction_type !=''))
	{
		if( ($timestamp_today<=$timestamp_fin && $timestamp_today >= $timestamp_debut) || ($timestamp_today > $timestamp_debut && $timestamp_fin < 0))
		{
			// echo "cas 1 \n";
			// Prix reduction (pourcentage)
			if($reduction_type == 'percentage')
			{
				$price_reduction = number_format(round($prix-($prix*$reduction),2),2, '.', '');
			}
			if($reduction_type == 'amount') //le montant est deja en TTC
			{
				$price_reduction = number_format(round($prix-$reduction,2),2, '.', '');
			}
		}
		// elseif($timestamp_debut == $timestamp_fin && $timestamp_debut!='')
		elseif($timestamp_debut == $timestamp_fin)
		{
			// echo "cas 2 \n";
			// Prix reduction (pourcentage)
			if($reduction_type == 'percentage')
			{
				$price_reduction = number_format(round($prix-($prix*$reduction),2),2, '.', '');
			}
			if($reduction_type == 'amount') //le montant est deja en TTC
			{
				$price_reduction = number_format(round($prix-$reduction,2),2, '.', '');
			}
		}
		else
		{
			$price_reduction = number_format(round($prix,2),2, '.', '');
		}
	}
	else
	{
		$price_reduction = number_format(round($prix,2),2, '.', '');
	}

	// echo "devise:" .$devise." \n";
	// echo "price_reduction:" .$price_reduction." \n";
	$price_reduction = Tools::convertPrice($price_reduction, Currency::getCurrency(intval($devise)));
	// echo "convertPrice price_reduction:" .$price_reduction." \n";

	// echo "devise getSpecialPrice:" .$devise."- prix:".$prix." -- price:".$price_reduction." <br>";
	return $price_reduction;
}

function getSpecialPrice1_3($data, $devise)
{
	$today = date("Y-m-d H:i:s");
	$timestamp_today = strtotime($today);
	$price = getPrice($data['price'], $data['tax'], $devise);

	$debut = $data['reduction_from'];
	$timestamp_debut = strtotime($debut);

	$fin = $data['reduction_to'];
	$timestamp_fin = strtotime($fin);
	// echo "debut:" .$debut." -- fin: ".$fin."\n";
	// echo "timestamp_debut:" .$timestamp_debut." -- timestamp_fin: ".$timestamp_fin."\n\n";
	// echo "timestamp_today:" .$timestamp_today."\n";
	// print_r($data);

	if( ($data['reduction_percent'] > 0) || ($data['reduction_price'] > 0))
	{
		if($timestamp_today>=$timestamp_debut && $timestamp_today<=$timestamp_fin)
		{
			// echo "cas 1 \n";
			if($data['reduction_percent'] > 0)
			{
				$price_reduction = number_format(round($price-(($price*$data['reduction_percent'])/100),2),2, '.', '');
				// echo "getSpecialPrice1_3 price:" .$price."\n";
			}
			if($data['reduction_price'] > 0)
			{
				$price_reduction = number_format(round($price-$data['reduction_price'],2),2, '.', '');
			}
		}
		elseif($timestamp_debut == $timestamp_fin)
		{
			// echo "cas 2 \n";
			if($data['reduction_percent'] > 0)
			{
				$price_reduction = number_format(round($price-(($price*$data['reduction_percent'])/100),2),2, '.', '');
			}
			if($data['reduction_price'] > 0)
			{
				$price_reduction = number_format(round($price-$data['reduction_price'],2),2, '.', '');
			}
		}
		else
		{
			$price_reduction = number_format(round($price,2),2, '.', '');
		}
	}
	else
	{
		$price_reduction = number_format(round($price,2),2, '.', '');
	}

	// echo "price_reduction:" .$price_reduction." \n";
	// if($data['tax'] != '' && $data['reduction_percent'] > 0)
	// {
	// 	$price_reduction = $price_reduction + ($price_reduction*$data['tax']) / 100;
	// 	$price_reduction = number_format(round($price_reduction,2),2, '.', '');
	// }
	$price_reduction = Tools::convertPrice($price_reduction, Currency::getCurrency(intval($devise)));
	// echo "price_reduction final:" .$price_reduction." \n";
	// echo "tax:" .$data['tax']." \n";

	return $price_reduction;
}


//nettoyage 
function netoyage_html($string)
{
	$string = nl2br($string);
	$pattern = '@<[\/\!]*?[^<>]*?>@si'; //nettoyage du code HTML
	$string = preg_replace($pattern, ' ', $string); 
	$string = preg_replace('/[\s]+/', ' ', $string); //nettoyage des espaces multiples
	
	$string = trim ($string);
	$string = str_replace("&nbsp;"," ",$string);
	$string = str_replace("|"," ",$string);
	$string = str_replace('"',"'",$string);
	$string = str_replace('’',"'",$string);
	$string = str_replace("&#39;","' ",$string);
	$string = str_replace("&#150;","-",$string);
	$string = str_replace(chr(9)," ",$string);
	$string = str_replace(chr(10)," ",$string);
	$string = str_replace(chr(13)," ",$string);
	return $string;
}


function cleanNomCategorie($nom)
{
	//$nom_cat = preg_replace("^[0-9]*^",'',$nom);
	$nom_cat = preg_replace("^[-.]*^",'',$nom);
	$nom_cat = trim($nom_cat);
	return $nom_cat;
}

function fieldLangue()
{
	$html ='';
	$sql_langue = 'SELECT id_lang,name,iso_code FROM '.constant('_DB_PREFIX_').'lang WHERE active=1';
	$liste_langue = Db::getInstance()->ExecuteS($sql_langue);

	$sql_langue_bdd = 'SELECT parametre_valeur FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="lang_id"';
	$lang_id_bdd = Db::getInstance()->getRow($sql_langue_bdd);
	$lang_id_bdd = $lang_id_bdd['parametre_valeur'];

	//print_r($liste_langue);
	$html .= '<select name="lang_id">';
	foreach($liste_langue as $langue)
	{
		if($langue['id_lang']==$lang_id_bdd)
		{
			$selected = 'selected';
		}
		else
		{
			$selected = '';
		}
		$html .= '<option value="'.$langue['id_lang'].'" '.$selected.'>'.$langue['name'].'</option>';
	}
	$html .= '</select>';
	return $html;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////// TRAITEMENT ERREUR ///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//message erreur formulaire
function fieldClientLengow($valeur='', $txt, $msg_erreur = '')
{
	$html = '<br /><b>'.$txt.'</b> : <input size="5" maxlength="5" type="text" name="Clientid_lengow" value="';
	// if($valeur !='')
	if(intval($valeur) > 0)
	{
		$html .= $valeur;
	}
	$html .= '" />&nbsp;'.$msg_erreur.'<br />';
	return $html;
}

function fieldClientGroupLengow($valeur='', $txt, $msg_erreur = '')
{
	$html = '<br /><b>'.$txt.'</b> : <input size="5" maxlength="5" type="text" name="Groupid_lengow" value="';
	
	// if($valeur !='')
	if(intval($valeur) > 0)
	{
		$html .= $valeur;
	}
	$html .= '" />&nbsp;'.$msg_erreur.'<br />';

	return $html;
}

function fieldTrackingLengow($valeur='', $txt)
{
	if($valeur == 'SimpleTag' || $valeur=='')
	{
		$checkedSimpleTag= 'checked';
		$checkedTagCapsule= '';
		$checkedNoTag= '';
	}
	elseif($valeur == 'TagCapsule' || $valeur=='')
	{
		$checkedSimpleTag= '';
		$checkedNoTag= '';
		$checkedTagCapsule= 'checked';
	}
	else
	{
		$checkedSimpleTag= '';
		$checkedTagCapsule= '';
		$checkedNoTag= 'checked';
	}
	$html = '<h4 style="clear:both;">'.$txt.' : </h4>';
	$html .= '<ul style="list-style-type: none; margin: 0; padding: 0; text-align: left;">';
	$html .= '<li style="padding: 5px 0;"><label for="NoTag" style="float: none;"><input type="radio" name="Tracking_lengow" value="NoTag" id="NoTag" '.$checkedNoTag.'>&nbsp;NoTag</label></li>';
	$html .= '<li style="padding: 5px 0;"><label for="SimpleTag" style="float: none;"><input type="radio" name="Tracking_lengow" value="SimpleTag" id="SimpleTag" '.$checkedSimpleTag.'>&nbsp;SimpleTag</label></li>';
	$html .= '<li style="padding: 5px 0;"><label for="TagCapsule" style="float: none;"><input type="radio" name="Tracking_lengow" value="TagCapsule" id="TagCapsule" '.$checkedTagCapsule.'>&nbsp;TagCapsule</label></li>';
	$html .= '</ul>';

	return $html;
}

function display_xml_error($error, $xml='')
{
	// $return  = $xml[$error->line - 1] . "\n";
	$return .= str_repeat('-', $error->column) . "\n<br>";

	switch ($error->level) 
	{
		case LIBXML_ERR_WARNING:
			$return .= "Warning $error->code: ";
			break;
		case LIBXML_ERR_ERROR:
			$return .= "Error $error->code: ";
			break;
		case LIBXML_ERR_FATAL:
			$return .= "Fatal Error $error->code: ";
			break;
}

	$return .= trim($error->message) .
	"\n<br>  Line: $error->line" .
	"\n<br>  Column: $error->column";

	if ($error->file) 
	{
		$return .= "\n<br>  File: $error->file";
	}
	return "$return<br>\n\n--------------------------------------------\n\n<br>";
}

class Error extends Exception
{
	public function __construct($Msg)
	{
		parent::__construct($Msg);
	}

	public function getError($request)
	{
		$output  = '<div><strong>'.$this->getMessage().'</strong>';
		$output .= 'Ligne: '.$this->getLine().'<br />'.
		$output .= 'Fichier: '.$this->getFile().'<br />'.
		$output .= 'Requete: '.$request.'<br /></div>';
		return $output;
	}
}

function checkSeparator($path)
{
	if(strpos($path, "\\") !== False)
	{
		// echo "windows<br/>";
		define('_SEPARATOR_', "\\");
	}
	else
	{
		// echo "unix<br/>";
		define('_SEPARATOR_', "/");
	}
}
?>