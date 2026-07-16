<?php
// ============================================================
//  app/validation/Validator.php — Fichier commenté
// ============================================================

// Classe Validator : implémente la logique métier pour cette partie de l’application
class Validator {

    public static function validate(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $constraints = explode('|', $ruleSet);

            foreach ($constraints as $rule) {
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $parameter] = explode(':', $rule, 2);
                } else {
                    $ruleName   = $rule;
                    $parameter  = null;
                }

                $message = self::applyRule($field, $value, $ruleName, $parameter, $data);
                if ($message !== null) {
                    $errors[] = $message;
                }
            }
        }

        return array_values(array_unique($errors));
    }

    private static function applyRule(string $field, mixed $value, string $rule, ?string $parameter, array $data): ?string {
        $label = ucfirst(str_replace('_', ' ', $field));

        if ($rule === 'required') {
            if (self::isEmpty($value)) {
                return "Le champ {$label} est requis.";
            }
            return null;
        }

        if ($rule === 'email') {
            if (!self::isEmpty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return "Le champ {$label} doit être une adresse email valide.";
            }
            return null;
        }

        if ($rule === 'numeric') {
            if (!self::isEmpty($value) && !is_numeric($value)) {
                return "Le champ {$label} doit être un nombre.";
            }
            return null;
        }

        if ($rule === 'integer') {
            if (!self::isEmpty($value) && filter_var($value, FILTER_VALIDATE_INT) === false) {
                return "Le champ {$label} doit être un entier.";
            }
            return null;
        }

        if ($rule === 'min') {
            if (!self::isEmpty($value) && is_numeric($value) && (float)$value < (float)$parameter) {
                return "Le champ {$label} doit être supérieur ou égal à {$parameter}.";
            }
            return null;
        }

        if ($rule === 'max') {
            if (!self::isEmpty($value) && is_numeric($value) && (float)$value > (float)$parameter) {
                return "Le champ {$label} doit être inférieur ou égal à {$parameter}.";
            }
            return null;
        }

        if ($rule === 'positive') {
            if (!self::isEmpty($value) && (!is_numeric($value) || (float)$value <= 0)) {
                return "Le champ {$label} doit être un nombre strictement positif.";
            }
            return null;
        }

        if ($rule === 'same') {
            if (!self::isEmpty($value) && ($value !== ($data[$parameter] ?? null))) {
                $otherLabel = ucfirst(str_replace('_', ' ', $parameter));
                return "Le champ {$label} doit être identique au champ {$otherLabel}.";
            }
            return null;
        }

        if ($rule === 'nullable') {
            return null;
        }

        if ($rule === 'required_if') {
            if (strpos($parameter, ',') !== false) {
                [$otherField, $expected] = explode(',', $parameter, 2);
                if (($data[$otherField] ?? null) === $expected && self::isEmpty($value)) {
                    return "Le champ {$label} est requis lorsque " . ucfirst(str_replace('_', ' ', $otherField)) . " est {$expected}.";
                }
            }
            return null;
        }

        if ($rule === 'regex') {
            if (!self::isEmpty($value) && !preg_match($parameter, (string)$value)) {
                return "Le champ {$label} ne respecte pas le format attendu.";
            }
            return null;
        }

        if ($rule === 'url') {
            if (!self::isEmpty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                return "Le champ {$label} doit être une URL valide.";
            }
            return null;
        }

        if ($rule === 'date') {
            if (!self::isEmpty($value) && strtotime((string)$value) === false) {
                return "Le champ {$label} doit être une date valide.";
            }
            return null;
        }

        if ($rule === 'min_length') {
            if (!self::isEmpty($value) && mb_strlen((string)$value) < (int)$parameter) {
                return "Le champ {$label} doit contenir au moins {$parameter} caractères.";
            }
            return null;
        }

        if ($rule === 'max_length') {
            if (!self::isEmpty($value) && mb_strlen((string)$value) > (int)$parameter) {
                return "Le champ {$label} ne doit pas dépasser {$parameter} caractères.";
            }
            return null;
        }

        if ($rule === 'in') {
            $allowed = explode(',', $parameter);
            if (!self::isEmpty($value) && !in_array($value, $allowed, true)) {
                return "Le champ {$label} n'est pas valide.";
            }
            return null;
        }

        if ($rule === 'array') {
            if (!self::isEmpty($value) && !is_array($value)) {
                return "Le champ {$label} doit être un tableau.";
            }
            return null;
        }

        return null;
    }

    private static function isEmpty(mixed $value): bool {
        return $value === null || (is_string($value) && trim($value) === '');
    }
}
