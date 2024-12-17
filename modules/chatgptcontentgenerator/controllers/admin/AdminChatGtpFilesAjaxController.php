<?php
/**
 * 2007-2023 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminChatGtpFilesAjaxController extends ModuleAdminController
{
    public function ajaxProcessSaveFiles()
    {
        $dir = rtrim(_PS_UPLOAD_DIR_, '/');

        try {
            if (!file_exists($dir) || !is_writable($dir)) {
                throw new Exception(sprintf('Directory %s id not exists or not writable', $dir));
            }

            $uploader = new Uploader();
            $uploader
                ->setName('file')
                ->setAcceptTypes(['png', 'jpg', 'jpeg', 'svg', 'webp'])
                ->setSavePath(_PS_UPLOAD_DIR_)
            ;

            $uploaded_files = $uploader->process();
            $files_map = Tools::getValue('files_map', []);
            $files = [];
            foreach ($uploaded_files as $k => $file) {
                if (isset($file['error']) && $file['error'] !== 0) {
                    continue;
                }
                $id = (isset($files_map[$k]) ? $files_map[$k] : $k);
                $files[$id] = $file;
            }
        } catch (Exception $e) {
            $this->module->jsonExeptionResponse($e);
        }

        $this->module->jsonResponse([
            'files' => $files,
        ]);
    }

    public function ajaxProcessDeleteFiles()
    {
        try {
            $files = Tools::getValue('file', []);
            if (is_array($files)) {
                foreach ($files as $file_path) {
                    if (!is_string($file_path)) {
                        continue;
                    }

                    if (file_exists($file_path) || is_file($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        } catch (Exception $e) {
        }

        $this->module->jsonResponse([]);
    }
}
