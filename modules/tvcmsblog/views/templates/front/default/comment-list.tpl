{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    {strip}
    <div class="comments_area" id="comments">
        {if count($tvcmsblog_commets) > 0}
        <div class="comments_title">
            {l s='All Feedback' mod='tvcmsblog'}
        </div>
        {/if}
        <ol class="comment_list">
            {foreach from=$tvcmsblog_commets item=tvcmsblog_commet}
            <li class="comment" id="comment_{$tvcmsblog_commet.id_tvcms_comments}">
                <article class="comment_body clearfix">
                    <div class="comment_author vcard">
                        {* <img alt="" class="tvcmsblog_img avatar avatar-70 photo" height="70" src="{$AvtarPath}avtar.png" width="70"> *}
                        <div class="comment_meta comment_meta_author">
                            <i class="material-icons">&#xe853;</i>
                            <b class="fn">{$tvcmsblog_commet.name}</b>
                        </div>
                        {* <div class="reply">
                            <a aria-label="Reply to raihan@sntbd.com" class="comment-reply-link" href="#" onclick='return addComment.moveForm( "div-comment-3", "3", "respond", "38" )' rel="nofollow">
                                Reply
                            </a>
                        </div> *}
                    </div>
                    <div class="comment_content">
                        <div class="comment_meta_date">
                            <i class="material-icons">&#xe878;</i>
                            <span>{l s='Reviewed on' mod='tvcmsblog'}</span>
                            <time datetime="2016-03-07T04:33:23+00:00">{$tvcmsblog_commet.created|date_format:"%b %e, %Y"}
                            </time>
                        </div>
                        <div class="comment_content_bottom">
                            <i class="material-icons">&#xe8af;</i>
                            <p>
                                {$tvcmsblog_commet.content}
                            </p>
                        </div>
                    </div>
                </article>
            </li>
            {/foreach}
        </ol>
    </div>
    {/strip}