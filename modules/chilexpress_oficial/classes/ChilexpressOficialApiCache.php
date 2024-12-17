<?php

class ChilexpressOficialApiCache extends ObjectModel
{
  public $id_chilexpress_oficial_apicache;
  public $carrier_key;
  public $comuna_origen;
  public $comuna_destino;
  public $cart_id;
  public $costo_total;
  public $product_quantity;

  /**
  * @see ObjectModel::$definition
  */
  public static $definition = array(
    'table' => 'chilexpress_oficial_apicache',
    'primary' => 'id_chilexpress_oficial_apicache',
    'multilang' => false,
    'fields' => array(
        'carrier_key' =>   array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 32),
        'comuna_origen' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 32),
        'comuna_destino' =>  array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 32),
        'cart_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        'costo_total' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        'product_quantity'=>  array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 256),
        'service_value' =>  array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
        'created' =>  array('type' => self::TYPE_DATE)
    )
  );
}
