<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSearch\Business;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\ProductSearchAttributeTransfer;
use Generated\Shared\Transfer\ProductSearchPreferencesTransfer;
use Orm\Zed\Touch\Persistence\SpyTouchQuery;
use Spryker\Zed\Collector\Business\Exporter\Reader\ReaderInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\TouchUpdaterInterface;
use Spryker\Zed\Collector\Business\Exporter\Writer\WriterInterface;
use Spryker\Zed\Collector\Business\Model\BatchResultInterface;
use Spryker\Zed\Kernel\Business\AbstractFacade;
use Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Spryker\Zed\ProductSearch\Business\ProductSearchBusinessFactory getFactory()
 */
class ProductSearchFacade extends AbstractFacade implements ProductSearchFacadeInterface
{

    /**
     * Specification:
     * - Iterates through the given product attribute associative array where the key is the name and the value is the value of the attributes.
     * - If an attribute is configured to be mapped in the page map builder, then it's value will be added to the page map.
     * - The data of the returned page map represents a hydrated Elasticsearch document with all the necessary attribute values.
     *
     * @api
     *
     * @param \Spryker\Zed\Search\Business\Model\Elasticsearch\DataMapper\PageMapBuilderInterface $pageMapBuilder
     * @param \Generated\Shared\Transfer\PageMapTransfer $pageMapTransfer
     * @param array $attributes
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function mapDynamicProductAttributes(PageMapBuilderInterface $pageMapBuilder, PageMapTransfer $pageMapTransfer, array $attributes)
    {
        return $this
            ->getFactory()
            ->createProductSearchAttributeMapper()
            ->mapDynamicProductAttributes($pageMapBuilder, $pageMapTransfer, $attributes);
    }

    /**
     * Specification:
     * - Marks the given product to be searchable.
     *
     * @api
     *
     * @param int $idProduct
     * @param \Generated\Shared\Transfer\LocaleTransfer[] $localeCollection
     *
     * @return void
     */
    public function activateProductSearch($idProduct, array $localeCollection)
    {
        $this->getFactory()
            ->createProductSearchMarker()
            ->activateProductSearch($idProduct, $localeCollection);
    }

    /**
     * Specification:
     * - Marks the given product to not to be searchable.
     *
     * @api
     *
     * @param int $idProduct
     * @param \Generated\Shared\Transfer\LocaleTransfer[] $localeCollection
     *
     * @return void
     */
    public function deactivateProductSearch($idProduct, array $localeCollection)
    {
        $this->getFactory()
            ->createProductSearchMarker()
            ->deactivateProductSearch($idProduct, $localeCollection);
    }

    /**
     * Specification:
     * - If the given product attribute key does not exists then it will be created.
     * - For the given product attribute the search preferences will be created.
     * - Returns a transfer that also contains the ids of the created entities.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchPreferencesTransfer $productSearchPreferencesTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSearchPreferencesTransfer
     */
    public function createProductSearchPreferences(ProductSearchPreferencesTransfer $productSearchPreferencesTransfer)
    {
        return $this
            ->getFactory()
            ->createAttributeMapWriter()
            ->create($productSearchPreferencesTransfer);
    }

    /**
     * Specification:
     * - For the given product attribute the search preferences will be updated.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchPreferencesTransfer $productSearchPreferencesTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSearchPreferencesTransfer
     */
    public function updateProductSearchPreferences(ProductSearchPreferencesTransfer $productSearchPreferencesTransfer)
    {
        return $this
            ->getFactory()
            ->createAttributeMapWriter()
            ->update($productSearchPreferencesTransfer);
    }

    /**
     * Specification:
     * - Removes all product search preferences for the given product attribute.
     * - The product attribute itself is not removed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchPreferencesTransfer $productSearchPreferencesTransfer
     *
     * @return void
     */
    public function cleanProductSearchPreferences(ProductSearchPreferencesTransfer $productSearchPreferencesTransfer)
    {
        $this
            ->getFactory()
            ->createAttributeMapWriter()
            ->clean($productSearchPreferencesTransfer);
    }

    /**
     * Specification:
     * - Returns a filtered list of keys that exists in the persisted product attribute key list but not in the persisted
     * product search attribute list
     *
     * @api
     *
     * @param string $searchText
     * @param int $limit
     *
     * @return array
     */
    public function suggestUnusedProductSearchAttributeKeys($searchText = '', $limit = 10)
    {
        return $this->getFactory()
            ->createAttributeReader()
            ->suggestUnusedKeys($searchText, $limit);
    }

    /**
     * Specification:
     * - Searches for an existing product attribute key entity by the provided key in database or create it if does not exist.
     * - Creates a new product search attribute entity with the given data and the found/created attribute key entity.
     * - Creates a glossary key for the search attribute key if does not exist already.
     * - Returns a transfer that also contains the ids of the created entities.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchAttributeTransfer $productSearchAttributeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSearchAttributeTransfer
     */
    public function createProductSearchAttribute(ProductSearchAttributeTransfer $productSearchAttributeTransfer)
    {
        return $this
            ->getFactory()
            ->createAttributeWriter()
            ->create($productSearchAttributeTransfer);
    }

