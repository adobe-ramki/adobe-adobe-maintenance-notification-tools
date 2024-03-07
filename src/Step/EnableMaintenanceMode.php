<?php

declare(strict_types=1);
/*******************************************************************************
 * ADOBE CONFIDENTIAL
 * ___________________
 *
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Adobe permits you to use and modify this file
 * in accordance with the terms of the Adobe license agreement
 * accompanying it (see LICENSE_ADOBE_PS.txt).
 * If you have received this file from a source other than Adobe,
 * then your use, modification, or distribution of it
 * requires the prior written permission from Adobe.
 ******************************************************************************/


namespace Adobe\MaintenanceNotification\Step;

use Adobe\MaintenanceNotification\Service\SendResponse;
use Magento\MagentoCloud\App\Error;
use Magento\MagentoCloud\App\GenericException;
use Magento\MagentoCloud\Step\StepInterface;
use Magento\MagentoCloud\Util\MaintenanceModeSwitcher;
use Psr\Log\LoggerInterface;
use Magento\MagentoCloud\Filesystem\Flag\Manager as FlagManager;

class EnableMaintenanceMode implements StepInterface
{
    /**
     * @var MaintenanceModeSwitcher
     */
    private $switcher;

    /**
     * Maintance mode flag
     */
    private const MODE = 'ENABLE';

    /**
     * Status flag
     */
    private const STATUS = 'status';

    /**
     * @var SendResponse
     */
    private $sendResponse;

    /**
     *
     * @param MaintenanceModeSwitcher $switcher
     * @param SendResponse $sendResponse
     * @param LoggerInterface $logger
     */
    public function __construct(
        MaintenanceModeSwitcher          $switcher,
        SendResponse                     $sendResponse,
        private readonly LoggerInterface $logger,
        private readonly FlagManager $flagManager
    )
    {
        $this->switcher = $switcher;
        $this->sendResponse = $sendResponse;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $response = $this->sendResponse->execute(self::MODE);
            if ($response[self::STATUS]) {
                $this->switcher->enable();
            } else {
                $this->logger->info("DEPLOYMENT STOPPED : No response from frontend side");
                $this->flagManager->set(FlagManager::FLAG_DEPLOY_HOOK_IS_FAILED);
                exit;
            }
        } catch (GenericException $e) {
            throw new StepException($e->getMessage(), Error::DEPLOY_MAINTENANCE_MODE_ENABLING_FAILED, $e);
        }
    }
}
