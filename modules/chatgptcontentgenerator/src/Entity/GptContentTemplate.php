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

class GptContentTemplate extends \ObjectModel
{
    public const TYPE = [
        'product' => 'Product',
        'category' => 'Category',
        'cms' => 'Page',
    ];

    /**
     * @var int
     */
    public $id_content_template;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type = 'product';

    /**
     * @var bool
     */
    public $active = 1;

    /**
     * @var string
     */
    public $short_code;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'content_template',
        'primary' => 'id_content_template',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => false],

            // lang fields
            'short_code' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    /**
     * Get the value of type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of short_code
     *
     * @return string|array
     */
    public function getShortCode()
    {
        return $this->short_code;
    }

    /**
     * Set the value of short_code
     *
     * @param string|array $short_code
     *
     * @return self
     */
    public function setShortCode($short_code)
    {
        $this->short_code = $short_code;

        return $this;
    }

    public static function getShortCodesFeaturesByLang(int $id_lang)
    {
        $shortCodesFeatures = [];

        foreach (\Feature::getFeatures($id_lang) as $feature) {
            $shortCodesFeatures['id'][$feature['id_feature']] = self::prepareFeatureId($feature['id_feature']);
            $shortCodesFeatures['name'][$feature['id_feature']] = $feature['name'];
            $shortCodesFeatures['prepareName'][$feature['id_feature']] = self::prepareFeatureName($feature['name']);
        }

        return $shortCodesFeatures;
    }

    public static function prepareFeatureId($id_feature)
    {
        return '{feature_' . (int) $id_feature . '}';
    }

    public static function prepareFeatureName($name)
    {
        return '{feature: ' . trim($name) . '}';
    }

    public static function prepareDisplayShortCode(int $id_lang, string $shortCode)
    {
        if (!$shortCode) {
            return trim($shortCode);
        }

        $shortCodesFeatures = self::getShortCodesFeaturesByLang((int) $id_lang);

        if (isset($shortCodesFeatures['id']) && isset($shortCodesFeatures['prepareName'])) {
            $shortCode = str_replace($shortCodesFeatures['id'], $shortCodesFeatures['prepareName'], trim($shortCode));
        }

        return trim($shortCode);
    }

    public static function getTemplates($page = 0, $limit = 0)
    {
        $sql = 'SELECT ct.*,  GROUP_CONCAT(DISTINCT lang.iso_code SEPARATOR ", ") AS langs
            FROM `' . _DB_PREFIX_ . 'content_template` ct
            LEFT JOIN `' . _DB_PREFIX_ . 'content_template_lang` ctl ON (ctl.`id_content_template` = ct.`id_content_template`)
            LEFT JOIN `' . _DB_PREFIX_ . 'lang` AS lang ON (ctl.`id_lang` = lang.`id_lang`
                AND ctl.`short_code` IS NOT NULL AND trim(ctl.`short_code`) <> "")
            GROUP BY ct.id_content_template
            ORDER BY ct.id_content_template DESC';

        if ($page <= 1) {
            $offset = 0;
        } else {
            $offset = ($page - 1) * $limit;
        }

        if ($limit) {
            $sql .= ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        }

        return \Db::getInstance()->executeS($sql);
    }

    public static function getTemplatesTotal()
    {
        $sql = 'SELECT count(id_content_template) FROM `' . _DB_PREFIX_ . 'content_template`';

        return \Db::getInstance()->getValue($sql);
    }

    public static function prepareSaveShortCode(int $id_lang, string $shortCode)
    {
        if (!$shortCode) {
            return trim($shortCode);
        }

        $shortCodesFeatures = self::getShortCodesFeaturesByLang((int) $id_lang);

        if (isset($shortCodesFeatures['id']) && isset($shortCodesFeatures['prepareName'])) {
            $shortCode = str_replace($shortCodesFeatures['prepareName'], $shortCodesFeatures['id'], trim($shortCode));
        }

        return trim($shortCode);
    }

    public static function getContentTemplatesByType($type, $active = null)
    {
        $result = [];

        $sql = 'SELECT cp.*, GROUP_CONCAT(DISTINCT cpl.id_lang) AS langs
            FROM `' . _DB_PREFIX_ . 'content_template` cp
                LEFT JOIN `' . _DB_PREFIX_ . 'content_template_lang` cpl ON (cp.id_content_template = cpl.id_content_template)
            WHERE cp.`type` = "' . pSQL($type) . '"
                AND cpl.`short_code` IS NOT NULL
                AND trim(cpl.`short_code`) <> ""';

        if (null !== $active) {
            $sql .= ' AND cp.`active` = ' . (int) $active;
        }

        $sql .= ' GROUP BY cp.id_content_template ORDER BY cp.`name`';

        $query = \Db::getInstance()->executeS($sql);

        if ($query) {
            foreach ($query as $template) {
                if ($template['langs']) {
                    $template['langs'] = explode(',', $template['langs']);
                    $result[(int) $template['id_content_template']] = $template;
                }
            }
        }

        return $result;
    }

    public static function getContentTemplatesByPage($adminPageName, $active = null)
    {
        $shortCodes = [];

        if (false !== strpos($adminPageName, 'product')) {
            $shortCodes = self::getContentTemplatesByType('product', $active);
        } elseif (false !== strpos($adminPageName, 'categor')) {
            $shortCodes = self::getContentTemplatesByType('category', $active);
        } elseif (false !== strpos($adminPageName, 'cms')) {
            $shortCodes = self::getContentTemplatesByType('cms', $active);
        }

        return $shortCodes;
    }

    public function getContentByObject($object)
    {
        $content = '';

        try {
            switch ($this->type) {
                case 'product':
                    $content = $this->getContentProduct($object);
                    break;
                case 'category':
                    $content = $this->getContentCategory($object);
                    break;
                case 'cms':
                    $content = $this->getContentCms($object);
                    break;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $content;
    }

    private function getContentProduct($object)
    {
        $newContent = '';

        if ($object instanceof \Product) {
            // default category
            $defaultCategory = ['name' => null, 'description' => null];
            if ($object->id_category_default) {
                $category = new \Category($object->id_category_default, $object->id_lang);
                $defaultCategory['name'] = trim($category->name);
                $defaultCategory['description'] = trim(strip_tags($category->description));
            }

            // all categories
            $productCategories = [];
            foreach (\Product::getProductCategoriesFull($object->id, $object->id_lang) as $category) {
                $productCategories[] = trim($category['name']);
            }

            // attributes
            $attributes = [];
            foreach ($object->getAttributesGroups($object->id_lang) as $attribute) {
                $attributes[$attribute['group_name']][$attribute['attribute_name']] = trim($attribute['attribute_name']);
            }
            if ($attributes) {
                foreach ($attributes as $group_name => &$attribute_name) {
                    $attribute_name = trim($group_name) . ': ' . implode(', ', $attribute_name);
                }
            }

            // features
            $features = [];
            $featuresProduct = [
                'all' => [],
                'search' => [],
                'replace' => [],
            ];
            foreach ($object->getFrontFeatures($object->id_lang) as $feature) {
                $features[$feature['id_feature']]['name'] = trim($feature['name']);
                $features[$feature['id_feature']]['values'][] = trim($feature['value']);
            }
            if ($features) {
                foreach ($features as $id_feature => $feature) {
                    $values = implode(', ', $feature['values']);
                    $featuresProduct['all'][] = trim($feature['name']) . ': ' . $values;
                    $featuresProduct['search'][] = self::prepareFeatureId($id_feature);
                    $featuresProduct['replace'][] = $values;
                }
            }

            $search = array_merge(self::getProductShortCodes(false), $featuresProduct['search']);

            $replace = array_merge([
                '{product_name}' => trim($object->name),
                '{product_description}' => trim(strip_tags($object->description)),
                '{product_description_short}' => trim(strip_tags($object->description_short)),
                '{product_tags}' => $object->getTags($object->id_lang),
                '{product_reference}' => trim($object->reference),
                '{product_weight}' => $object->weight,
                '{product_default_category}' => $defaultCategory['name'],
                '{product_categories}' => implode(', ', $productCategories),
                '{product_category_description}' => $defaultCategory['description'],
                '{product_brand}' => trim($object->getWsManufacturerName()),
                '{product_attributes}' => implode('; ', $attributes),
                '{product_features}' => implode('; ', $featuresProduct['all']),
            ], $featuresProduct['replace']);

            $newContent = str_replace($search, $replace, $this->getShortCode());
        } else {
            throw new \Exception('Object is not a product class');
        }

        return $this->cleanString($newContent);
    }

    private function getContentCategory($object)
    {
        $newContent = '';

        if ($object instanceof \Category) {
            $replace = [
                '{category_name}' => trim($object->name),
                '{category_description}' => trim(strip_tags($object->description)),
            ];

            $newContent = str_replace(self::getCategoryShortCodes(false), $replace, $this->getShortCode());
        } else {
            throw new \Exception('Object is not a category class');
        }

        return $this->cleanString($newContent);
    }

    private function getContentCMS($object)
    {
        $newContent = '';

        if ($object instanceof \CMS) {
            $replace = [
                '{cms_name}' => trim($object->meta_title),
                '{cms_content}' => trim(strip_tags($object->content)),
            ];

            $newContent = str_replace(self::getPageShortCodes(false), $replace, $this->getShortCode());
        } else {
            throw new \Exception('Object is not a CMS class');
        }

        return $this->cleanString($newContent);
    }

    private function cleanString($string)
    {
        return trim(preg_replace(['/{feature_(\d+)}/', '/\s\s+/'], ['', ' '], $string));
    }

    public static function getProductShortCodes($isDescr = true)
    {
        $translator = \Context::getContext()->getTranslator();

        $shortCodes = [
            '{product_name}' => $translator->trans('Product name', [], 'Admin.Catalog.Feature'),
            '{product_description}' => $translator->trans('Description', [], 'Admin.Global'),
            '{product_description_short}' => $translator->trans('Summary', [], 'Admin.Catalog.Feature'),
            '{product_tags}' => $translator->trans('Tags', [], 'Admin.Catalog.Feature'),
            '{product_reference}' => $translator->trans('Reference', [], 'Admin.Global'),
            '{product_weight}' => $translator->trans('Weight', [], 'Admin.Catalog.Feature'),
            '{product_default_category}' => $translator->trans('Product default category name', [], 'Modules.Chatgptcontentgenerator.Admin'),
            '{product_categories}' => $translator->trans('Names of all product categories', [], 'Modules.Chatgptcontentgenerator.Admin'),
            '{product_category_description}' => $translator->trans('Description of the default product category', [], 'Modules.Chatgptcontentgenerator.Admin'),
            '{product_brand}' => $translator->trans('Brand', [], 'Admin.Global'),
            '{product_attributes}' => $translator->trans('Product attributes', [], 'Modules.Chatgptcontentgenerator.Admin'),
            '{product_features}' => $translator->trans('Features', [], 'Admin.Global'),
        ];

        if (false === $isDescr) {
            $shortCodes = array_keys($shortCodes);
        }

        return $shortCodes;
    }

    public static function getCategoryShortCodes($isDescr = true)
    {
        $translator = \Context::getContext()->getTranslator();

        $shortCodes = [
            '{category_name}' => $translator->trans('Category name', [], 'Admin.Catalog.Feature'),
            '{category_description}' => $translator->trans('Description', [], 'Admin.Global'),
        ];

        if (false === $isDescr) {
            $shortCodes = array_keys($shortCodes);
        }

        return $shortCodes;
    }

    public static function getPageShortCodes($isDescr = true)
    {
        $translator = \Context::getContext()->getTranslator();

        $shortCodes = [
            '{page_name}' => $translator->trans('Page name', [], 'Admin.Shopparameters.Feature'),
            '{page_content}' => $translator->trans('Page content', [], 'Admin.Design.Feature'),
        ];

        if (false === $isDescr) {
            $shortCodes = array_keys($shortCodes);
        }

        return $shortCodes;
    }
}
