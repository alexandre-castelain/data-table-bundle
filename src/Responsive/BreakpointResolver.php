<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

class BreakpointResolver
{
    /**
     * @param array<string, int> $breakpoints Associative array of name => max width, sorted ascending
     */
    public function __construct(
        private readonly array $breakpoints,
    ) {
    }

    /**
     * Resolves a pixel width to the name of the active breakpoint.
     *
     * Returns the largest configured breakpoint when width exceeds all thresholds.
     */
    public function resolve(int $width): string
    {
        foreach ($this->breakpoints as $name => $maxWidth) {
            if ($width <= $maxWidth) {
                return $name;
            }
        }

        return array_key_last($this->breakpoints);
    }

    /**
     * Checks whether a column is visible at the given active breakpoint.
     *
     * A column with $minimumBreakpoint is visible when the active breakpoint
     * is at the same position or higher in the configured breakpoints order.
     */
    public function isVisible(string $activeBreakpoint, string $minimumBreakpoint): bool
    {
        $names = array_keys($this->breakpoints);
        $activeIndex = array_search($activeBreakpoint, $names, true);
        $minimumIndex = array_search($minimumBreakpoint, $names, true);

        if (false === $minimumIndex) {
            return true;
        }

        return $activeIndex >= $minimumIndex;
    }

    /**
     * @return array<string, int>
     */
    public function getBreakpoints(): array
    {
        return $this->breakpoints;
    }

    /**
     * Resolves a User-Agent device type to a fallback breakpoint name.
     *
     * Phone → smallest breakpoint, Tablet → median breakpoint, Desktop → largest breakpoint.
     */
    public function resolveUaFallback(Device $device): ?string
    {
        $names = array_keys($this->breakpoints);

        if ([] === $names) {
            return null;
        }

        return match ($device) {
            Device::Phone => $names[0],
            Device::Tablet => $names[(int) floor(count($names) / 2)],
            Device::Desktop => $names[count($names) - 1],
        };
    }
}
