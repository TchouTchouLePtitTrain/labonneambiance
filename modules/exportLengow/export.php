<?php

error_reporting(0);

set_time_limit(0);

ini_set('memory_limit', '512M');

header('Content-Type: text/plain; charset=utf-8');

if((isset($_GET['debug'])) && ($_GET['debug'] ==1))
{
	error_reporting(1);
	ini_set('display_errors', 1);
	ini_set('display_errors', 'on');
	define('_PS_DEBUG_SQL_', true);
}

if ((include 'function.php') !== 1)
{
	die('Include failed function.php');
}
else
{
	checkSeparator(dirname(__FILE__));
}

$path = explode(_SEPARATOR_.'modules',dirname(__FILE__));

$settings = $path[0]._SEPARATOR_.'config'._SEPARATOR_.'settings.inc.php';

$config = $path[0]._SEPARATOR_.'config'._SEPARATOR_.'config.inc.php';

$nom_module = $path[1];

if ((include $settings) !== 1)
{
	die('Include failed '.$settings.'');
}

if ((include $config) !== 1)
{
	die('Include failed '.$config.'');
}

if ((include 'exportLengow.php') !== 1)
{
	die('Include failed exportLengow.php');
}

$E = new exportLengow();
$E->secureDir($nom_module);
$link=new Link();

$flag_delim_poid='non';
$flag_delim_prix='non';

$version = _PS_VERSION_;
$sub_verison = substr($version, 0, 3);
$compareVersion = version_compare($sub_verison, '1.4');

//LANG DU SITE : recup en bdd par default, sinon fr
$id_lang = getIdLang($_GET['lang']);
// echo "id_lang:".$id_lang."\n";
$IdCurrency = getIdCurrency($_GET['CUR']);
//echo "IdCurrency:".$IdCurrency."\n";

$Currency = getCurrencyCode($IdCurrency);
//echo "Currency:".$Currency."\n";

//echo "compareVersion:".$compareVersion."\n";
if($compareVersion == 1) //version 1.5.X
{
	$IdShop = getIdShop($_GET['Shop']);
}
//$IdShop = '';
//choix de la gestion des images
$LegacyImage = getLegacyImage();
$buffer = '';

$sql_colonne_feature = 'SELECT fl.name, fl.id_feature FROM '.constant('_DB_PREFIX_').'feature_lang fl WHERE fl.id_lang='.mysql_escape_string($id_lang);
$sql_colonne_feature .= ' ORDER BY fl.id_feature ASC ';
$data_colonne_feature = Db::getInstance()->ExecuteS($sql_colonne_feature);

$entete = '"ID_PRODUCT"|"NAME_PRODUCT"|"REFERENCE_PRODUCT"|"SUPPLIER_REFERENCE"|"MANUFACTURER"|"CATEGORY"|"DESCRIPTION"|"DESCRIPTION_SHORT"|"PRICE_PRODUCT"|"WHOLESALE_PRICE"|"PRICE_HT"|"PRICE_REDUCTION"|"POURCENTAGE_REDUCTION"|"QUANTITY"|"WEIGHT"|"EAN"|"UPC"|"ECOTAX"|';
$entete .= '"AVAILABLE_PRODUCT"|"URL_PRODUCT"|"IMAGE_PRODUCT"|"FDP"|';
$entete .='"ID_MERE"|"DELAIS_LIVRAISON"|"IMAGE_PRODUCT_2"|"IMAGE_PRODUCT_3"|"REDUCTION_FROM"|"REDUCTION_TO"|';
$entete .='"META_KEYWORDS"|"META_DESCRIPTION"|"URL_REWRITE"|"PRODUCT_TYPE"|"PRODUCT_VARIATION"|"CURRENCY"|"CONDITION"|';

//FEATURE DES PRODUITS
$entete_feature='';
foreach($data_colonne_feature as $colonne_feature)
{
	$nom = getCleanData($colonne_feature['name']);
	$entete .= '"'.strtoupper($nom).'"|';
	$entete_feature .= '"'.strtoupper($nom).'"|';
}

//ATTRIBUTS DES PRODUITS
$entete_attribute='';
$sql_attribute_declinaison = 'SELECT DISTINCT(agl.name), agl.id_attribute_group FROM '.constant('_DB_PREFIX_').'product_attribute pa ';
$sql_attribute_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute';
$sql_attribute_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute a ON pac.id_attribute = a.id_attribute';
$sql_attribute_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_lang al ON al.id_attribute = pac.id_attribute';
$sql_attribute_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group';
$sql_attribute_declinaison .= ' WHERE al.id_lang ='.mysql_escape_string($id_lang).' AND agl.id_lang ='.mysql_escape_string($id_lang).' ORDER BY agl.id_attribute_group ASC';
$liste_attribute_declinaison =  Db::getInstance()->ExecuteS($sql_attribute_declinaison);
foreach($liste_attribute_declinaison as $attribute_declinaison)
{
	$nom = getCleanData($attribute_declinaison['name']);
	$entete .= '"'.strtoupper($nom).'"|';
	$entete_attribute .= '"'.strtoupper($nom).'"|';
}
$entete = $entete."\r\n";
$buffer .= $entete;

///////////////////////
//frais de port
///////////////////////
$sql_config = 'SELECT name, value FROM '.constant('_DB_PREFIX_').'configuration ';
$data_conf = Db::getInstance()->ExecuteS($sql_config);
foreach($data_conf as $conf)
{
	if($conf['name'] == 'PS_SHIPPING_HANDLING')
	$frais_manut = $conf['value'];
	if($conf['name'] == 'PS_SHIPPING_FREE_PRICE')
	$free_prix = $conf['value'];
	if($conf['name'] == 'PS_SHIPPING_FREE_WEIGHT')
	$free_poid = $conf['value'];
	if($conf['name'] == 'PS_SHIPPING_METHOD')
	$mode = $conf['value'];
	if($conf['name'] == 'PS_CARRIER_DEFAULT')
	$id_carrier = $conf['value'];
	if($conf['name'] == 'PS_COUNTRY_DEFAULT')
	$id_pays = $conf['value'];
	if($conf['name'] == 'PS_TAX')
	$id_tax_conf = $conf['value'];
}

