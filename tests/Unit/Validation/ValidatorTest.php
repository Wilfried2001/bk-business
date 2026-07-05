<?php
/**
 * Tests pour le validateur
 * Teste les règles de validation principales
 */

class ValidatorTest extends TestCase {

    /**
     * Teste la validation d'un champ requis
     */
    public function testValidationRequired(): void {
        require_once APP_PATH . '/validation/Validator.php';

        $data = ['email' => ''];
        $rules = ['email' => 'required'];

        $errors = Validator::validate($data, $rules);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('requis', $errors[0]);
    }

    /**
     * Teste la validation d'un email
     */
    public function testValidationEmail(): void {
        require_once APP_PATH . '/validation/Validator.php';

        // Email invalide
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'email'];

        $errors = Validator::validate($data, $rules);
        $this->assertNotEmpty($errors);

        // Email valide
        $data = ['email' => 'test@example.com'];
        $errors = Validator::validate($data, $rules);
        $this->assertEmpty($errors);
    }

    /**
     * Teste la validation numérique
     */
    public function testValidationNumeric(): void {
        require_once APP_PATH . '/validation/Validator.php';

        // Valeur non numérique
        $data = ['montant' => 'abc'];
        $rules = ['montant' => 'numeric'];

        $errors = Validator::validate($data, $rules);
        $this->assertNotEmpty($errors);

        // Valeur numérique
        $data = ['montant' => '50000.50'];
        $errors = Validator::validate($data, $rules);
        $this->assertEmpty($errors);
    }

    /**
     * Teste la validation entier
     */
    public function testValidationInteger(): void {
        require_once APP_PATH . '/validation/Validator.php';

        // Non entier
        $data = ['age' => '25.5'];
        $rules = ['age' => 'integer'];

        $errors = Validator::validate($data, $rules);
        $this->assertNotEmpty($errors);

        // Entier valide
        $data = ['age' => '25'];
        $errors = Validator::validate($data, $rules);
        $this->assertEmpty($errors);
    }

    /**
     * Teste les validations multiples
     */
    public function testValidationMultiples(): void {
        require_once APP_PATH . '/validation/Validator.php';

        $data = [
            'email' => 'invalid',
            'montant' => '',
        ];

        $rules = [
            'email' => 'required|email',
            'montant' => 'required|numeric',
        ];

        $errors = Validator::validate($data, $rules);

        $this->assertGreaterThanOrEqual(2, count($errors));
    }
}
