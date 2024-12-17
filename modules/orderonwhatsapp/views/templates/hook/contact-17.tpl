{*
*  2012-2023 Weblir
*
*  @author    weblir <contact@weblir.com>
*  @copyright 2012-2023 weblir
*  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
*  International Registered Trademark & Property of weblir.com
*
*  You are allowed to modify this copy for your own use only. You must not redistribute it. License
*  is permitted for one Prestashop instance only but you can install it on your test instances.
*}

{if $WL_OOW_DISPLAY_TYPE == 1}
<a target="_blank" href="https://wa.me/{$WL_OOW_WNUMBER}" title="{$WL_OOW_CONTACT_TEXT}" class="whatsapp-button position-{$WL_OOW_DISPLAY_POS}">
	<img src="{$img_path}/views/img/wpp-icon.png" width="40">
</a>
{elseif $WL_OOW_DISPLAY_TYPE == 2}
<!-- wpp-btn-mobile -->
<div class="phone-call cbh-phone cbh-green cbh-show cbh-static position-{$WL_OOW_DISPLAY_POS}" id="clbh_phone_div">
	<a id="WhatsApp-button" href="https://wa.me/{$WL_OOW_WNUMBER}" target="_blank" class="phoneJs" title="{$WL_OOW_CONTACT_TEXT}">
		<div class="cbh-ph-circle"></div>
		<div class="cbh-ph-circle-fill"></div>
		<div class="cbh-ph-img-circle1" style="background-image: url({$img_path}/views/img/wpp-icon.png);"></div>
	</a>
</div>
<!-- wpp-btn-mobile -->
{elseif $WL_OOW_DISPLAY_TYPE == 3}
<div class="whats-float position-{$WL_OOW_DISPLAY_POS}">
	{if $WL_OOW_DISPLAY_POS == 'left'}
	    <a target="_blank" href="https://wa.me/{$WL_OOW_WNUMBER}" title="{$WL_OOW_CONTACT_TEXT}">
	        <span>{$WL_OOW_CONTACT_TEXT}</span> <img src="{$img_path}/views/img/wpp-icon.png" width="45">
	    </a>
	{else}
		<a target="_blank" href="https://wa.me/{$WL_OOW_WNUMBER}" title="{$WL_OOW_CONTACT_TEXT}">
	        <img src="{$img_path}/views/img/wpp-icon.png" width="45"> <span>{$WL_OOW_CONTACT_TEXT}</span>
	    </a>
	{/if}
</div>
{elseif $WL_OOW_DISPLAY_TYPE == 4}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">

<div class="nav-bottom position-{$WL_OOW_DISPLAY_POS}">
    <div class="popup-whatsapp fadeIn">
        <div class="content-whatsapp -top"><button type="button" class="closePopup">
              <i class="material-icons icon-font-color">close</i>
            </button>
            <p>{$WL_OOW_CONTACT_TEXT}</p>
        </div>
        <div class="content-whatsapp -bottom">
          <input class="whats-input" id="whats-in" type="text" Placeholder="{l s='Send message...' mod='orderonwhatsapp'}" />
            <button class="send-msPopup" id="send-btn" type="button">
                <i class="material-icons icon-font-color--black">send</i>
            </button>

        </div>
    </div>
    <button type="button" id="whats-openPopup" class="whatsapp-button" title="{$WL_OOW_CONTACT_TEXT}">
        <img class="icon-whatsapp" src="{$img_path}/views/img/wa.svg">
    </button>
    <div class="circle-anime"></div>
</div>
<script type="text/javascript">
popupWhatsApp = () => {
  
  let btnClosePopup = document.querySelector('.closePopup');
  let btnOpenPopup = document.querySelector('.whatsapp-button');
  let popup = document.querySelector('.popup-whatsapp');
  let sendBtn = document.getElementById('send-btn');
  let is_closed = getCookie('waopened');

  btnClosePopup.addEventListener("click",  () => {
    popup.classList.toggle('is-active-whatsapp-popup');
    setCookie('waopened', "0", 3);
  })
  
  btnOpenPopup.addEventListener("click",  () => {
    popup.classList.toggle('is-active-whatsapp-popup')
     popup.style.animation = "fadeIn .6s 0.0s both";
     setCookie('waopened', "1", 3);
  })
  
  sendBtn.addEventListener("click", () => {
  let msg = document.getElementById('whats-in').value;
  let relmsg = msg.replace(/ /g,"%20");
   window.open('https://wa.me/{$WL_OOW_WNUMBER}?text='+relmsg, '_blank'); 
  
  });

  if (is_closed == null || is_closed != '0') {
	  setTimeout(() => {
	    popup.classList.toggle('is-active-whatsapp-popup');
	  }, 3000);
  }
	  
}