$sql_tax = 'SELECT rate FROM '.constant('_DB_PREFIX_').'tax WHERE id_tax='.mysql_escape_string($id_tax_conf);
$data_tax = Db::getInstance()->getRow($sql_tax);
$tax_conf = $data_tax['rate'];

///////////////////////////////
//mode de calcul des frais de port: tranche de poid OU tranche de prix
///////////////////////////////
if($mode == 0) //tranche prix
{
	$sql_range_price = 'SELECT c.id_carrier, rp.delimiter1, rp.delimiter2, t.rate, d.price ';
	$sql_range_price .='FROM '.constant('_DB_PREFIX_').'range_price rp ';
	$sql_range_price .='LEFT JOIN '.constant('_DB_PREFIX_').'carrier c ON c.id_carrier=rp.id_carrier ';
	if($compareVersion < 0)
	{
		$sql_range_price .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=c.id_tax ';
	}
	else
	{
		$sql_range_price .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=c.id_tax_rules_group ';
	}
	$sql_range_price .='LEFT JOIN '.constant('_DB_PREFIX_').'delivery d ON rp.id_range_price=d.id_range_price ';
	$sql_range_price .='WHERE rp.id_carrier='.mysql_escape_string($id_carrier);
	if($compareVersion == 1 && $IdShop != '') //version 1.5.X
	{
		$sql_range_price .= ' AND d.id_shop="'.mysql_escape_string($IdShop).'" ';
	}
	$sql_range_price .=' AND d.id_zone=(SELECT id_zone FROM '.constant('_DB_PREFIX_').'country WHERE id_country='.mysql_escape_string($id_pays).') ORDER BY rp.delimiter1';
	$data_range_price =  Db::getInstance()->ExecuteS($sql_range_price);

	$cpt = 0;
	$numrow = count($data_range_price);
	foreach($data_range_price as $range_price)
	{
		$data_price[$cpt] = $range_price;
		if (++$cpt == $numrow)
		break;
	}
}
else //mode == 1 tranche poid
{
	$sql_range_weight = 'SELECT c.id_carrier, rw.delimiter1, rw.delimiter2, t.rate, d.price ';
	$sql_range_weight .='FROM '.constant('_DB_PREFIX_').'range_weight rw ';
	$sql_range_weight .='LEFT JOIN '.constant('_DB_PREFIX_').'carrier c ON c.id_carrier=rw.id_carrier ';
	if($compareVersion < 0)
	{
		$sql_range_weight .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=c.id_tax ';
	}
	else
	{
		$sql_range_weight .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=c.id_tax_rules_group ';
	}
	$sql_range_weight .='LEFT JOIN '.constant('_DB_PREFIX_').'delivery d ON rw.id_range_weight=d.id_range_weight ';
	$sql_range_weight .='WHERE rw.id_carrier='.mysql_escape_string($id_carrier);
	if($compareVersion == 1 && $IdShop != '') //version 1.5.X
	{
		$sql_range_weight .= ' AND d.id_shop="'.mysql_escape_string($IdShop).'" ';
	}
	$sql_range_weight .=' AND d.id_zone=(SELECT id_zone FROM '.constant('_DB_PREFIX_').'country WHERE id_country='.mysql_escape_string($id_pays).') ORDER BY rw.delimiter1';
	$data_range_weight =  Db::getInstance()->ExecuteS($sql_range_weight);
	$cpt = 0;
	
	$numrow = count($data_range_weight);
	if($numrow  >0 )
	{
		foreach($data_range_weight as $range_weight)
		{
			$data_weight[$cpt] = $range_weight;
			if (++$cpt == $numrow)
			break;
		}
	}
}
///////////////////////////////
//DELAIS DE LIVRAISON
///////////////////////////////
$sql_delais_livraison = 'SELECT cl.delay FROM '.constant('_DB_PREFIX_').'carrier_lang cl ';
$sql_delais_livraison .= 'WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cl.id_carrier='.mysql_escape_string($id_carrier);
$data_delais_livraison = Db::getInstance()->getRow($sql_delais_livraison);
$delais_livraison = $data_delais_livraison['delay'];


