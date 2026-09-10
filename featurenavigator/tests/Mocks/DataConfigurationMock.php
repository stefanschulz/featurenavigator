<?php
namespace PrestaShop\PrestaShop\Core\Configuration;

interface DataConfigurationInterface {
    public function getConfiguration(): array;
    public function updateConfiguration(array $configuration): array;
    public function validateConfiguration(array $configuration): bool;
}