popupWhatsApp();

function setCookie(name, value, days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}


</script>


{elseif $WL_OOW_DISPLAY_TYPE == 5}
<div id="WAButton"></div>

<script type="text/javascript">
setTimeout(
  function() 
  {
    
    $(function() {
  $('#WAButton').floatingWhatsApp({
    phone: "{$WL_OOW_WNUMBER}",
    headerTitle: "{$WL_OOW_CONTACT_TEXT}", //Popup Title
    popupMessage: "{l s='Hello, how can we help you?' mod='orderonwhatsapp'}",
    showPopup: true, //Enables popup display
    buttonImage: '<img src="{$img_path}/views/img/whatsapp.svg" />',
    //headerColor: 'crimson', //Custom header color
    //backgroundColor: 'crimson', //Custom background button color
    position: "{$WL_OOW_DISPLAY_POS}"    
  });
});
}, 2000);
</script>
{elseif $WL_OOW_DISPLAY_TYPE == 6}
{literal}
<script type="text/javascript">
(() => {
	class Whatsapp {
		constructor({
			title = "{/literal}{$WL_OOW_CONTACT_TEXT}{literal}",
			subtitle = "{/literal}{l s='Chat with us on WhatsApp' mod='orderonwhatsapp'}{literal}",
			agents = [],
		}) {
			(this.t = title),
			(this.s = subtitle),
			(this.a = agents)
				this.render();
		}
		agent({ cta: c, name: n, hours: h, phone: p }) {
			return `<a href="#" data-phone="${p}" class="wa-w_a_a js-owaa" title="${n} ${p}">
      <span class="wa-w_a_a_i"></span>
      <span class="wa-w_a_a_c">
        <span class="a_t">${n}</span>
        <span class="a_s">${h}</span>
        <span class="a_c">${c}<span class="a_c_i"></span></span>
      </span>
    </a>`;
		}
		header() {
			return `<header class="wa-w_h">
        <span class="wa-w_h_t">${this.t}</span>
        <span class="wa-w_h_s">
          <span class="wa-w_h_i"></span>
          ${this.s}
        </span>
      </header>`;
		}
		render() {
			if (!this.a.length) return;
			let a = "";
			for (const s of this.a) a += this.agent(s);
			$("body").append(
				`<div class="wa-w position-{/literal}{$WL_OOW_DISPLAY_POS}{literal}">
          ${this.header()}
          <section class="wa-w_a">${a}</section>
          <button class="wa-w_b" title="${this.t}"></button>
        </div>`
			),
			$(document)
				.on({ click: () => $(".wa-w").toggleClass("open") }, ".wa-w_b")
				.on({
					click: (e) => {
						e.preventDefault(), this.openAgent(e.currentTarget.dataset);
					},
				}, ".js-owaa");
		}
		openAgent({ phone }) {
				window.open(
					`https://wa.me/${phone.replace(/ /g, "").replace("+", "")}`,
					"_blank"
				);
		}
	}
  window.onload = () => {
    new Whatsapp({
      agents:[
      {/literal}
      	{foreach from=$wa_agent_list item=single_agent}
      	{literal}
		    {
		        name:"{/literal}{$single_agent.name nofilter}{literal}",
		        phone:"{/literal}{$single_agent.wa_number nofilter}{literal}",
		        hours:`{/literal}{$single_agent.availability nofilter}{literal}`,
		        cta:"{/literal}{$single_agent.cta nofilter}{literal}"
		    },
		{/literal}
		{/foreach}
      {literal}
      ]
    })
  }
})();
</script>
{/literal}
{else}

{/if}