/////////////////////////////////
//RECUPERATION DES INFOS PRODUIT
/////////////////////////////////
if($compareVersion < 0)
{
	$sql_produit = 'SELECT p.id_product,p.reference as reference_produit,p.supplier_reference, p.ean13,p.ecotax,p.quantity, pl.id_lang as lang, ';
	$sql_produit .= ' p.price,t.rate as tax,p.reduction_percent, p.wholesale_price, p.reduction_price, p.id_category_default as category_id, ';
	$sql_produit .= ' p.reduction_from,p.reduction_to, ';
	$sql_produit .='pl.description,pl.description_short,pl.name,pl.available_now,p.weight,m.name as manufacturer,';
	$sql_produit .='(SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'image pi WHERE pi.id_product=p.id_product AND pi.cover=1 LIMIT 1) as id_image,';
	$sql_produit .='(SELECT cl.name FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
	$sql_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product order by cp.position ASC LIMIT 1) as category ';
	$sql_produit .='FROM '.constant('_DB_PREFIX_').'product p ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute pa ON p.id_product=pa.id_product ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'manufacturer m ON m.id_manufacturer=p.id_manufacturer ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=p.id_tax ';
	$sql_produit .='WHERE pl.id_lang='.mysql_escape_string($id_lang).' AND p.active=1 ';
}
else//1.4.X & 1.5.X
{
	$sql_produit = 'SELECT p.id_product,p.reference as reference_produit,p.supplier_reference, p.ean13, p.upc,p.ecotax, pl.id_lang as lang, pl.link_rewrite,';
	if($compareVersion == 1) //version 1.5.X
	{
		$sql_produit .= ' psa.quantity, psupp.product_supplier_reference, ps.condition, ';
	}
	else
	{
		$sql_produit .= ' p.quantity, p.condition, ';
	}
	$sql_produit .= ' p.price,p.id_tax_rules_group,t.rate as tax, t.id_tax, p.additional_shipping_cost, p.wholesale_price, p.id_category_default as category_id, sp.reduction, sp.reduction_type, sp.from, sp.to, ';
	$sql_produit .='pl.description,pl.description_short,pl.meta_description,pl.meta_keywords,pl.name,pl.available_now,p.weight,m.name as manufacturer,';
	$sql_produit .='(SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'image pi WHERE pi.id_product=p.id_product AND pi.cover=1 LIMIT 1) as id_image,';
	$sql_produit .='(SELECT cl.name FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
	$sql_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product order by cp.position ASC LIMIT 1) as category ';

	$sql_produit .=',(SELECT cl.link_rewrite FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
	$sql_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product AND cl.id_category=p.id_category_default order by cp.position ASC LIMIT 1) as cat_link_rewrite ';

	$sql_produit .='FROM '.constant('_DB_PREFIX_').'product p ';
	
	if($compareVersion == 1) //version 1.5.X
	{
		$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_shop ps ON p.id_product=ps.id_product ';
		$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'stock_available psa ON p.id_product=psa.id_product ';
		$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_supplier psupp ON p.id_product=psupp.id_product ';
	}

	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'specific_price sp ON p.id_product=sp.id_product ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute pa ON p.id_product=pa.id_product ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'manufacturer m ON m.id_manufacturer=p.id_manufacturer ';
	//tax sur le pays par defaut
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax_rule tr ON tr.id_tax_rules_group=p.id_tax_rules_group AND tr.id_country="'.mysql_escape_string($id_pays).'" ';
	$sql_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=tr.id_tax ';
	$sql_produit .='WHERE pl.id_lang='.mysql_escape_string($id_lang).' ';
	
	if($compareVersion == 1 && $IdShop != '') //version 1.5.X
	{
		$sql_produit .= ' AND ps.id_shop="'.mysql_escape_string($IdShop).'" AND ps.active=1 AND psupp.id_product_attribute=0 ';
	}
	else
	{
		$sql_produit .=' AND p.active=1 ';
	}

}

$tab_cat_selected_bdd = check_tab_parametre_lengow();
if(!empty($tab_cat_selected_bdd) && count($tab_cat_selected_bdd)>0)
{
	$list_cat_selected_bdd = implode(',', $tab_cat_selected_bdd);
	$sql_produit .= ' AND p.id_category_default in ('.$list_cat_selected_bdd.') ';
}

$tab_produit_selected_bdd = check_produit_parametre_lengow();
if(!empty($tab_produit_selected_bdd) && count($tab_produit_selected_bdd)>0)
{
	$list_produit_selected_bdd = implode(',', $tab_produit_selected_bdd);
	//echo "list_produit_selected_bdd: ".$list_produit_selected_bdd."\n";
	if($compareVersion == 1 && $IdShop != '') //version 1.5.X
	{
		$sql_produit .= ' AND ps.id_product in ('.$list_produit_selected_bdd.') ';
	}
	else
	{
		$sql_produit .= ' AND p.id_product in ('.$list_produit_selected_bdd.') ';
	}
}
// $sql_produit .= ' AND p.id_product =543 ';
$sql_produit .='GROUP BY p.id_product';

$liste_produit = Db::getInstance()->ExecuteS($sql_produit);

