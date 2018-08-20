<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\RestApiDocumentationGenerator\Business\Generator;

use ReflectionClass;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

class RestApiDocumentationSchemaGenerator implements RestApiDocumentationSchemaGeneratorInterface
{
    protected const DATA_TYPES_MAPPING_LIST = [
        'int' => 'integer',
        'bool' => 'boolean',
        'float' => 'number',
    ];

    /**
     * @var array
     */
    protected $schemas = [];

    /**
     * @return array
     */
    public function getSchemas(): array
    {
        ksort($this->schemas);

        return $this->schemas;
    }

    /**
     * @param string $transferName
     *
     * @return void
     */
    public function addSchemaFromTransferClassName(string $transferName): void
    {
        if (!class_exists($transferName)) {
            return;
        }

        $transfer = new $transferName;
        if (!$transfer instanceof AbstractTransfer) {
            return;
        }

        $this->addTransferToSchema($transfer, $this->getSchemaKeyFromTransferClassName($transferName));
    }

    /**
     * @return string
     */
    public function getLastAddedSchemaKey(): string
    {
        $schemaKeys = array_keys($this->schemas);

        return array_pop($schemaKeys);
    }

    /**
     * @param string $transferClassName
     *
     * @return string
     */
    protected function getSchemaKeyFromTransferClassName(string $transferClassName): string
    {
        $transferClassNameExploded = explode('\\', $transferClassName);

        return str_replace('Transfer', '', end($transferClassNameExploded));
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer
     * @param string $transferSchemaKey
     *
     * @return void
     */
    protected function addTransferToSchema(AbstractTransfer $transfer, string $transferSchemaKey): void
    {
        if (array_key_exists($transferSchemaKey, $this->schemas)) {
            return;
        }

        $transferReflection = new ReflectionClass($transfer);
        $transferMetadata = $transferReflection->getProperty('transferMetadata');
        $transferMetadata->setAccessible(true);
        $transferMetadataValue = $transferMetadata->getValue($transfer);

        $schemaProperties = [];
        foreach ($transferMetadataValue as $key => $value) {
            if (class_exists($value['type'])) {
                $schemaProperties[$key]['$ref'] = $this->formatTransferClassToSchemaType($value['type']);
            } else {
                $schemaProperties[$key]['type'] = $this->formatSchemaType($value['type']);
            }
        }

        $this->schemas[$transferSchemaKey]['properties'] = $schemaProperties;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function formatSchemaType(string $type): string
    {
        if (array_key_exists($type, static::DATA_TYPES_MAPPING_LIST)) {
            return static::DATA_TYPES_MAPPING_LIST[$type];
        }

        if (substr($type, -2) === '[]') {
            return 'array';
        }

        return $type;
    }

    /**
     * @param string $transferClassName
     *
     * @return string
     */
    protected function formatTransferClassToSchemaType(string $transferClassName): string
    {
        $this->addTransferToSchema(new $transferClassName, $this->getSchemaKeyFromTransferClassName($transferClassName));

        return sprintf('#/components/schemas/%s', $this->getSchemaKeyFromTransferClassName($transferClassName));
    }
}
