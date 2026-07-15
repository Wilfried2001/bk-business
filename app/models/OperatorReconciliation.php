<?php

class OperatorReconciliation
{
    public function normalizeOperatorName(string $name): string
    {
        $normalized = trim(strtolower($name));

        if (str_contains($normalized, 'orange')) {
            return 'Orange Money';
        }

        if (str_contains($normalized, 'mtn')) {
            return 'MTN Money';
        }

        if (str_contains($normalized, 'ria')) {
            return 'Ria';
        }

        return ucfirst($normalized);
    }

    public function calculateDifference(float $expected, float $actual, float $tolerance = 5.0): array
    {
        $difference = abs($expected - $actual);
        $isMatch = $difference <= $tolerance;

        return [
            'difference' => round($difference, 2),
            'tolerance' => round($tolerance, 2),
            'status' => $isMatch ? 'MATCH' : 'DIVERGENCE',
        ];
    }
}