    /**
     * Specification:
     * - Searches for an existing product attribute key entity by the provided key in database or create it if does not exist.
     * - Updates an existing product search attribute entity by id with the given data and the found/created attribute key entity.
     * - Creates a glossary key for the product attribute key if does not exist already.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchAttributeTransfer $productSearchAttributeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSearchAttributeTransfer
     */
    public function updateProductSearchAttribute(ProductSearchAttributeTransfer $productSearchAttributeTransfer)
    {
        return $this
            ->getFactory()
            ->createAttributeWriter()
            ->update($productSearchAttributeTransfer);
    }

    /**
     * Specification:
     * - Removes the product search attribute entity by id.
     * - The product attribute itself is not removed.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchAttributeTransfer $productSearchAttributeTransfer
     *
     * @return void
     */
    public function deleteProductSearchAttribute(ProductSearchAttributeTransfer $productSearchAttributeTransfer)
    {
        $this
            ->getFactory()
            ->createAttributeWriter()
            ->delete($productSearchAttributeTransfer);
    }

    /**
     * Specification:
     * - Reads a product search attribute entity from the database and returns a fully hydrated transfer representation.
     * - Return null if the entity is not found by id.
     *
     * @api
     *
     * @param int $idProductSearchAttribute
     *
     * @return \Generated\Shared\Transfer\ProductSearchAttributeTransfer|null
     */
    public function getProductSearchAttribute($idProductSearchAttribute)
    {
        return $this
            ->getFactory()
            ->createAttributeReader()
            ->getAttribute($idProductSearchAttribute);
    }

    /**
     * Specification:
     * - Reads all product search attribute entities from the database and returns a list of their fully hydrated transfer representations.
     * - The returned list is ordered ascending by position.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\ProductSearchAttributeTransfer[]
     */
    public function getProductSearchAttributeList()
    {
        return $this
            ->getFactory()
            ->createAttributeReader()
            ->getAttributeList();
    }

    /**
     * Specification:
     * - Updates the positions of the provided product search attribute entities.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductSearchAttributeTransfer[] $productSearchAttributes
     *
     * @return void
     */
    public function updateProductSearchAttributeOrder(array $productSearchAttributes)
    {
        $this
            ->getFactory()
            ->createAttributeWriter()
            ->reorder($productSearchAttributes);
    }

    /**
     * Specification:
     * - Touches abstract products which has an attribute that has not been synchronized yet.
     * - Asynchronous attribute means a product search attribute entity had been created/modified/deleted since last synchronization.
     * - After touch, product search attribute entities are marked as synchronized.
     *
     * @api
     *
     * @return void
     */
    public function touchProductAbstractByAsynchronousAttributes()
    {
        $this
            ->getFactory()
            ->createProductSearchAttributeMarker()
            ->touchProductAbstract();
    }

    /**
     * Specification:
     * - Touches abstract products which has an attribute that has not been synchronized yet.
     * - Asynchronous attribute means a product search attribute map entity had been created/modified/deleted since last synchronization.
     * - After touch, product search attribute map entities are marked as synchronized.
     *
     * @api
     *
     * @return void
     */
    public function touchProductAbstractByAsynchronousAttributeMap()
    {
        $this
            ->getFactory()
            ->createProductSearchAttributeMapMarker()
            ->touchProductAbstract();
    }

    /**
     * Specification:
     * - Touches the "product_search_config_extension" resource which will indicate the responsible collector to run next time collectors are executed.
     *
     * @return void
     */
    public function touchProductSearchConfigExtension()
    {
        $this
            ->getFactory()
            ->createProductSearchConfigExtensionMarker()
            ->touchProductSearchConfigExtension();
    }

    /**
     * Specification:
     * - Executes the product search config extension collector.
     * - The collected data is compatible with \Generated\Shared\Transfer\SearchConfigExtensionTransfer.
     * - The collected data contains all the facet configurations provided by the database.
     * - The facet configurations are stored in their defined order.
     *
     * @api
     *
     * @param SpyTouchQuery $baseQuery
     * @param LocaleTransfer $locale
     * @param BatchResultInterface $result
     * @param ReaderInterface $dataReader
     * @param WriterInterface $dataWriter
     * @param TouchUpdaterInterface $touchUpdater
     * @param OutputInterface $output
     *
     * @return void
     */
    public function runProductSearchConfigExtensionCollector(
        SpyTouchQuery $baseQuery,
        LocaleTransfer $locale,
        BatchResultInterface $result,
        ReaderInterface $dataReader,
        WriterInterface $dataWriter,
        TouchUpdaterInterface $touchUpdater,
        OutputInterface $output
    )
    {
        $collector = $this->getFactory()->createProductSearchConfigExtensionCollector();

        $this
            ->getFactory()
            ->getCollectorFacade()
            ->runCollector($collector, $baseQuery, $locale, $result, $dataReader, $dataWriter, $touchUpdater, $output);
    }

}
