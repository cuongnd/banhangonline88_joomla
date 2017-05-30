jQuery("input[class='size']").change( function() {
	name = jQuery(this).attr('name');
	name = name.replace('_size', '');
	value = parseInt(this.value);
	if(!isNaN(value)) {
		jQuery("#" + name).css("font-size", value + "px");
	}
});

jQuery("input[name='qrcode_size']").change( function() {
	value = parseInt(this.value);
	if(!isNaN(value)) {
		jQuery("#qrcode").css("width", value + "px");
		jQuery("#qrcode").css("height", value + "px");
		jQuery("#qrcode_image").attr("src", "https://chart.googleapis.com/chart?chs=" + value + "x" + value + "&cht=qr&chl=<?php echo $sampleCode; ?>");
	}
});

/* ----------------------------------- */

jQuery("input[class='alignment']").change( function() {
	name = jQuery(this).attr('name');
	name = name.replace('_alignment', '');
	jQuery("#" + name).css("text-align", this.value);

});

/* ----------------------------------- */

jQuery("input[class='control']").change( function() {
	name = jQuery(this).attr('name');
	name = name.replace('_control', '');

	if(this.value == 'false') {
		jQuery("#" + name).css("visibility", "hidden");
	}
	else if(this.value == 'true') {
		jQuery("#" + name).css("visibility", "visible");
	}

});

/* ----------------------------------- */

jQuery(function()
{
	jQuery("#qrcode").draggable();
	jQuery("#couponcode").draggable();
	jQuery("#couponcode").resizable();
	jQuery("#recipient").resizable();
	jQuery("#recipient").draggable();
	jQuery("#option").resizable();
	jQuery("#option").draggable();
	jQuery("#shortdesc").resizable();
	jQuery("#shortdesc").draggable();
	jQuery("#couponexp").resizable();
	jQuery("#couponexp").draggable();
	jQuery("#highlights").resizable();
	jQuery("#highlights").draggable();
	jQuery("#terms").resizable();
	jQuery("#terms").draggable();
	jQuery("#price").resizable();
	jQuery("#price").draggable();
	jQuery("#originalprice").resizable();
	jQuery("#originalprice").draggable();
	jQuery("#advanceprice").resizable();
	jQuery("#advanceprice").draggable();
	jQuery("#remainprice").resizable();
	jQuery("#remainprice").draggable();
});

/* ----------------------------------- */

