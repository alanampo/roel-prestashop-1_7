{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2022 PrestaShop SA
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{strip}
    {* {block name='header_banner'}
    <div class="tvcmsheader-banner">
        {hook h='displayBanner'}
    </div>
    {/block} *}
    {block name='header_nav'} 
    {/block}
    {block name='header_top'}
    {if Context::getContext()->getDevice() == 1}
        {* for Desktop *}
        {include file='_partials/desktop-header.tpl' withData=true}
        {include file='_partials/mobile-header.tpl' withData=false}
    {else}
        {* for mobile *}
        {include file='_partials/desktop-header.tpl' withData=false}
        {include file='_partials/mobile-header.tpl' withData=true}
    {/if}
    {hook h='displayNavFullWidth'}
    {/block}
{/strip}

{literal}
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XET4712C4P"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XET4712C4P');
</script>
{/literal}

{literal}
<!-- Sendinblue Conversations {literal} -->
<script>
    (function(d, w, c) {
        w.SibConversationsID = '61ae85df989f4973e279ad33';
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        var s = d.createElement('script');
        s.async = true;
        s.src = 'https://conversations-widget.sendinblue.com/sib-conversations.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'SibConversations');
</script>
<!-- /Sendinblue Conversations {/literal} -->
{/literal}

{literal}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "url": "https://tienda.roelplant.cl/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://tienda.roelplant.cl/busqueda?controller=search&s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>
{/literal}

{literal}
<script type="text/javascript">
    (function() {
        window.sib = {
            equeue: [],
            client_key: "5mcjez6o3ydoe3x2okvkr098"
        };
        /* OPTIONAL: email for identify request*/
        // window.sib.email_id = 'example@domain.com';
        window.sendinblue = {};
        for (var j = ['track', 'identify', 'trackLink', 'page'], i = 0; i < j.length; i++) {
        (function(k) {
            window.sendinblue[k] = function() {
                var arg = Array.prototype.slice.call(arguments);
                (window.sib[k] || function() {
                        var t = {};
                        t[k] = arg;
                        window.sib.equeue.push(t);
                    })(arg[0], arg[1], arg[2]);
                };
            })(j[i]);
        }
        var n = document.createElement("script"),
            i = document.getElementsByTagName("script")[0];
            n.type = "text/javascript", n.id = "sendinblue-js",
            n.async = !0, n.src = "https://sibautomation.com/sa.js?key="+ window.sib.client_key,
            i.parentNode.insertBefore(n, i), window.sendinblue.page();
    })();
    </script>
{/literal}