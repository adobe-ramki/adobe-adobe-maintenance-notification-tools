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

namespace Adobe\MaintenanceNotification\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Magento\MagentoCloud\Config\Environment;
use Magento\MagentoCloud\App\GenericException;

class SendResponse
{
    /**
     * Curl client object to call extenal system
     * @var ClientInterface
     */
    private ClientInterface $client;

    /**
     * Environment variable for cloud
     */
    const APPLICATION_FRONTEND_URL = 'APPLICATION_FRONTEND_URL';

    /**
     * Variable for cloud
     */
    const  VARIABLE= 'variables';

    /**
     * Env for cloud
     */
    const  ENV = 'env';

    /**
     * X api key for cloud
     */
    const  X_API_KEY = 'X_API_KEY';

    /**
     * Status code for api
     */
    const  STATUS_CODE = 200;

    /**
     * Headers code for api
     */
    const  HEADERS = 'headers';

    /**
     * Auth key code for api
     */
    const  AUTH_KEY = 'x-api-key';



    /**
     * Constructor method for request status update
     *
     * @param MaintenanceConfig $maintenanceConfig
     * @param ClientInterface|null $client
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment     $env,
        ClientInterface                  $client = null
    )
    {
        $this->client = $client ?: new Client();
    }

    /**
     * Status update success and return callback
     *
     * @param string $mode
     * @return array
     */
    public function execute(string $mode): array
    {
        $variables = $this->env->getApplication();
        $frontendUrl = $variables[self::VARIABLE][self::ENV][self::APPLICATION_FRONTEND_URL] ?? '';
        $xApiKey = $variables[self::VARIABLE][self::ENV][self::X_API_KEY] ?? '';
        $url = $frontendUrl . '?maintenance_mode=' . $mode;
        $options[self::HEADERS] = [self::AUTH_KEY => $xApiKey];
        $this->logger->info("STARTED MAINTENANCE FLAG " . $mode . ":" . $url);
        $response = $this->client->request('GET', $url, $options);
        $this->logger->info("ENDED MAINTENANCE FLAG " . $mode . ":" . $url);
        if($response->getStatusCode() === self::STATUS_CODE){
            return json_decode($response->getBody()->getContents(),true);
        }
        return [];
    }
}
