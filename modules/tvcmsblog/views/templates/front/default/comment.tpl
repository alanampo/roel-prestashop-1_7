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
    <div class="comment_respond clearfix m_bottom_50 tvcms-blog-inner-page" id="respond">
        <h3 class="comment_reply_title" id="reply-title">
            {l s='Leave a Reply' mod='tvcmsblog'}
            <small>
                <a href="/wp_showcase/wp-supershot/?p=38#respond" id="cancel-comment-reply-link" rel="nofollow" style="display:none;">
                    {l s='Cancel reply' mod='tvcmsblog'}
                </a>
            </small>
        </h3>
        <form class="comment_form" method="post" id="tvcmsblogs_commentfrom" data-toggle="validator">
            <div class="form-group tvcmsblogs_message-success"></div>
            <div class="form-group tvcmsblogs_message-error"></div>
            <div class="form-group tvcmsblog_name_parent clearfix">
                <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2" for="tvcmsblog_name">
                    {l s='Your Name' mod='tvcmsblog'}
                    <span class="required-star">*</span>
                </label>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <input type="text" id="tvcmsblog_name" name="tvcmsblog_name" class="form-control tvcmsblog_name" required>
                </div>
            </div>
            <div class="form-group tvcmsblog_email_parent clearfix">
                <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2" for="tvcmsblog_email">
                    {l s='Your Email' mod='tvcmsblog'}
                    <span class="required-star">*</span>
                </label>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <input type="email" id="tvcmsblog_email" name="tvcmsblog_email" class="form-control tvcmsblog_email" required>
                </div>
            </div>
            <div class="form-group tvcmsblog_website_parent clearfix">
                <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2" for="tvcmsblog_website">{l s='Website Url' mod='tvcmsblog'}</label>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <input type="url" id="tvcmsblog_website" name="tvcmsblog_website" class="form-control tvcmsblog_website">
                </div>
            </div>
            <div class="form-group tvcmsblog_subject_parent clearfix">
                <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2" for="tvcmsblog_subject">
                    {l s='Subject' mod='tvcmsblog'}
                    <span class="required-star">*</span>
                </label>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <input type="text" id="tvcmsblog_subject" name="tvcmsblog_subject" class="form-control tvcmsblog_subject" required>
                </div>
            </div>
            <div class="form-group tvcmsblog_content_parent clearfix">
                <label class="col-xs-12 col-sm-12 col-md-2 col-lg-2" for="tvcmsblog_content">
                    {l s='Comment' mod='tvcmsblog'}
                    <span class="required-star">*</span>
                </label>
                <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
                    <textarea rows="15" id="tvcmsblog_content" name="tvcmsblog_content" class="form-control tvcmsblog_content" required></textarea>
                </div>
            </div>
            <input type="hidden" class="tvcmsblog_id_parent" id="tvcmsblog_id_parent" name="tvcmsblog_id_parent" value="0">
            <input type="hidden" class="tvcmsblog_id_post" id="tvcmsblog_id_post" name="tvcmsblog_id_post" value="{$tvcmsblogpost.id_tvcmsposts}">
            <div class="tvblob-all-submit-btn">
                {* <input type="button" class="tvall-inner-btn tvcmsblog_submit_btn" value="Submit Button"> *}
                <button class="tvall-inner-btn tvcmsblog_submit_btn">
                    <span>{l s='Submit' mod='tvcmsblog'}</span>
                </button>
            </div>
        </form>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
    $('.tvcmsblog_submit_btn').on("click", function(e) {
        e.preventDefault();

        $('#tvcmsblogs_commentfrom').submit(function(event) {
            event.preventDefault();
        });
        var data = new Object();
        $('[id^="tvcmsblog_"]').each(function() {
            id = $(this).prop("id").replace("tvcmsblog_", "");
            data[id] = $(this).val();
        });

        function logErrprMessage(element, index, array) {
            $('.tvcmsblogs_message-error').append('<span class="tvcmsblogs_error">' + element + '</span>');
        }

        function tvcmsremove() {
            $('.tvcmsblogs_error').remove();
            $('.tvcmsblogs_success').remove();
        }

        function logSuccessMessage(element, index, array) {
            $('.tvcmsblogs_message-success').append('<span class="tvcmsblogs_success">' + element + '</span>');
        }

        $.ajax({
            url: tvcms_base_dir + 'modules/tvcmsblog/ajax.php',
            data: data,
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                tvcmsremove();
                $(".tvcmsblog_submit_btn").val("Please wait..");
                $(".tvcmsblog_submit_btn").addClass("disabled");
            },
            complete: function() {
                $(".tvcmsblog_submit_btn").val("Submit Button");
                $(".tvcmsblog_submit_btn").removeClass("disabled");
            },
            success: function(data) {
                tvcmsremove();
                if (typeof data.success != 'undefined') {
                    data.success.forEach(logSuccessMessage);
                    location.reload();
                }
                if (typeof data.error != 'undefined') {
                    data.error.forEach(logErrprMessage);
                }
            },
            error: function(data) {
                tvcmsremove();
                $('.tvcmsblogs_message').append('<span class="error">Something Wrong ! Please Try Again. </span>');
            },
        });
        e.stopPropagation();
    });
    </script>
    {/strip}