<?php

class AgencyContextTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
        Session::destroy();
    }

    public function testSetAndGetCurrentAgencyId(): void
    {
        AgencyContext::setCurrentAgencyId(42);

        $this->assertSame(42, AgencyContext::getCurrentAgencyId());
    }

    public function testResolveAgencyIdUsesExplicitValueFirst(): void
    {
        AgencyContext::setCurrentAgencyId(10);

        $this->assertSame(7, AgencyContext::resolveAgencyId(7));
        $this->assertSame(10, AgencyContext::resolveAgencyId(null));
    }
}