function generateResult()
{
	jQuery("#json").val(
		'{"qrcode":{"size": "' + jQuery("div#qrcode").width()
			+ '","top": "' + jQuery("div#qrcode").position().top
			+ '","left": "' + jQuery("div#qrcode").position().left
			+ '","visible": "' + jQuery("input[name='qrcode_control']:checked").val()
			+ '"},"couponcode":{"width": "' + jQuery("div#couponcode").width()
			+ '","height": "' + jQuery("div#couponcode").height()
			+ '","top": "' + jQuery("div#couponcode").position().top
			+ '","left": "' + jQuery("div#couponcode").position().left
			+ '","fontsize": "' + jQuery("div#couponcode").css("font-size")
			+ '","align": "' +jQuery("input[name='couponcode_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='couponcode_control']:checked").val()
			+ '"},"recipient":{"width": "' + jQuery("div#recipient").width()
			+ '","height": "' + jQuery("div#recipient").height()
			+ '","top": "' + jQuery("div#recipient").position().top
			+ '","left": "' + jQuery("div#recipient").position().left
			+ '","fontsize": "' + jQuery("div#recipient").css("font-size")
			+ '","align": "' +jQuery("input[name='recipient_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='recipient_control']:checked").val()
			+ '"},"option":{"width": "' + jQuery("div#option").width()
			+ '","height": "' + jQuery("div#option").height()
			+ '","top": "' + jQuery("div#option").position().top
			+ '","left": "' + jQuery("div#option").position().left
			+ '","fontsize": "' + jQuery("div#option").css("font-size")
			+ '","align": "' + jQuery("input[name='option_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='option_control']:checked").val()
			+ '"},"shortdesc":{"width": "' + jQuery("div#shortdesc").width()
			+ '","height": "' + jQuery("div#shortdesc").height()
			+ '","top": "' + jQuery("div#shortdesc").position().top
			+ '","left": "' + jQuery("div#shortdesc").position().left
			+ '","fontsize": "' + jQuery("div#shortdesc").css("font-size")
			+ '","align": "' +jQuery("input[name='shortdesc_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='shortdesc_control']:checked").val()
			+ '"},"highlights":{"width": "' + jQuery("div#highlights").width()
			+ '","height": "' + jQuery("div#highlights").height()
			+ '","top": "' + jQuery("div#highlights").position().top
			+ '","left": "' + jQuery("div#highlights").position().left
			+ '","fontsize": "' + jQuery("div#highlights").css("font-size")
			+ '","align": "' +jQuery("input[name='highlights_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='highlights_control']:checked").val()
			+ '"},"terms":{"width": "' + jQuery("div#terms").width()
			+ '","height": "' + jQuery("div#terms").height()
			+ '","top": "' + jQuery("div#terms").position().top
			+ '","left": "' + jQuery("div#terms").position().left
			+ '","fontsize": "' + jQuery("div#terms").css("font-size")
			+ '","align": "' +jQuery("input[name='terms_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='terms_control']:checked").val()
			+ '"},"couponexp":{"width": "' + jQuery("div#couponexp").width()
			+ '","height": "' + jQuery("div#couponexp").height()
			+ '","top": "' + jQuery("div#couponexp").position().top
			+ '","left": "' + jQuery("div#couponexp").position().left
			+ '","fontsize": "' + jQuery("div#couponexp").css("font-size")
			+ '","align": "' +jQuery("input[name='couponexp_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='couponexp_control']:checked").val()
			+ '"},"price":{"width": "' + jQuery("div#price").width()
			+ '","height": "' + jQuery("div#price").height()
			+ '","top": "' + jQuery("div#price").position().top
			+ '","left": "' + jQuery("div#price").position().left
			+ '","fontsize": "' + jQuery("div#price").css("font-size")
			+ '","align": "' +jQuery("input[name='price_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='price_control']:checked").val()
			+ '"},"originalprice":{"width": "' + jQuery("div#originalprice").width()
			+ '","height": "' + jQuery("div#originalprice").height()
			+ '","top": "' + jQuery("div#originalprice").position().top
			+ '","left": "' + jQuery("div#originalprice").position().left
			+ '","fontsize": "' + jQuery("div#originalprice").css("font-size")
			+ '","align": "' +jQuery("input[name='originalprice_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='originalprice_control']:checked").val()
			+ '"},"advanceprice":{"width": "' + jQuery("div#advanceprice").width()
			+ '","height": "' + jQuery("div#advanceprice").height()
			+ '","top": "' + jQuery("div#advanceprice").position().top
			+ '","left": "' + jQuery("div#advanceprice").position().left
			+ '","fontsize": "' + jQuery("div#advanceprice").css("font-size")
			+ '","align": "' +jQuery("input[name='advanceprice_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='advanceprice_control']:checked").val()
			+ '"},"remainprice":{"width": "' + jQuery("div#remainprice").width()
			+ '","height": "' + jQuery("div#remainprice").height()
			+ '","top": "' + jQuery("div#remainprice").position().top
			+ '","left": "' + jQuery("div#remainprice").position().left
			+ '","fontsize": "' + jQuery("div#remainprice").css("font-size")
			+ '","align": "' +jQuery("input[name='remainprice_alignment']:checked").val()
			+ '","visible": "' + jQuery("input[name='remainprice_control']:checked").val()
			+ '"}}');
}