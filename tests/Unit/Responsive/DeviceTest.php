<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
    #[DataProvider('provideIsAtLeastCases')]
    public function testIsAtLeast(Device $device, Device $minimum, bool $expected): void
    {
        $this->assertSame($expected, $device->isAtLeast($minimum));
    }

    public static function provideIsAtLeastCases(): iterable
    {
        // Phone is at least Phone
        yield 'phone >= phone' => [Device::Phone, Device::Phone, true];
        // Phone is NOT at least Tablet
        yield 'phone >= tablet' => [Device::Phone, Device::Tablet, false];
        // Phone is NOT at least Desktop
        yield 'phone >= desktop' => [Device::Phone, Device::Desktop, false];

        // Tablet is at least Phone
        yield 'tablet >= phone' => [Device::Tablet, Device::Phone, true];
        // Tablet is at least Tablet
        yield 'tablet >= tablet' => [Device::Tablet, Device::Tablet, true];
        // Tablet is NOT at least Desktop
        yield 'tablet >= desktop' => [Device::Tablet, Device::Desktop, false];

        // Desktop is at least everything
        yield 'desktop >= phone' => [Device::Desktop, Device::Phone, true];
        yield 'desktop >= tablet' => [Device::Desktop, Device::Tablet, true];
        yield 'desktop >= desktop' => [Device::Desktop, Device::Desktop, true];
    }

    public function testStringValues(): void
    {
        $this->assertSame('phone', Device::Phone->value);
        $this->assertSame('tablet', Device::Tablet->value);
        $this->assertSame('desktop', Device::Desktop->value);
    }

    public function testTryFromValidValues(): void
    {
        $this->assertSame(Device::Phone, Device::tryFrom('phone'));
        $this->assertSame(Device::Tablet, Device::tryFrom('tablet'));
        $this->assertSame(Device::Desktop, Device::tryFrom('desktop'));
    }

    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(Device::tryFrom('invalid'));
        $this->assertNull(Device::tryFrom(''));
    }
}
