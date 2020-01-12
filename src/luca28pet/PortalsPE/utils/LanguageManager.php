<?php
declare(strict_types=1);

namespace luca28pet\PortalsPE\utils;

use InvalidArgumentException;
use function array_keys;
use function str_replace;

class LanguageManager{

    /** @var array  */
    private $defaultLangData;
    /** @var array  */
    private $userLangData;

    public function __construct(array $defaultLangData, array $userLangData){
        $this->defaultLangData = $defaultLangData;
        $this->userLangData = $userLangData;
    }

    public function getDefaultLangData() : array{
        return $this->defaultLangData;
    }

    public function setDefaultLangData(array $defaultLangData) : void{
        $this->defaultLangData = $defaultLangData;
    }

    public function getUserLangData() : array{
        return $this->userLangData;
    }

    public function setUserLangData(array $userLangData) : void{
        $this->userLangData = $userLangData;
    }

    public function getTranslation(string $key, array $params = []) : string{
        if(!isset($this->userLangData[$key]) && !isset($this->defaultLangData[$key])){
            throw new InvalidArgumentException('Invalid key '.$key);
        }
        return str_replace(array_keys($params), $params, $this->userLangData[$key] ?? $this->defaultLangData[$key]);
    }

}