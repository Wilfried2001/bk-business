<?php
/**
 * Tests pour le modèle Service
 */

class ServiceTest extends TestCase {

    public function testGetAllActifsReturnsArray(): void {
        require_once APP_PATH . '/models/Service.php';

        $service = new Service();
        $services = $service->getAllActifs();

        $this->assertIsArray($services);
    }

    public function testGetByCategorieReturnsArray(): void {
        require_once APP_PATH . '/models/Service.php';

        $service = new Service();
        $services = $service->getByCategorie('MOBILE_MONEY');

        $this->assertIsArray($services);
    }
}
