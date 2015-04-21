	
	<ul id="manufacturers_list_img" class="manu-slider orientation-{$orientation} jcarousel-skin-{$skin}">
	{foreach from=$manufacturers item=manufacturer name=manufacturers}
		<li class="clearfix {if $smarty.foreach.manufacturers.first}first_item{elseif $smarty.foreach.manufacturers.last}last_item{else}item{/if}"> 
			<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
				<img src="{$img_manu_dir}{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}{$manu_size}.jpg" alt="{$manufacturer.name}" />
			</a>
		</li>
	{/foreach}
	</ul>
	
