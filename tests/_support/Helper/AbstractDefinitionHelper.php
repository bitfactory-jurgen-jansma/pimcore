<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Tests\Helper;

use Codeception\Module;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Tests\Util\TestHelper;

abstract class AbstractDefinitionHelper extends Module
{
    /**
     * @var array
     */
    protected $config = [
        'initialize_definitions' => true,
        'cleanup' => true,
    ];

    /**
     * @return Module|ClassManager
     */
    protected function getClassManager()
    {
        return $this->getModule('\\' . ClassManager::class);
    }

    /**
     * {@inheritdoc}
     */
    public function _beforeSuite($settings = [])
    {
        if ($this->config['initialize_definitions']) {
            if (TestHelper::supportsDbTests()) {
                $this->initializeDefinitions();
            } else {
                $this->debug(sprintf(
                    '[%s] Not initializing model definitions as DB is not connected',
                    strtoupper((new \ReflectionClass($this))->getShortName())
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _afterSuite()
    {
        if ($this->config['cleanup']) {
            TestHelper::cleanUp();
        }
    }

    /**
     * @param string $type
     * @param string|null $name
     * @param bool $mandatory
     * @param int $index
     * @param bool $visibleInGridView
     * @param bool $visibleInSearchResult
     *
     * @return Data
     */
    public function createDataChild($type, $name = null, $mandatory = false, $index = 0, $visibleInGridView = true, $visibleInSearchResult = true)
    {
        if (!$name) {
            $name = $type;
        }

        if (strpos($type, 'indexField') === 0) {
            $classname = 'Pimcore\\Bundle\\EcommerceFrameworkBundle\\CoreExtensions\\ClassDefinition\\' . ucfirst($type);
        } else {
            $classname = 'Pimcore\\Model\\DataObject\\ClassDefinition\Data\\' . ucfirst($type);
        }
        /** @var Data $child */
        $child = new $classname();
        $child->setName($name);
        $child->setTitle($name);
        $child->setMandatory($mandatory);
        $child->setIndex($index);
        $child->setVisibleGridView($visibleInGridView);
        $child->setVisibleSearch($visibleInSearchResult);

        return $child;
    }

    abstract public function initializeDefinitions();
}
