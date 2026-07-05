<?php

class AgencyContext
{
    public static function setCurrentAgencyId(?int $agencyId): void
    {
        if ($agencyId === null) {
            Session::remove('current_agency_id');
            return;
        }

        Session::set('current_agency_id', (int) $agencyId);
    }

    public static function getCurrentAgencyId(): ?int
    {
        $value = Session::get('current_agency_id');
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    public static function resolveAgencyId(?int $agencyId = null): ?int
    {
        if ($agencyId !== null) {
            return $agencyId;
        }

        return self::getCurrentAgencyId();
    }
}
