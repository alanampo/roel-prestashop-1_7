<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
    
function upgrade_module_1_0_11($object) // NOSONAR
{
    return Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'chilexpress_oficial_apicache` (
        `id_chilexpress_oficial_ordermeta` int(11) NOT NULL AUTO_INCREMENT,
        `carrier_key` VARCHAR(32) NOT NULL,
        `comuna_origen` VARCHAR(32) NOT NULL,
        `comuna_destino` VARCHAR(32) NOT NULL,
        `cart_id` int(11) NOT NULL,
        `costo_total` int(11) NOT NULL,
        `product_quantity` VARCHAR(256) NOT NULL,
        `service_value` VARCHAR(128) NOT NULL,
        `created` DATETIME NOT NULL,
        PRIMARY KEY (`id_chilexpress_oficial_ordermeta`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN transportOrderNumber varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN reference varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN productDescription varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN serviceDescription varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN genericString1 varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN genericString2 varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN deliveryTypeCode varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN destinationCoverageAreaName varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN additionalProductDescription varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN barcode varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN classificationData varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN printedDate varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN labelVersion varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN distributionDescription varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN companyName varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN recipient varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN address varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN groupReference varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        ALTER TABLE prestashop.'._DB_PREFIX_.'chilexpress_oficial_ordermeta MODIFY COLUMN createdDate varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
        '
    );
}
