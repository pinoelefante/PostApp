<?php
    abstract class StatusCodes extends BasicEnum
    {
        const FAIL = -1;
        const RICHIESTA_MALFORMATA = -2;
        const METODO_ASSENTE = -3;
        
        const OK = 1;

        const LOGIN_ERROR = 10;
        const LOGIN_GIA_LOGGATO = 11;
        const LOGIN_NON_LOGGATO = 12;

        const REG_CODICE_IN_USO = 20; //registrazione utente ma codice già in uso

        const EDITOR_IMPOSSIBILE_ASSEGNARE_AMMINISTRATORE = 50;
        const EDITOR_ERRORE_CREAZIONE = 51;
        const EDITOR_UTENTE_NON_AUTORIZZATO = 52;
        const EDITOR_SEGUI_GIA = 53;
        const EDITOR_NEWS_GIA_RINGRAZIATO = 54;
        const EDITOR_NEWS_NON_TROVATA = 55;
        const EDITOR_NEWS_GIA_LETTA = 56;
    }
    abstract class CategorieEnum extends BasicEnum
    {
        const COMUNE = "Comune";
        const SCUOLA = "Scuola";
        const UNIVERSITA = "Universita";
    }

    abstract class BasicEnum {
        private static $constCacheArray = NULL;

        public static function getConstants() {
            if (self::$constCacheArray == NULL) {
                self::$constCacheArray = [];
            }
            $calledClass = get_called_class();
            if (!array_key_exists($calledClass, self::$constCacheArray)) {
                $reflect = new ReflectionClass($calledClass);
                self::$constCacheArray[$calledClass] = $reflect->getConstants();
            }
            return self::$constCacheArray[$calledClass];
        }

        public static function isValidName($name, $strict = false) {
            $constants = self::getConstants();

            if ($strict) {
                return array_key_exists($name, $constants);
            }

            $keys = array_map('strtolower', array_keys($constants));
            return in_array(strtolower($name), $keys);
        }

        public static function isValidValue($value, $strict = true) {
            $values = array_values(self::getConstants());
            return in_array($value, $values, $strict);
        }
    }
?>