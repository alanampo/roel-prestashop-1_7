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
    {if !empty($arr_result[0]['id'])}
    <div class='container-fluid tvcmsadvance-block' {* data-bg-url="{$AdvanceBlockImgpath_bk}{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BACK_IMG',$id_lang)}" *}>
        {*<div class="tvcmsadvance-block-sub-title">
            <h1>{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE',$id_lang)}</h1>
        </div> *}
        <div class='container tvadvance-block'>
            <div class='tvcmsadvanceblock-slider-main-title-wrapper'>
                {include file='_partials/tvcms-main-title.tpl' main_heading=$main_heading path=$dis_arr_result['path']}
            </div>
            <div class="tvadvance-block-desc">
                {Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_DESCRIPTION',$id_lang) nofilter}
            </div>
            <div class="tvadvance-block-sub-description">
                {Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_DESCRIPTION',$id_lang) nofilter}
            </div>
            <div class="tvadvance-block-wrapper">
                <div class="tv-advance-block-image">
                    <img src="{$AdvanceBlockImgpath}{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_IMG',$id_lang)}" alt="">
                </div>
                <div class="tv-advance-block-info-content">
                    <div class="tv-advance-block-info-subtitle">{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_SUB_TITLE',$id_lang)}</div>
                    <div class="tv-advance-block-info-title">{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_TITLE',$id_lang)}</div>
                </div>
            </div>
            <div class="tvcmsadvance-block-content row">
                {foreach $arr_result as $data}
                {if $data['status'] == '1'}
                <div class="tvadvance-block-content col-md-3">
                    {if $show_fields['image']}
                    <div class="tvadvance-block-content-img">
                        <img src="{$AdvanceBlockImgpath}{$data['image']}" alt="{$data['lang_info'][$id_lang]['title']}" />
                    </div>
                    {/if}
                    <div class="tvadvance-block-content-details">
                        {if $show_fields['title']}
                        <div class="tvadvance-block-content-title">
                            {$data['lang_info'][$id_lang]['title']}
                        </div>
                        {/if}
                    </div>
                </div>
                {/if}
                {/foreach}
                {if Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION',$id_lang) && $show_fields['main_block_btn_caption']}
                <div class="tvadvance-bolck-btn-link-wrapper">
                    <a href="{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_LINK',$id_lang)}" class="btn tvadvance-bolck-btn-link tvadvance-bolck-btn">
                        <span>{Configuration::get('TVCMSADVANCEBLOCK_MAIN_BLOCK_BTN_CAPTION',$id_lang)}</span>
                    </a>
                </div>
                {/if}
            </div>
        </div>
    </div>
    {/if}
    {/strip}