<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

enum Device: string
{
    case Phone = 'phone';
    case Tablet = 'tablet';
    case Desktop = 'desktop';

    /**
     * Returns true if this device is at least as large as $minimum.
     *
     * Cascade: Phone < Tablet < Desktop
     *
     * For example, if $minimum is Tablet, then Tablet and Desktop return true, but Phone returns false.
     */
    public function isAtLeast(self $minimum): bool
    {
        return $this->order() >= $minimum->order();
    }

    private function order(): int
    {
        return match ($this) {
            self::Phone => 0,
            self::Tablet => 1,
            self::Desktop => 2,
        };
    }
}
