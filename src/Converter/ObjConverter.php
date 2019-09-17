<?php

namespace Mooon\Rest\Converter;

/**
 * Class ObjConverter
 */
class ObjConverter
{
    /**
     * @param object $object
     * @return array
     */
    public static function constantsToArray(object $object):array
    {
        $arr = [];

        /** @var \ReflectionObject $reflectionObject */
        $reflectionObject = new \ReflectionObject($object);
        $constants = $reflectionObject->getConstants();

        foreach ($constants as $constantKey => $constant){
            $arr[$constant] = $constantKey;
        }

        return $arr;
    }

    /**
     * @param object $object
     * @return array
     */
    public static function objectToArray(object $object):array
    {
        /** @var \ReflectionObject $reflectionObject */
        $reflectionObject = new \ReflectionObject($object);

        $properties = $reflectionObject->getProperties();
        $methods = $reflectionObject->getMethods();

        $propertyList = [];

        /** @var \ReflectionProperty $property */
        foreach ($properties as $property){
            $property->setAccessible(true);
            $modifier = \Reflection::getModifierNames($property->getModifiers());
            $propertyList[$property->getName()] = [
                'modifier' => $modifier,
                'value' => $property->getValue($object),
                'static' => $property->isStatic(),
                'doc' => $property->getDocComment(),
            ];
        }

        return [
            'properties' => $propertyList,
            'methods' => $methods,
        ];
    }

    /**
     * @param array $array
     * @param object $object
     * @return object
     */
    public static function arrayToObject(array $array, object $object):object
    {
        /** @var \ReflectionObject $reflectionObject */
        $reflectionObject = new \ReflectionObject($object);
        /** @var \ReflectionProperty[] $reflectionProperties */
        $reflectionProperties = $reflectionObject->getProperties();

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionProperties as $reflectionProperty){
            $reflectionPropertyName = $reflectionProperty->getName();
            if(false === array_key_exists($reflectionPropertyName, $array)){
                continue;
            }
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($object, $array[$reflectionPropertyName]);
        }

        return $object;
    }

    /**
     * @param string $json
     * @param object $object
     * @return object
     */
    public static function jsonToObject(string $json, object $object):object
    {
        return self::arrayToObject(json_decode($json, true), $object);
    }
}