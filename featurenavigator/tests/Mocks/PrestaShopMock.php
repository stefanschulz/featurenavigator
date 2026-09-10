<?php
namespace PrestaShop\PrestaShop\Core;

interface ConfigurationInterface {
    public function get($key);
    public function set($key, $value);
}

class PrestaShopDatabaseException extends \Exception {}

namespace PrestaShop\PrestaShop\Core\Product\Search;

interface ProductSearchProviderInterface {
    public function runQuery(ProductSearchContext $context, ProductSearchQuery $query): ProductSearchResult;
}

class ProductSearchContext {
    public function getIdLang(): int { return 1; }
    public function getIdShop(): int { return 1; }
}

class ProductSearchQuery {
    public function getSearchString(): string { return ''; }
    public function getSearchTag(): string { return ''; }
    public function getResultsPerPage(): int { return 0; }
    public function getPage(): int { return 1; }
}

class ProductSearchResult {
    private array $products = [];
    private int $totalCount = 0;

    public function setProducts(array $products): void {
        $this->products = $products;
    }

    public function getProducts(): array {
        return $this->products;
    }

    public function setTotalProductsCount(int $count): void {
        $this->totalCount = $count;
    }

    public function getTotalProductsCount(): int {
        return $this->totalCount;
    }
}

namespace PrestaShop\PrestaShop\Core\Form;

interface FormDataProviderInterface {
    public function getData(): array;
    public function setData(array $data): array;
}

// Global namespace for Db
class Db {
    public function executeS(string $sql, bool $multiline = false, bool $cache = false): array {
        return [];
    }

    public function getValue(string $sql, bool $multiline = false, bool $cache = false) {
        return 0;
    }
}
