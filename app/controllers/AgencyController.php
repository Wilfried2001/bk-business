<?php

class AgencyController extends Controller
{
    public function switchAgency(): void
    {
        Auth::requireAuth();
        $this->verifyCsrf();

        $agencyId = $this->post('agency_id');
        if ($agencyId === '' || $agencyId === null) {
            AgencyContext::setCurrentAgencyId(null);
        } else {
            AgencyContext::setCurrentAgencyId((int)$agencyId);
        }

        $this->redirect('dashboard');
    }
}
