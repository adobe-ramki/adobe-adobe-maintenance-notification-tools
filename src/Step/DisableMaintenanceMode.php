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
use Magento\MagentoCloud\Step\StepInterface;
use Magento\MagentoCloud\App\Error;
use Magento\MagentoCloud\App\GenericException;
use Magento\MagentoCloud\Util\MaintenanceModeSwitcher;

class DisableMaintenanceMode implements StepInterface
{
    /**
     * @var MaintenanceModeSwitcher
     */
    private $switcher;

    /**
     * Maintance mode flag
     */
    private const MODE = 'DISABLE';

    /**
     * @var SendResponse
     */
    private $sendResponse;

    /**
     *
     * @param MaintenanceModeSwitcher $switcher
     * @param SendResponse $sendResponse
     */
    public function __construct(
        MaintenanceModeSwitcher $switcher,
        SendResponse $sendResponse
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
            $this->switcher->disable();
            $this->sendResponse->execute(self::MODE);
        } catch (GenericException $e) {
            throw new StepException($e->getMessage(), Error::DEPLOY_MAINTENANCE_MODE_DISABLING_FAILED, $e);
        }
    }
}