///////////////////////////////
// BOUCLE PRODUITS
///////////////////////////////
//$Product = new ProductCore();
foreach($liste_produit as $data)
{
	// echo "-->quantity:".$Product->getQuantity($data['id_product'])."\n";

	//ID_PRODUCT
	$id_product = $data['id_product'];

	// Description longue
	$description = netoyage_html($data['description']);

	// Description courte
	$description_short = netoyage_html($data['description_short']);

	// Categorie
	$category = (getParentCategoy($data['category_id'], $id_lang));

	// Nom Produit
	$name = trim($data['name']);
	$name = str_replace('"',"'",$name);

	// Disponibilité
	$available_now = ($data['available_now']);
	
	// Ecotax
	$ecotax = ($data['ecotax']);
	
	// EAN
	$ean13 = ($data['ean13']);

	// UPC
	$upc = ($data['upc']);

	// Référence
	$reference = ($data['reference_produit']);

	// Référence Fournisseur
	if($compareVersion == 1) //version 1.5.X
	{
		$supplier_reference = ($data['product_supplier_reference']);
	}
	else
	{
		$supplier_reference = ($data['supplier_reference']);
	}

	// Quantité
	$quantity = ($data['quantity']);
	// echo "quantity:".$quantity."\n";
	
	// Fabricant
	$manufacturer = ($data['manufacturer']);

	// Prix
	$price = getPrice($data['price'], $data['tax'], $IdCurrency);

	//Prix reduction
	if($compareVersion < 0)
	{
		$price_reduction = getSpecialPrice1_3($data,$IdCurrency);
	}
	else
	{
		$price_reduction = getSpecialPrice($data['id_product'], $price, $data['tax'], $data['reduction'], $data['reduction_type'],$IdCurrency);
	}
	
	// Prix achat HT - wholesale_price
	$wholesale_price = getPrice($data['wholesale_price'], 0, $IdCurrency);
	
	// Prix vente HT
	$price_HT = getPrice($data['price'], 0, $IdCurrency);;

	// Pourcentage reduction
	if($data['reduction_type'] == 'percentage')
	{
		$pourcentage_reduction = getPourcentage($data['id_product']);
	}
	else
	{
		$pourcentage_reduction = 0;
	}
	
	// Poids
	$weight = ($data['weight']);
	
	// URL produit
	$url = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'product.php?id_product='.$data['id_product'];
	$url_rewrite = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').$data['cat_link_rewrite'].'/'.$data['id_product'].'-'.$data['link_rewrite'].'.html';

	// URL image
	if($data['id_image'] > 0 or !empty($data['id_image']) && $data['link_rewrite'] !='')
	{
		//echo "compareVersion:".$compareVersion."\n";
		//echo "version:".$version."\n";
		if($compareVersion < 1)
		{
			if($version == '1.4.9.0')
			{
				$image = $link->getImageLink($data['link_rewrite'],$id_product.'-'.$data['id_image'],'large');
			}
			elseif($compareVersion < 0)
			{
				$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image'].'-large.jpg';
			}
			else
			{
				$image = 'http://'.$link->getImageLink($data['link_rewrite'],$id_product.'-'.$data['id_image'],'large');	
			}
		}
		else
		{
			if($LegacyImage  == 1)
			{
				$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image'].'-large_default.jpg';
			}
			else
			{
				$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($data['id_image']).'-large_default.jpg';
			}
		}
	}
	else
	{
		$image = '';
	}
	//echo "image:".$image."\n";

	//IMAGES SUPPLEMENTAIRE
	$sql_image_supp = 'SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'image pi WHERE pi.id_product='.mysql_escape_string($data['id_product']).' AND pi.cover<>1 ORDER BY POSITION ASC LIMIT 2';
	$liste_image_supp = Db::getInstance()->ExecuteS($sql_image_supp);
	if(count($liste_image_supp)>0)
	{
		if($liste_image_supp[0]['id_image'] > 0 or !empty($liste_image_supp[0]['id_image']))
		{
			if($compareVersion < 1)
			{
				if($version == '1.4.9.0')
				{
					$image2 = $link->getImageLink($data['link_rewrite'],$id_product.'-'.$liste_image_supp[0]['id_image'],'large');
				}
				elseif($compareVersion < 0)
				{
					$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[0]['id_image'].'-large.jpg';
				}
				else
				{
					$image2 = 'http://'.$link->getImageLink($data['link_rewrite'],$id_product.'-'.$liste_image_supp[0]['id_image'],'large');
				}
			}
			else
			{
				if($LegacyImage  == 1)
				{
					$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[0]['id_image'].'-large_default.jpg';
				}
				else
				{
					$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($liste_image_supp[0]['id_image']).'-large_default.jpg';
				}
			}
		}
		else
		{
			$image2 = '';
		}
		if($liste_image_supp[1]['id_image'] > 0 or !empty($liste_image_supp[1]['id_image']))
		{
			if($compareVersion < 1)
			{
				if($version == '1.4.9.0')
				{
					$image3 = $link->getImageLink($data['link_rewrite'],$id_product.'-'.$liste_image_supp[1]['id_image'],'large');
				}
				elseif($compareVersion < 0)
				{
					$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[1]['id_image'].'-large.jpg';
				}
				else
				{
					$image3 = 'http://'.$link->getImageLink($data['link_rewrite'],$id_product.'-'.$liste_image_supp[1]['id_image'],'large');
				}
			}
			else
			{
				if($LegacyImage  == 1)
				{
					$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[1]['id_image'].'-large_default.jpg';
				}
				else
				{
					$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($liste_image_supp[1]['id_image']).'-large_default.jpg';
				}
			}
		}
		else
		{
			$image3 = '';
		}
	}
	else
	{
		$image2 = '';
		$image3 = '';
	}
	
	//Product feature
	$product_feature = getProductFeature($data['id_product'], $id_lang);

	//Frais de port
	$fdp = $data['additional_shipping_cost'];
	if($mode == 0) //tranche prix
	{
		$fdp += fdp_prix($mode, $data_price, $price, $frais_manut, $free_prix, $tax_conf);
	}
	else // tranche poid
	{
		$fdp += fdp_poid($mode, $data_weight, $weight, $frais_manut, $free_poid, $tax_conf);
	}

	//Product Attribute
	$product_attibute = getProductAttributs(0, $id_lang, $liste_attribute_declinaison);
	
	//meta_keywords
	$meta_keywords = netoyage_html($data['meta_keywords']);
	
	//meta_description
	$meta_description = netoyage_html($data['meta_description']);
	
	//type de produit
	$product_type = getProductType($id_product);

	//type de variation - recup du 1 er enfant
	if($product_type =='parent')
	{
		$sql_enfant ='SELECT id_product_attribute FROM '.constant('_DB_PREFIX_').'product_attribute WHERE id_product ="'.mysql_escape_string($data['id_product']).'" ';
		$liste_enfant =  Db::getInstance()->ExecuteS($sql_enfant);
		$liste_enfant = $liste_enfant[0]['id_product_attribute'];
		$variation_type = getProductVariation($liste_enfant, $id_lang, $liste_attribute_declinaison);
	}
	else
		$variation_type = "";

	//Condition
	$condition = strtoupper($data['condition']);

	// INSERTION DE LA LIGNE PRODUIT
	if($data['price'] != 0)
	$buffer .= '"'.$id_product.'"|"'.$name.'"|"'.$reference.'"|"'.$supplier_reference.'"|"'.$manufacturer.'"|"'.$category.'"|"'.$description.'"|"'.$description_short.'"|"'.$price.'"|"'.$wholesale_price.'"|"'.$price_HT.'"|"'.$price_reduction.'"|"'.$pourcentage_reduction.'"|"'.$quantity.'"|"'.$weight.'"|"'.$ean13.'"|"'.$upc.'"|"'.$ecotax.'"|"'.$available_now.'"|"'.$url.'"|"'.$image.'"|"'.$fdp.'"|"'.$id_product.'"|"'.$delais_livraison.'"|"'.$image2.'"|"'.$image3.'"|"'.$data['from'].'"|"'.$data['to'].'"|"'.$meta_keywords.'"|"'.$meta_description.'"|"'.$url_rewrite.'"|"'.$product_type.'"|"'.$variation_type.'"|"'.$Currency.'"|"'.$condition.'"|'.$product_feature.$product_attibute."\r\n";
}

