<?php 
include("function.php");

if(isset($_POST['id_cat']))
{
	$tab_cat_selected_bdd = check_tab_parametre_lengow();
	$tab_cat_selected = $_POST['id_cat'];
	// INSERTION OU SUPPRESSION
	foreach($_POST['id_cat'] as $categorie)
	{
		if( intval($categorie) > 0 )
		{
			//verification de la presence ou non du cat_id en bdd
			$sql_verif_cat_id = 'SELECT count(*) as nb FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_valeur='.mysql_escape_string($categorie).' AND parametre_nom="cat_id"';
			$verif = Db::getInstance()->getRow($sql_verif_cat_id);
			//si pas en BDD, insertion
			if($verif['nb'] == 0)
			{
				$sql_insert_cat_id = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("cat_id", '.mysql_escape_string($categorie).')';
				Db::getInstance()->ExecuteS($sql_insert_cat_id);
			}
		}
	}
	//si en BDD et pas dans $_POST, suppression
	foreach($tab_cat_selected_bdd as $categorie_bdd)
	{
		if(!in_array($categorie_bdd, $_POST['id_cat']))
		{
			$sql_suppression_cat_id = 'DELETE FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="cat_id" AND parametre_valeur='.mysql_escape_string($categorie_bdd);
			Db::getInstance()->ExecuteS($sql_suppression_cat_id);
		}
	}
}
else
{
	// récupération des valeur de la table parametre_lengow
	$tab_cat_selected = check_tab_parametre_lengow();
}


if(isset($_POST['id_produit']))
{
	$tab_produit_selected_bdd = check_produit_parametre_lengow();
	$tab_produit_selected = $_POST['id_produit'];
	// INSERTION OU SUPPRESSION
	foreach($_POST['id_produit'] as $produit)
	{
		if( intval($produit) > 0 )
		{
			//verification de la presence ou non du id_produit en bdd
			$sql_verif_produit_id = 'SELECT count(*) as nb FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_valeur='.mysql_escape_string($produit).' AND parametre_nom="product_id"';
			$verif = Db::getInstance()->getRow($sql_verif_produit_id);
			//si pas en BDD, insertion
			if($verif['nb'] == 0)
			{
				$sql_insert_produit_id = 'INSERT INTO '.constant('_DB_PREFIX_').'parametre_lengow (parametre_nom, parametre_valeur) VALUES ("product_id", '.mysql_escape_string($produit).')';
				Db::getInstance()->ExecuteS($sql_insert_produit_id);
			}
		}
	}

	//si en BDD et pas dans $_POST, suppression
	foreach($tab_produit_selected_bdd as $produit_bdd)
	{
		if(!in_array($produit_bdd, $_POST['id_produit']))
		{
			$sql_suppression_produit_id = 'DELETE FROM '.constant('_DB_PREFIX_').'parametre_lengow WHERE parametre_nom="product_id" AND parametre_valeur='.mysql_escape_string($produit_bdd);
			Db::getInstance()->ExecuteS($sql_suppression_produit_id);
		}
	}
}
else
{
	// récupération des valeur de la table parametre_lengow
	$tab_produit_selected = check_produit_parametre_lengow();
}

// ID CLIENT LENGOW
$msg_erreur = '';
$msg_erreur_group = '';

if(isset($_POST['Groupid_lengow'])  && $_POST['Groupid_lengow']!='')
{
	$Groupid_lengow = $_POST['Groupid_lengow'];
	if( ($Groupid_lengow>0) && is_numeric($Groupid_lengow))
	{
		set_Groupid_lengow($Groupid_lengow);
	}
	else
	{
		$msg_erreur_group = "<span style='color:red;'>".$this->l('Your Group ID must be an integer > 0')."</span>";
	}
}
else
{
	$_POST['Groupid_lengow'] = get_Groupid_lengow();
}

if(isset($_POST['Tracking_lengow']) && $_POST['Tracking_lengow']!='')
{
	$Tracking_lengow = $_POST['Tracking_lengow'];
	if($Tracking_lengow !='')
	{
		set_Tracking_lengow($Tracking_lengow);
	}
}
else
{
	$_POST['Tracking_lengow'] = get_Tracking_lengow();
}

