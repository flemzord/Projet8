<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;

/**
 * @internal
 */
class UpdateProductStatusHandler implements UpdateProductStatusHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param ProductRepository $productRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * @param UpdateProductStatusCommand $command
     */
    public function handle(UpdateProductStatusCommand $command)
    {
        $product = $this->productRepository->get($command->getProductId());
        $initialState = (bool) $product->active;
        $product->active = $command->getEnable();
        $this->productRepository->partialUpdate(
            $product,
            ['active'],
            CannotUpdateProductException::FAILED_UPDATE_STATUS
        );

        // If status changed we need to update its indexes (we check if it is necessary because index build can be
        // an expensive operation).
        if ($initialState !== $command->getEnable()) {
            $this->productIndexationUpdater->updateIndexation($product);
        }
    }
}
