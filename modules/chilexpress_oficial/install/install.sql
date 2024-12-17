CREATE TABLE IF NOT EXISTS `PREFIX_chilexpress_oficial_ordermeta` (
  `id_chilexpress_oficial_ordermeta` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `transportOrderNumber` VARCHAR(128) NOT NULL,
  `reference` VARCHAR(128) NOT NULL,
  `productDescription` VARCHAR(128) NOT NULL,
  `serviceDescription` VARCHAR(128) NOT NULL,
  `genericString1` VARCHAR(128) NOT NULL,
  `genericString2` VARCHAR(128) NOT NULL,
  `deliveryTypeCode` VARCHAR(128) NOT NULL,
  `destinationCoverageAreaName` VARCHAR(128) NOT NULL,
  `additionalProductDescription` VARCHAR(256) NOT NULL,
  `barcode` VARCHAR(128) NOT NULL,
  `classificationData` VARCHAR(128) NOT NULL,
  `printedDate` VARCHAR(128) NOT NULL,
  `labelVersion` VARCHAR(128) NOT NULL,
  `distributionDescription` VARCHAR(128) NOT NULL,
  `companyName` VARCHAR(128) NOT NULL,
  `recipient` VARCHAR(128) NOT NULL,
  `address` VARCHAR(128) NOT NULL,
  `groupReference` VARCHAR(128) NOT NULL,
  `createdDate` VARCHAR(128) NOT NULL,
  `labelData` MEDIUMTEXT NOT NULL,  
  PRIMARY KEY (`id_chilexpress_oficial_ordermeta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `PREFIX_chilexpress_oficial_apicache` (
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
