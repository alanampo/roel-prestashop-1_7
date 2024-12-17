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

namespace PrestaShop\Module\Chatgptcontentgenerator\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GptSpinoffConnections extends \ObjectModel
{
    public $id;
    public $id_spinoff;
    public $id_product;
    public $stock;

    public const SPINOFF_STOCK_COMMON = 1;
    public const SPINOFF_STOCK_INDIVIDUAL = 2;

    public static $definition = [
        'table' => 'spinoff_connections',
        'primary' => 'id',
        'multilang' => false,
        'fields' => [
            'id_spinoff' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'stock' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
        ],
    ];

    public static function getConectionsByProductId($id_product)
    {
        if (!$id_product) {
            return [];
        }

        $sql = 'SELECT id_spinoff, stock FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
                WHERE id_product = ' . (int) $id_product;

        return \Db::getInstance()->executeS($sql);
    }

    public static function getConectionsBySpinOffId($id_spinoff)
    {
        if (!$id_spinoff) {
            return [];
        }
        $sql = 'SELECT id, id_spinoff, stock, id_product FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
                WHERE id_spinoff = ' . (int) $id_spinoff;

        return \Db::getInstance()->getRow($sql);
    }

    public static function deleteConectionsBySpinOffId($id_spinoff)
    {
        if (!$id_spinoff) {
            return false;
        }

        $sql = 'DELETE FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
                WHERE id_spinoff = ' . (int) $id_spinoff;

        return \Db::getInstance()->execute($sql);
    }

    public static function getAllConections()
    {
        $sql = 'SELECT id_spinoff FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`';

        return \Db::getInstance()->executeS($sql);
    }

    public static function getAllConectionsInfoByLang($id_lang, $isActive = true)
    {
        if (!$id_lang) {
            return [];
        }

        $sql = 'SELECT sc.`id_spinoff`, pl.`name` FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` sc
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = sc.`id_spinoff`)';

        if (true === $isActive) {
            $sql .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = sc.`id_spinoff`)';
        }

        $sql .= ' WHERE pl.`id_lang` = ' . (int) $id_lang;

        if (true === $isActive) {
            $sql .= ' AND p.`active` = 1';
        }

        $sql .= ' ORDER BY pl.`name`';

        return \Db::getInstance()->executeS($sql);
    }

    public function delete()
    {
        $product = new \Product($this->id_spinoff);
        $product->delete();

        return parent::delete();
    }

    public static function getConectionsBySpinOffIdList(array $spinoffIdList)
    {
        if (!$spinoffIdList) {
            return [];
        }
        $sql = 'SELECT id_spinoff FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
                WHERE id_spinoff IN (' . implode(', ', $spinoffIdList) . ') ';

        return \Db::getInstance()->executeS($sql);
    }

    public static function countSpinOffsByProductId($id_product)
    {
        if (!$id_product) {
            return [];
        }
        $sql = 'SELECT COUNT(*) AS spinoffs_count FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
                WHERE id_product = ' . (int) $id_product;

        return \Db::getInstance()->executeS($sql);
    }

    public static function countAllSpinOffs()
    {
        $sql = 'SELECT COUNT(*) AS spinoffs_count FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`';

        $count = \Db::getInstance()->getRow($sql);

        return isset($count['spinoffs_count']) ? (int) $count['spinoffs_count'] : 0;
    }

    public static function updateStockByProductList(array $product_list, $id_lang)
    {
        \Chatgptcontentgenerator::$isUpdateStock = false;
        foreach ($product_list as $product) {
            $attributes_list = [];
            $id_product = $product['product_id'];
            $id_product_attribute = $product['product_attribute_id'];

            if (isset($product['quantity'])) {
                $quantity = (int) $product['quantity'];
            } else {
                $quantity = \StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute);
            }

            if ($id_product_attribute) {
                $product = new \Product($id_product);
                foreach ($product->getAttributesGroups($id_lang, $id_product_attribute) as $attributeGroup) {
                    $attributes_list[] = $attributeGroup['id_attribute'];
                }
            }

            // if the main product
            $spinOffs = self::getConectionsByProductId($id_product);
            if ($spinOffs) {
                foreach ($spinOffs as $spinOff) {
                    if ($spinOff['stock'] == self::SPINOFF_STOCK_COMMON) {
                        self::updateProductStock($spinOff['id_spinoff'], $attributes_list, $quantity);
                    }
                }
            }

            // if the spinn off product
            $spinoffConnections = self::getConectionsBySpinOffId($id_product);
            if ($spinoffConnections && $spinoffConnections['stock'] == self::SPINOFF_STOCK_COMMON) {
                self::updateProductStock($spinoffConnections['id_product'], $attributes_list, $quantity);

                $spinOffs = self::getConectionsByProductId($spinoffConnections['id_product']);
                if ($spinOffs) {
                    foreach ($spinOffs as $spinOff) {
                        if ($spinOff['stock'] == self::SPINOFF_STOCK_COMMON && $spinOff['id_spinoff'] != $id_product) {
                            self::updateProductStock($spinOff['id_spinoff'], $attributes_list, $quantity);
                        }
                    }
                }
            }
        }
        \Chatgptcontentgenerator::$isUpdateStock = true;
    }

    public static function updateProductStock($id_product, $attributes_list, $quantity)
    {
        $id_product_attribute = 0;

        if ($attributes_list) {
            $product = new \Product($id_product);
            $id_product_attribute = $product->productAttributeExists($attributes_list, false, null, false, true);
        }

        \StockAvailable::setQuantity($id_product, $id_product_attribute, $quantity);
    }
}
