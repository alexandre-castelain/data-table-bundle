<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use Kreyu\Bundle\DataTableBundle\Responsive\DeviceDetectorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class HttpFoundationRequestHandlerDeviceDetectionTest extends TestCase
{
    public function testDeviceParameterOverridesUserAgent(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->never())->method('detect');

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setDevice')
            ->with(Device::Phone);

        $handler->handle($dataTable, $this->createRequest(['_device' => 'phone']));
    }

    public function testFallsBackToUserAgentWhenNoDeviceParameter(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Tablet);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setDevice')
            ->with(Device::Tablet);

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testFallsBackToUserAgentWhenDeviceParameterIsInvalid(): void
    {
        $detector = $this->createMock(DeviceDetectorInterface::class);
        $detector->expects($this->once())
            ->method('detect')
            ->willReturn(Device::Desktop);

        $handler = new HttpFoundationRequestHandler($detector);

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setDevice')
            ->with(Device::Desktop);

        $handler->handle($dataTable, $this->createRequest(['_device' => 'invalid']));
    }

    public function testNoDeviceDetectionWithoutDetector(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->never())->method('setDevice');

        $handler->handle($dataTable, $this->createRequest());
    }

    public function testDeviceParameterWorksWithoutDetector(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->once())
            ->method('setDevice')
            ->with(Device::Tablet);

        $handler->handle($dataTable, $this->createRequest(['_device' => 'tablet']));
    }

    public function testAllDeviceParameterValues(): void
    {
        $handler = new HttpFoundationRequestHandler();

        foreach (Device::cases() as $device) {
            $dataTable = $this->createDataTableMock();
            $dataTable->expects($this->once())
                ->method('setDevice')
                ->with($device);

            $handler->handle($dataTable, $this->createRequest(['_device' => $device->value]));
        }
    }

    public function testNullRequestDoesNothing(): void
    {
        $handler = new HttpFoundationRequestHandler();

        $dataTable = $this->createDataTableMock();
        $dataTable->expects($this->never())->method('setDevice');

        $handler->handle($dataTable, null);
    }

    private function createRequest(array $query = []): Request
    {
        $request = Request::create('/?' . http_build_query($query));
        $request->headers->set('Turbo-Frame', 'kreyu_data_table_test');

        return $request;
    }

    private function createDataTableMock(): DataTableInterface&\PHPUnit\Framework\MockObject\MockObject
    {
        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isFiltrationEnabled')->willReturn(false);
        $config->method('isSortingEnabled')->willReturn(false);
        $config->method('isPaginationEnabled')->willReturn(false);
        $config->method('isPersonalizationEnabled')->willReturn(false);
        $config->method('isExportingEnabled')->willReturn(false);

        $dataTable = $this->createMock(DataTableInterface::class);
        $dataTable->method('getConfig')->willReturn($config);

        return $dataTable;
    }
}