if(isset($_GET['mode']) && $_GET['mode'] == 'full')
{
	if($compareVersion < 0)//1.3 et inférieur
	{
		$sql_declinaison_produit = 'SELECT pa.id_product_attribute,p.id_product,p.reference as reference_produit,pa.reference as reference_produit_declinaison,p.supplier_reference,pa.supplier_reference as supplier_reference_declinaison, p.ean13, pa.ean13 as ean13_declinaison , pa.ecotax, pa.quantity, pl.id_lang as lang, ';
		$sql_declinaison_produit .= ' p.price as price, pa.price as price_attribute,t.rate as tax,p.reduction_percent, pa.wholesale_price, p.reduction_price, p.id_category_default as category_id, ';
		$sql_declinaison_produit .= ' p.reduction_from,p.reduction_to, ';
		$sql_declinaison_produit .='pl.description,pl.description_short,pl.name,pl.available_now,p.weight, pa.weight as poid_declinaison,m.name as manufacturer,';
		$sql_declinaison_produit .='(SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'image pi WHERE pi.id_product=p.id_product AND pi.cover=1 LIMIT 1) as id_image,';
		$sql_declinaison_produit .='(SELECT pai.id_image FROM '.constant('_DB_PREFIX_').'product_attribute_image pai WHERE pai.id_product_attribute=pa.id_product_attribute LIMIT 1) as id_image2,';
		$sql_declinaison_produit .='(SELECT cl.name FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
		$sql_declinaison_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product order by cp.position ASC LIMIT 1) as category ';
		$sql_declinaison_produit .='FROM '.constant('_DB_PREFIX_').'product p ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute pa ON p.id_product=pa.id_product ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'manufacturer m ON m.id_manufacturer=p.id_manufacturer ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=p.id_tax ';
		$sql_declinaison_produit .='WHERE pl.id_lang='.mysql_escape_string($id_lang).' AND p.active=1 ';
	}
	else
	{
		$sql_declinaison_produit = 'SELECT pa.id_product_attribute,p.id_product,p.reference as reference_produit,pa.reference as reference_produit_declinaison,p.supplier_reference,pa.supplier_reference as supplier_reference_declinaison, p.ean13, p.upc, pa.ean13 as ean13_declinaison, pa.upc as upc_declinaison , pa.ecotax, pl.id_lang as lang, pl.link_rewrite,';
		if($compareVersion == 1) //version 1.5.X
		{
			$sql_declinaison_produit .= ' sa.quantity, psupp.product_supplier_reference, ps.condition, ';
		}
		else
		{
			$sql_declinaison_produit .= ' pa.quantity, p.condition, ';
		}
		$sql_declinaison_produit .= ' p.price as price,p.id_tax_rules_group,t.rate as tax, p.additional_shipping_cost, pa.price as price_attribute,t.rate as tax, pa.wholesale_price, p.id_category_default as category_id, sp.reduction, sp.reduction_type, sp.from, sp.to, ';
		$sql_declinaison_produit .='pl.description,pl.description_short,pl.meta_description,pl.meta_keywords,pl.name,pl.available_now,p.weight, pa.weight as poid_declinaison,m.name as manufacturer,';
		$sql_declinaison_produit .='(SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'image pi WHERE pi.id_product=p.id_product AND pi.cover=1 LIMIT 1) as id_image,';
		$sql_declinaison_produit .='(SELECT pai.id_image FROM '.constant('_DB_PREFIX_').'product_attribute_image pai WHERE pai.id_product_attribute=pa.id_product_attribute LIMIT 1) as id_image2,';
		$sql_declinaison_produit .='(SELECT cl.name FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
		$sql_declinaison_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product order by cp.position ASC LIMIT 1) as category ';
		
		$sql_declinaison_produit .=',(SELECT cl.link_rewrite FROM '.constant('_DB_PREFIX_').'category_lang cl INNER JOIN '.constant('_DB_PREFIX_').'category_product cp ON cp.id_category=cl.id_category ';
		$sql_declinaison_produit .='WHERE cl.id_lang='.mysql_escape_string($id_lang).' AND cp.id_product=p.id_product AND cl.id_category=p.id_category_default order by cp.position ASC LIMIT 1) as cat_link_rewrite ';
		
		$sql_declinaison_produit .='FROM '.constant('_DB_PREFIX_').'product p ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute pa ON p.id_product=pa.id_product ';
		
		if($compareVersion == 1) //version 1.5.X
		{
			$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_shop ps ON p.id_product=ps.id_product ';
			// $sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'stock_available psa ON p.id_product=psa.id_product ';

			// $sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute_shop pas ON pa.id_product_attribute=pas.id_product_attribute ';
			$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'stock_available sa ON sa.id_product_attribute=pa.id_product_attribute ';
			$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_supplier psupp ON pa.id_product_attribute=psupp.id_product_attribute ';
		}

		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'product_lang pl ON p.id_product=pl.id_product ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'specific_price sp ON p.id_product=sp.id_product ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'manufacturer m ON m.id_manufacturer=p.id_manufacturer ';
		//tax sur le pays par defaut
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax_rule tr ON tr.id_tax_rules_group=p.id_tax_rules_group AND tr.id_country="'.$id_pays.'" ';
		$sql_declinaison_produit .='LEFT JOIN '.constant('_DB_PREFIX_').'tax t ON t.id_tax=tr.id_tax ';
		$sql_declinaison_produit .='WHERE pl.id_lang='.mysql_escape_string($id_lang).' ';
		
		if($compareVersion == 1 && $IdShop != '') //version 1.5.X
		{
			// $sql_declinaison_produit .= ' AND pas.id_shop="'.$IdShop.'" AND ps.active=1 ';
			$sql_declinaison_produit .= ' AND ps.id_shop="'.mysql_escape_string($IdShop).'" AND ps.active=1 ';
		}
		else
		{
			$sql_declinaison_produit .=' AND p.active=1 ';
		}

	}
	if(!empty($tab_cat_selected_bdd) && count($tab_cat_selected_bdd)>0)
	{
		$list_cat_selected_bdd = implode(',', $tab_cat_selected_bdd);
		$sql_declinaison_produit .= ' AND p.id_category_default in ('.$list_cat_selected_bdd.') ';
	}

	if(!empty($tab_produit_selected_bdd) && count($tab_produit_selected_bdd)>0)
	{
		$list_produit_selected_bdd = implode(',', $tab_produit_selected_bdd);
		$sql_declinaison_produit .= ' AND p.id_product in ('.$list_produit_selected_bdd.') ';
	}
	// $sql_declinaison_produit .= ' AND p.id_product =543 ';
	$sql_declinaison_produit .='GROUP BY pa.id_product_attribute';
	$liste_declinaison =  Db::getInstance()->ExecuteS($sql_declinaison_produit);
	// echo "-> ".$sql_declinaison_produit."<br>";

	// BOUCLE DECLINAISONS PRODUITS
	foreach($liste_declinaison as $data)
	{
		// echo "-->data['id_product_attribute']:".$data['id_product_attribute']."\n";
		// echo "-->quantity:".$Product->getQuantity($data['id_product'],$data['id_product_attribute'])."\n";
		//ID_PRODUCT
		$id_product = $data['id_product']."_".$data['id_product_attribute'];

		// Description longue
		$description = netoyage_html($data['description']);

		// Description courte
		$description_short = netoyage_html($data['description_short']);

		// Categorie
		$category = (getParentCategoy($data['category_id'], $id_lang));

		// Nom Produit
		if(!empty($data['id_product_attribute']))
		{
			$sql_feature_declinaison = 'SELECT agl.name, al.name AS valeur FROM '.constant('_DB_PREFIX_').'product_attribute pa ';
			$sql_feature_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute';
			$sql_feature_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute a ON pac.id_attribute = a.id_attribute';
			$sql_feature_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_lang al ON al.id_attribute = pac.id_attribute';
			$sql_feature_declinaison .= ' LEFT JOIN '.constant('_DB_PREFIX_').'attribute_group_lang agl ON agl.id_attribute_group = a.id_attribute_group';
			$sql_feature_declinaison .= ' WHERE al.id_lang ='.mysql_escape_string($id_lang).' AND agl.id_lang ='.mysql_escape_string($id_lang).' AND pa.id_product_attribute ='.mysql_escape_string($data['id_product_attribute']);
			$liste_feature_declinaison =  Db::getInstance()->ExecuteS($sql_feature_declinaison);

			$feature_declinaison =" ";
			foreach($liste_feature_declinaison as $data_feature_declinaison)
			{
				$feature_declinaison .= $data_feature_declinaison['name'].' '.$data_feature_declinaison['valeur'].' ';
			}
			$feature_declinaison = rtrim($feature_declinaison);
			$name = trim($data['name']).$feature_declinaison;
		}
		else
		{
			$name = trim($data['name']);
		}
		$name = str_replace('"',"'",$name);

		// Disponibilité
		$available_now = ($data['available_now']);
		
		// Ecotax
		$ecotax = ($data['ecotax']);
		
		// EAN
		if($data['ean13_declinaison'] !="")
		$ean13 = ($data['ean13_declinaison']);
		else
		$ean13 = ($data['ean13']);

		// UPC
		if($data['upc_declinaison'] !="")
		$upc = ($data['upc_declinaison']);
		else
		$upc = ($data['upc']);	

		// Référence
		if($data['reference_produit_declinaison'] !="")
		$reference = ($data['reference_produit_declinaison']);
		else
		$reference = ($data['reference_produit']);

		// Référence Fournisseur

		if($compareVersion == 1) //version 1.5.X
		{
			$supplier_reference = ($data['product_supplier_reference']);
		}
		else
		{
			if($data['supplier_reference_declinaison'] !="")
			$supplier_reference = ($data['supplier_reference_declinaison']);
			else
			$supplier_reference = ($data['supplier_reference']);
		}

		// Quantité
		$quantity = ($data['quantity']);
		// echo "quantity:".$quantity."\n";

		// Fabricant
		$manufacturer = ($data['manufacturer']);

		// Prix
		$price_tmp = $data['price']+$data['price_attribute'];
		$price = getPrice($price_tmp, $data['tax'], $IdCurrency);

		//Prix reduction
		if($compareVersion < 0)
		{
			$price_reduction = getSpecialPrice1_3($data,$IdCurrency);
		}
		else
		{
			$price_reduction = getSpecialPrice($data['id_product'], $price, $data['tax'], $data['reduction'], $data['reduction_type'], $IdCurrency);
		}
		
		// Prix achat HT - wholesale_price
		$wholesale_price = getPrice($data['wholesale_price'], 0, $IdCurrency);
		
		// Prix vente HT il faut enlever la TVA du prix de la déclinaison
		//$price_HT_tmp = ($price_tmp)-($price_tmp*$data['tax']/100);
		$price_HT_tmp = $price_tmp;
		$price_HT = getPrice($price_HT_tmp, 0, $IdCurrency);;

		// Pourcentage reduction
		if($data['reduction_type'] == 'percentage')
		{
			$pourcentage_reduction = getPourcentage($data['id_product']);
		}
		else
		{
			$pourcentage_reduction = 0;
		}

		
		// Poids
		$weight = ($data['weight']+$data['poid_declinaison']);
		// URL produit
		$url = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'product.php?id_product='.$data['id_product'];
		$url_rewrite = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').$data['cat_link_rewrite'].'/'.$data['id_product'].'-'.$data['link_rewrite'].'.html';

		// URL image
		if($data['id_image'] > 0 or !empty($data['id_image']))
		{
				if($data['id_image2'] > 0 or !empty($data['id_image2']))
				{
					if($compareVersion < 1)
					{
						if($version == '1.4.9.0')
						{
							$image = $link->getImageLink($data['link_rewrite'],$data['id_product']."-".$data['id_image2'],'large');
						}
						elseif($compareVersion < 0)
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image2'].'-large.jpg';
						}
						else
						{
							$image = 'http://'.$link->getImageLink($data['link_rewrite'],$data['id_product']."-".$data['id_image2'],'large');
						}			
					}
					else
					{
						if($LegacyImage  == 1)
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image2'].'-large_default.jpg';
						}
						else
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($data['id_image2']).'-large_default.jpg';
						}
					}
				}
				else
				{
					if($compareVersion < 1)
					{
						if($version == '1.4.9.0')
						{
							$image = $link->getImageLink($data['link_rewrite'],$data['id_product']."-".$data['id_image'],'large');
						}
						elseif($compareVersion < 0)
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image'].'-large.jpg';
						}
						else
						{
							$image = 'http://'.$link->getImageLink($data['link_rewrite'],$data['id_product']."-".$data['id_image'],'large');
						}
					}
					else
					{
						if($LegacyImage  == 1)
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$data['id_image'].'-large_default.jpg';
						}
						else
						{
							$image = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($data['id_image']).'-large_default.jpg';
						}
					}
				}
		}
		else{
			$image = '';
		}

		//FDP
		$fdp = $data['additional_shipping_cost'];
		if($mode == 0) //tranche prix
		{
			$fdp += fdp_prix($mode, $data_price, $price, $frais_manut, $free_prix, $tax_conf);
		}
		else // tranche poid
		{
			$fdp += fdp_poid($mode, $data_weight, $weight, $frais_manut, $free_poid, $tax_conf);
		}


		//IMAGES SUPPLEMENTAIRE
		$liste_image_supp = array();
		if(intval($data['id_product_attribute']) && $data['id_product_attribute']> 0)
		{
			$sql_image_supp = 'SELECT pi.id_image FROM '.constant('_DB_PREFIX_').'product_attribute_image pai ';
			$sql_image_supp .= ' LEFT JOIN '.constant('_DB_PREFIX_').'image pi ON pai.id_image = pi.id_image';
			$sql_image_supp .= ' WHERE pai.id_product_attribute='.mysql_escape_string($data['id_product_attribute']).' AND pi.cover<>1 ORDER BY POSITION ASC LIMIT 2 ';
			//echo "sql_image_supp:".$sql_image_supp."\n";
			$liste_image_supp = Db::getInstance()->ExecuteS($sql_image_supp);
		}
		
		if(count($liste_image_supp)>0)
		{
			if($liste_image_supp[0]['id_image'] > 0 or !empty($liste_image_supp[0]['id_image']))
			{
				if($compareVersion < 1)
				{
					if($version == '1.4.9.0')
					{
						$image2 = $link->getImageLink($data['link_rewrite'],$data['id_product']."-".$liste_image_supp[0]['id_image'],'large');
					}
					elseif($compareVersion < 0)
					{
						$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[0]['id_image'].'-large.jpg';
					}
					else
					{
						$image2 = 'http://'.$link->getImageLink($data['link_rewrite'],$data['id_product']."-".$liste_image_supp[0]['id_image'],'large');
					}
				}
				else
				{
					if($LegacyImage  == 1)
					{
						$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[0]['id_image'].'-large_default.jpg';
					}
					else
					{
						$image2 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($liste_image_supp[0]['id_image']).'-large_default.jpg';
					}
				}
			}
			else
			{
				$image2 = '';
			}
			if($liste_image_supp[1]['id_image'] > 0 or !empty($liste_image_supp[1]['id_image']))
			{
				if($compareVersion < 1)
				{
					if($version == '1.4.9.0')
					{
						$image3 = $link->getImageLink($data['link_rewrite'],$data['id_product']."-".$liste_image_supp[1]['id_image'],'large');
					}
					elseif($compareVersion < 0)
					{
						$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[1]['id_image'].'-large.jpg';
					}
					else
					{
						$image3 = 'http://'.$link->getImageLink($data['link_rewrite'],$data['id_product']."-".$liste_image_supp[1]['id_image'],'large');
					}
				}
				else
				{
					if($LegacyImage  == 1)
					{
						$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p/'.$data['id_product'].'-'.$liste_image_supp[1]['id_image'].'-large_default.jpg';
					}
					else
					{
						$image3 = 'http://'.$_SERVER['HTTP_HOST'].constant('__PS_BASE_URI__').'img/p'.decomposeIdImage($liste_image_supp[1]['id_image']).'-large_default.jpg';
					}
				}
			}
			else
			{
				$image3 = '';
			}
		}
		else
		{
			$image2 = '';
			$image3 = '';
		}

		//Product Attribute
		$product_attibute = getProductAttributs($data['id_product_attribute'], $id_lang, $liste_attribute_declinaison);
		// echo "product_attibute:".$product_attibute."\n";

		//Product feature
		$product_feature = getProductFeature($data['id_product'], $id_lang);
		// echo "product_feature:".$product_feature."\n";
		
		//meta_keywords
		$meta_keywords = netoyage_html($data['meta_keywords']);
		
		//meta_description
		$meta_description = netoyage_html($data['meta_description']);
		
		//type de produit
		$product_type = "child";

		//type de variation
		$variation_type = getProductVariation($data['id_product_attribute'], $id_lang, $liste_attribute_declinaison);
		// echo "variation_type:".$variation_type."\n";

		//Condition
		$condition = strtoupper($data['condition']);

		// INSERTION DE LA LIGNE PRODUIT
		if($data['id_product_attribute'] != NULL)
		// echo $id_product.'|'.$name.'|'.$reference.'|'.$supplier_reference.'|'.$manufacturer.'|'.$category.'|'.$description.'|'.$description_short.'|'.$price.'|'.$wholesale_price.'|'.$price_HT.'|'.$price_reduction.'|'.$pourcentage_reduction.'|'.$quantity.'|'.$weight.'|'.$ean13.'|'.$ecotax.'|'.$available_now.'|'.$url.'|'.$image.'||'.$fdp.'|'.$data['id_product'].'|'.$delais_livraison.'|'.$image2.'|'.$image3.'|'.$data['from'].'|'.$data['to'].'|'.$product_feature.$product_attibute.$meta_description.'|'.$meta_keywords.'|'.$url_rewrite.'|'."\r\n";
		$buffer .= '"'.$id_product.'"|"'.$name.'"|"'.$reference.'"|"'.$supplier_reference.'"|"'.$manufacturer.'"|"'.$category.'"|"'.$description.'"|"'.$description_short.'"|"'.$price.'"|"'.$wholesale_price.'"|"'.$price_HT.'"|"'.$price_reduction.'"|"'.$pourcentage_reduction.'"|"'.$quantity.'"|"'.$weight.'"|"'.$ean13.'"|"'.$upc.'"|"'.$ecotax.'"|"'.$available_now.'"|"'.$url.'"|"'.$image.'"|"'.$fdp.'"|"'.$data['id_product'].'"|"'.$delais_livraison.'"|"'.$image2.'"|"'.$image3.'"|"'.$data['from'].'"|"'.$data['to'].'"|"'.$meta_keywords.'"|"'.$meta_description.'"|"'.$url_rewrite.'"|"'.$product_type.'"|"'.$variation_type.'"|"'.$Currency.'"|"'.$condition.'"|'.$product_feature.$product_attibute."\r\n";
		// echo "\n\n";
	}
}
header('Content-Type: text/plain; charset=utf-8');

