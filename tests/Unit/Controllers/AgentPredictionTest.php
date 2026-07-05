<?php

class AgentPredictionTest extends TestCase
{
    public function testBuildPredictionSummaryUsesRecentTrend(): void
    {
        $controller = new AgentIAController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('buildPredictionSummary');
        $method->setAccessible(true);

        $history = [
            ['jour' => '2026-07-01', 'nb_transactions' => 10, 'volume_total' => 100000],
            ['jour' => '2026-07-02', 'nb_transactions' => 12, 'volume_total' => 120000],
            ['jour' => '2026-07-03', 'nb_transactions' => 15, 'volume_total' => 150000],
        ];

        $summary = $method->invoke($controller, $history);

        $this->assertArrayHasKey('forecast_transactions', $summary);
        $this->assertArrayHasKey('forecast_volume', $summary);
        $this->assertGreaterThan(0, $summary['forecast_transactions']);
        $this->assertGreaterThan(0, $summary['forecast_volume']);
    }

    public function testBuildPredictionSummaryReturnsZeroesForEmptyHistory(): void
    {
        $controller = new AgentIAController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('buildPredictionSummary');
        $method->setAccessible(true);

        $summary = $method->invoke($controller, []);

        $this->assertSame(0, $summary['forecast_transactions']);
        $this->assertSame(0, $summary['forecast_volume']);
    }

    public function testDeduceMovementTypeMapsImpactToEntryOrExit(): void
    {
        $controller = new TransactionController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('deduireTypeMouvement');
        $method->setAccessible(true);

        $this->assertSame('ENTREE', $method->invoke($controller, ['impact_caisse' => 1]));
        $this->assertSame('SORTIE', $method->invoke($controller, ['impact_float' => -2]));
        $this->assertSame('NEUTRE', $method->invoke($controller, ['impact_caisse' => 0, 'impact_float' => 0]));
    }

    public function testBuildPredictionSummaryDampensVolatileSpike(): void
    {
        $controller = new AgentIAController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('buildPredictionSummary');
        $method->setAccessible(true);

        $history = [
            ['jour' => '2026-07-01', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-02', 'nb_transactions' => 12, 'volume_total' => 12000],
            ['jour' => '2026-07-03', 'nb_transactions' => 15, 'volume_total' => 15000],
            ['jour' => '2026-07-04', 'nb_transactions' => 80, 'volume_total' => 80000],
        ];

        $summary = $method->invoke($controller, $history);

        $this->assertLessThan(80, $summary['forecast_transactions']);
        $this->assertLessThan(80000, $summary['forecast_volume']);
    }

    public function testBuildPredictionSummaryUsesWeeklySeasonality(): void
    {
        $controller = new AgentIAController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('buildPredictionSummary');
        $method->setAccessible(true);

        $history = [
            ['jour' => '2026-06-22', 'nb_transactions' => 20, 'volume_total' => 20000],
            ['jour' => '2026-06-23', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-24', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-25', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-26', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-27', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-28', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-06-29', 'nb_transactions' => 20, 'volume_total' => 20000],
            ['jour' => '2026-06-30', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-01', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-02', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-03', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-04', 'nb_transactions' => 10, 'volume_total' => 10000],
            ['jour' => '2026-07-05', 'nb_transactions' => 10, 'volume_total' => 10000],
        ];

        $summary = $method->invoke($controller, $history);

        $this->assertGreaterThan(15, $summary['forecast_transactions']);
        $this->assertGreaterThan(15000, $summary['forecast_volume']);
    }
}
