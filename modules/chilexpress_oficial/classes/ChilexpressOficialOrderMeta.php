<?php

class ChilexpressOficialOrderMeta extends ObjectModel
{
  public $id_chilexpress_oficial_ordermeta;
  public $id_order;
  public $transportOrderNumber;
  public $reference;
  public $productDescription;
  public $serviceDescription;
  public $genericString1;
  public $genericString2;
  public $deliveryTypeCode;
  public $destinationCoverageAreaName;
  public $additionalProductDescription;
  public $barcode;
  public $classificationData;
  public $printedDate;
  public $labelVersion;
  public $distributionDescription;
  public $companyName;
  public $recipient;
  public $address;
  public $groupReference;
  public $createdDate;
  public $labelData;


  /**
  * @see ObjectModel::$definition
  */
  public static $definition = array(
    'table' => 'chilexpress_oficial_ordermeta',
    'primary' => 'id_chilexpress_oficial_ordermeta',
    'multilang' => false,
    'fields' => array(
      'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
      'transportOrderNumber' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'productDescription' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'serviceDescription' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'genericString1' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'genericString2' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'deliveryTypeCode' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'destinationCoverageAreaName' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'additionalProductDescription' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'barcode' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'classificationData' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'printedDate' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'labelVersion' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'distributionDescription' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'companyName' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'recipient' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'address' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'groupReference' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'createdDate' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
      'labelData' => array('type' => self::TYPE_STRING)
    )
  );
}
