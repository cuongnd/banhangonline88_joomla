function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
  {
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}

function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value + "; path=/";
}

function checkCookie()
{
 var disclaimercookie=getCookie("disclaimercookie");
 if (disclaimercookie!=null && disclaimercookie!="") return;
 else
 {

 jQ(document).ready(function() {
	var id = '#dialog';

	// Overlay size
	var overlayHeight = jQ(document).height();
	var overlayWidth = jQ(window).width();
    
	// Transition
	jQ('#overlay').fadeIn(1000);	
	jQ('#overlay').fadeTo("slow",0.9);	

	// Windows size
	var winH = jQ(window).height();
	var winW = jQ(window).width();

	// Center popup
	jQ(id).css('top',  winH/2-jQ(id).height()/2);
	jQ(id).css('left', winW/2-jQ(id).width()/2);

	// Transition effect
	jQ(id).fadeIn(1500); 	

	// If the close button is clicked on
	jQ('.window .enter').click(function (e) {
		
		e.preventDefault();
		
		jQ('#overlay').fadeOut(1000);
		jQ('.window').fadeOut(1000);
	});		
	
	//On click on overlay, blink effect
	jQ('#overlay').click(function () {
	//	jQ('#dialog').effect("pulsate", { times:4 }, 600);
	//	jQ(this).hide();
		jQ('#dialog').fadeOut(200);
		jQ('#dialog').fadeIn(100);
		jQ('#dialog').fadeOut(200);
		jQ('#dialog').fadeIn(100);
	});

	// Click on open button
	jQ('#disclaimer_open').click(function () {
        console.log(disclaimer_duration);
		setCookie('disclaimercookie','ok',disclaimer_duration);
	});

 });
 }
}
//Check disclaimer already done
window.onload = checkCookie();
