<?php

class OperatorReconciliationTest extends TestCase
{
    public function testNormalizeOperatorNameUsesCanonicalTokens(): void
    {
        require_once APP_PATH . '/models/OperatorReconciliation.php';

        $service = new OperatorReconciliation();

        $this->assertSame('Orange Money', $service->normalizeOperatorName('orange money'));
        $this->assertSame('MTN Money', $service->normalizeOperatorName('mtn money'));
        $this->assertSame('Ria', $service->normalizeOperatorName('ria'));
    }

    public function testCalculateDifferenceFlagsVarianceAndTolerance(): void
    {
        require_once APP_PATH . '/models/OperatorReconciliation.php';

        $service = new OperatorReconciliation();

        $matching = $service->calculateDifference(1000, 1000);
        $divergence = $service->calculateDifference(1000, 950);
        $withinTolerance = $service->calculateDifference(1000, 995);

        $this->assertSame('MATCH', $matching['status']);
        $this->assertSame('DIVERGENCE', $divergence['status']);
        $this->assertSame('MATCH', $withinTolerance['status']);
        $this->assertSame(50.0, $divergence['difference']);
    }
}