echo $buffer;

if((isset($_GET['debug'])) && ($_GET['debug'] ==1))
{
	echo "\n\n\n\n";
	if(isset($data_range_price[0]['delimiter1']) && ($mode == 0))
	{
		$flag_delim_prix = 'oui';
		print_r($data_range_price);
	}
	if(isset($data_range_weight[0]['delimiter1']) && ($mode == 1))
	{
		$flag_delim_poid = 'oui';
		print_r($data_range_weight);
	}
	echo "version Prestashop: ".$version."\n";
	echo "sub_verison:".$sub_verison."\n";
	echo "version Module: ".$E->version."\n";
	echo "id_lang:".$id_lang."\n";
	echo "IdCurrency:".$IdCurrency."\n";
	echo "Currency:".$Currency."\n";
	echo "Multi Shop:".getMultiShop()."\n";
	echo "IdShop:".$IdShop."\n";
	echo "LegacyImage:".$LegacyImage."\n";
	
	
	echo "Clientid_lengow:".get_Clientid_lengow()."\n";
	echo "Groupid_lengow:".get_Groupid_lengow()."\n\n";
	echo "Tracking_lengow:".get_Tracking_lengow()."\n\n";
	echo "_PS_SMARTY_DIR_:"._PS_SMARTY_DIR_."\n\n";
	echo "emplacement_template:"._PS_THEME_DIR_."order-confirmation.tpl"."\n\n";
	echo "PS_SMARTY_FORCE_COMPILE:".Configuration::get('PS_SMARTY_FORCE_COMPILE')."\n\n";
	
	
	echo "tax_conf:".$tax_conf."\n";
	echo "sql_produit:".$sql_produit."\n";
	echo "sql_declinaison_produit:".$sql_declinaison_produit."\n";
	echo "entete:".$entete;
	echo "entete_feature:".$entete_feature."\n";
	echo "entete_attribute:".$entete_attribute."\n\n\n\n";

	echo '#PS_SHIPPING_HANDLING - frais_manut: '.$frais_manut."\n"
	.'#PS_SHIPPING_FREE_PRICE: '.$free_prix."\n".'#PS_SHIPPING_FREE_WEIGHT: '.$free_poid."\n"
	.'#PS_SHIPPING_METHOD: '.$mode."\n".'#PS_CARRIER_DEFAULT: '.$id_carrier."\n"
	.'#PS_COUNTRY_DEFAULT: '.$id_pays."\n".'#PS_TAX: '.$id_tax_conf."\n"
	.'#flag_delim_prix: '.$flag_delim_prix."\n".'#flag_delim_poid: '.$flag_delim_poid."\n";
	
	
	echo "produit checked en base:"."\n";
	print_r($tab_produit_selected_bdd);
	echo "categories checked en base:"."\n";
	print_r($tab_cat_selected_bdd);
}

?>