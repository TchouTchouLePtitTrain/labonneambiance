Les modifications de la base par rapport à un prestashop normal
ALTER TABLE  `ps_product` ADD  `video` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL

Les modifications du code :
Dans admin648795\themes\witty\template\controllers\products\images.tpl
Ajouter les lignes 75 à 81 suivantes :
		<tr><td colspan="2" style="padding-bottom:10px;"><div class="separation"></div></td></tr>
		<tr>
			<td class="col-left"><label>{l s='Video:'}</label></td>
			<td style="padding-bottom:5px;">
				<input size="55" maxlength="255" type="text" name="video" id="video" value="{$product->video|escape:html:'UTF-8'}" style="width: 350px; margin-right: 5px;" />
			</td>
		</tr>