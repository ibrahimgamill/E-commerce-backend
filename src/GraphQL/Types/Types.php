<?php
namespace App\GraphQL\Types;

class Types
{
    private static $categoryType;
    private static $productType;
    private static $attributeSetType;
    private static $priceType;
    private static $attributeItemType;
    private static $currencyType;

    public static function category()
    {
        return self::$categoryType ?: (self::$categoryType = new CategoryType());
    }

    public static function product()
    {
        return self::$productType ?: (self::$productType = new ProductType());
    }

    public static function attributeSet()
    {
        return self::$attributeSetType ?: (self::$attributeSetType = new AttributeSetType());
    }
    public static function currency()
    {
        return self::$currencyType ?: (self::$currencyType = new CurrencyType());
    }

    public static function attributeItem()
    {
        return self::$attributeItemType ?: (self::$attributeItemType = new AttributeItemType());
    }

    public static function price()
    {
        return self::$priceType ?: (self::$priceType = new PriceType());
    }
}
