<?php
    abstract class StatusCodes extends BasicEnum
    {
        //ENVELOP_UNSET non è mai inviato dal server ma è utilizzato soltanto all'interno dell'applicazione
        const ENVELOP_UNSET = 0;
        const METHOD_NOT_IMPLEMENTED = -1000;

        const FAIL = -1;
        const RICHIESTA_MALFORMATA = -2;
        const METODO_ASSENTE = -3;
        const SQL_FAIL = -4;
		const INVALID_CLIENT = -5;
        
        const OK = 1;

        const LOGIN_ERROR = 10;
        const LOGIN_GIA_LOGGATO = 11;
        const LOGIN_NON_LOGGATO = 12;

        const REG_CODICE_IN_USO = 20; //registrazione utente ma codice già in uso

        const EDITOR_IMPOSSIBILE_ASSEGNARE_AMMINISTRATORE = 50;
        const EDITOR_ERRORE_CREAZIONE = 51;
        const EDITOR_UTENTE_NON_AUTORIZZATO = 52;
        const SEGUI_GIA = 53;
        const NEWS_GIA_RINGRAZIATO = 54;
        const EDITOR_NEWS_NON_TROVATA = 55;
        const NEWS_GIA_LETTA = 56;
        const EDITOR_NON_SEGUITO = 57;
        const NEWS_LETTURA_GIA_CONFERMATA = 58;

        const SCUOLA_IMPOSSIBILE_ASSEGNARE_PRESIDE = 60;
        const SCUOLA_USERNAME_NON_VALIDO = 61;
        const SCUOLA_PASSWORD_ERRATA = 62;
        const SCUOLA_PERMESSI_INSUFFICIENTI = 63;
        const SCUOLA_PLESSO_DUPLICATO = 64;
        const SCUOLA_PLESSO_NON_PRESENTE = 65;
        const SCUOLA_GRADO_DUPLICATO = 66;
        const SCUOLA_ERRORE_INSERIMENTO_SEZIONE = 67;
        const SCUOLA_GRADO_NON_PRESENTE = 68;

        const CODICE_INVALIDO = 90;
        const CODICE_USATO = 91;
        const CODICE_GIA_IN_USO = 92;
        const CODICE_MAI_USATO = 93;
        const CODICE_CAMPI_INVALIDI = 94;

        const NEWS_COMMON_TIPO_NEWS_INVALIDO = 80;
    }
    abstract class DatabaseReturns extends BasicEnum
    {
        const RETURN_BOOLEAN = 1;
        const RETURN_AFFECTED_ROWS = 2;
        const RETURN_INSERT_ID = 3;
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
                self::$constCacheArray = array();
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