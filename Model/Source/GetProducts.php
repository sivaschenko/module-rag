<?php
namespace PHPDublin\Rag\Model\Source;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Url;
use Magento\Framework\Api\SearchCriteriaBuilder;

class GetProducts
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Url $url
    ) {
    }

    /**
     * select entity_id as id, value as description from catalog_product_entity_text
     * where attribute_id = 75 and entity_id in (select distinct parent_id from catalog_product_super_link);
     *
     * @return array
     */
    public function execute(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('type_id', 'configurable')->create();
        $searchResult = $this->productRepository->getList($searchCriteria);
        $products = $searchResult->getItems();
        return $this->toArray($products);
    }

    /**
     * @param ProductInterface[]|Product[] $products
     * @return array
     */
    private function toArray(array $products): array
    {
        return array_map(
            function (ProductInterface $product): array
            {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'link' => $this->url->getUrl($product),
                    'description' => str_replace('&bull;', '', strip_tags($product->getDescription()))
                ];
            },
            $products
        );
    }
}