if(isset($_POST['Clientid_lengow']) && $_POST['Clientid_lengow']!='')
{
	$Clientid_lengow = $_POST['Clientid_lengow'];
	if( ($Clientid_lengow>0) && is_numeric($Clientid_lengow))
	{
		set_Clientid_lengow($Clientid_lengow);
	}
	else
	{
		$msg_erreur = "<span style='color:red;'>".$this->l('Your Client ID must be an integer > 0')."</span>";
	}
}
else
{
	$_POST['Clientid_lengow'] = get_Clientid_lengow();
}


//LANGUE DE L EXPORT
if(isset($_POST['lang_id']) && $_POST['lang_id']!='')
{
	set_Lang_id($_POST['lang_id']);
}

$html = "<script type='text/javascript'>
function toggle_produit_cat(produit)
{
//alert('produit:'+document.getElementById(produit));
	if(document.getElementById(produit).style.display  == 'block')
		document.getElementById(produit).style.display  = 'none';
	else
		document.getElementById(produit).style.display  = 'block';
}

function check_cat(cat)
{
	if(!document.getElementById(cat).checked)
		document.getElementById(cat).checked = true;
}

function check_produit_cat(cat, classNom)
{
	var chkbox = document.getElementsByTagName('input');
	for(var i=0; i<chkbox.length; i++)
	{
		if(chkbox[i].type =='checkbox')
		{
			if(chkbox[i].className == classNom)
			{
				if(cat.checked)
				{
					chkbox[i].checked = true;
				}
				else
				{
					chkbox[i].checked = false;
				}
			}
		}
	}
}

function checkAllBox(span)
{
	//alert('checkALL:'+span.innerHTML);
	var txt = span.innerHTML;
	var chkbox = document.getElementsByTagName('input');
	if(txt == '".$this->l("Check All")."')
	{
		span.innerHTML = '".$this->l("Uncheck All")."';
		//alert('checkALL:'+span.innerHTML);
	}
	else
	{
		span.innerHTML = '".$this->l("Check All")."';
		//alert('checkALL:'+span.innerHTML);
	}
	
	for(var i=0; i<chkbox.length; i++)
	{
		if(chkbox[i].type =='checkbox')
		{
			if(txt == '".$this->l("Check All")."')
			{
					chkbox[i].checked = true;
			}
			else
			{
				chkbox[i].checked = false;
			}
		}
	}
}
</script>";

$html .= '<form action="" method="POST" name="lengow_cat_selection">';
$html .="<br/><fieldset>
<legend>".$this->l("Choose the language to export your catalog: ")."</legend>";
$html .= fieldLangue();
$html .='</fieldset>';
$html .="<br/><fieldset>
<legend>".$this->l("You are a Lengow's custumer? Please enter your client ID: ")."</legend>";
$html .=$this->l('Stating your client ID will place the conversion tag automatically on your order confirmation page.').'<br /><br />';
$html .= fieldClientLengow($_POST['Clientid_lengow'], $this->l("Custumer ID"), $msg_erreur);
$html .= fieldClientGroupLengow($_POST['Groupid_lengow'], $this->l("group ID"), $msg_erreur_group);
$html .= fieldTrackingLengow($_POST['Tracking_lengow'], $this->l("Choose your tracking method"));
$html .='<br /><b>'.$this->l('Where can I find my customer and group IDs?').'</b><br />'.$this->l('You can find your IDs in your Lengow\'s account page').' : <a href="https://solution.lengow.com/compte/informations/" target="_blank">https://solution.lengow.com/compte/informations/</a>';
$html .='<br /><br /><b>'.$this->l('The TagCapsule option is available only under contract, if you want to subscribe it, please contact our sales team.').'</b> commercial@lengow.com';
$html .='</fieldset>';
$html .="<br/><fieldset><legend>".$this->l('Choose your categories and products to export: ')."</legend>";
$html .='<b>'.$this->l('Products default categories are used in this tree, if you do not find a product, check its default category value, the value "Home" cannot be set as default category.').'</b><br />';
$html .= getTree($tab_cat_selected, $tab_produit_selected, $this->l('Product'), $this->l("Check All"));
$html .='</fieldset>';
$html .= '<br/><span align="center"><input value="'.$this->l('Save').'" type="submit" name="save_param" class="button" /></span>';
$html .= '</form>';

echo $html;

?>