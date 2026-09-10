<?php
namespace PrestaShop\PrestaShop\Core;

interface ConfigurationInterface {
    public function get($key);
    public function set($key, $value);
}

