<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
/**
 * SQL Formatter is a collection of utilities for debugging SQL queries.
 * It includes methods for formatting, syntax highlighting, removing comments, etc.
 *
 * @package    SqlFormatter
 * @author     Jeremy Dorn <jeremy@jeremydorn.com>
 * @author     Florin Patan <florinpatan@gmail.com>
 * @copyright  2013 Jeremy Dorn
 * @license    http://opensource.org/licenses/MIT
 * @link       http://github.com/jdorn/sql-formatter
 * @version    1.2.16
 */
class SqlFormatter
{
    protected static $reserved = array("ACCESSIBLE", "ACTION", "AGAINST", "AGGREGATE", "ALGORITHM", "ALL", "ALTER", "ANALYSE", "ANALYZE", "AS", "ASC", "AUTOCOMMIT", "AUTO_INCREMENT", "BACKUP", "BEGIN", "BETWEEN", "BINLOG", "BOTH", "CASCADE", "CASE", "CHANGE", "CHANGED", "CHARACTER SET", "CHARSET", "CHECK", "CHECKSUM", "COLLATE", "COLLATION", "COLUMN", "COLUMNS", "COMMENT", "COMMIT", "COMMITTED", "COMPRESSED", "CONCURRENT", "CONSTRAINT", "CONTAINS", "CONVERT", "CREATE", "CROSS", "CURRENT_TIMESTAMP", "DATABASE", "DATABASES", "DAY", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "DEFAULT", "DEFINER", "DELAYED", "DELETE", "DESC", "DESCRIBE", "DETERMINISTIC", "DISTINCT", "DISTINCTROW", "DIV", "DO", "DUMPFILE", "DUPLICATE", "DYNAMIC", "ELSE", "ENCLOSED", "END", "ENGINE", "ENGINE_TYPE", "ENGINES", "ESCAPE", "ESCAPED", "EVENTS", "EXECUTE", "EXISTS", "EXPLAIN", "EXTENDED", "FAST", "FIELDS", "FILE", "FIRST", "FIXED", "FLUSH", "FOR", "FORCE", "FOREIGN", "FULL", "FULLTEXT", "FUNCTION", "GLOBAL", "GRANT", "GRANTS", "GROUP_CONCAT", "HEAP", "HIGH_PRIORITY", "HOSTS", "HOUR", "HOUR_MINUTE", "HOUR_SECOND", "IDENTIFIED", "IF", "IFNULL", "IGNORE", "IN", "INDEX", "INDEXES", "INFILE", "INSERT", "INSERT_ID", "INSERT_METHOD", "INTERVAL", "INTO", "INVOKER", "IS", "ISOLATION", "KEY", "KEYS", "KILL", "LAST_INSERT_ID", "LEADING", "LEVEL", "LIKE", "LINEAR", "LINES", "LOAD", "LOCAL", "LOCK", "LOCKS", "LOGS", "LOW_PRIORITY", "MARIA", "MASTER", "MASTER_CONNECT_RETRY", "MASTER_HOST", "MASTER_LOG_FILE", "MATCH", "MAX_CONNECTIONS_PER_HOUR", "MAX_QUERIES_PER_HOUR", "MAX_ROWS", "MAX_UPDATES_PER_HOUR", "MAX_USER_CONNECTIONS", "MEDIUM", "MERGE", "MINUTE", "MINUTE_SECOND", "MIN_ROWS", "MODE", "MODIFY", "MONTH", "MRG_MYISAM", "MYISAM", "NAMES", "NATURAL", "NOT", "NOW()", "NULL", "OFFSET", "ON", "OPEN", "OPTIMIZE", "OPTION", "OPTIONALLY", "ON UPDATE", "ON DELETE", "OUTFILE", "PACK_KEYS", "PAGE", "PARTIAL", "PARTITION", "PARTITIONS", "PASSWORD", "PRIMARY", "PRIVILEGES", "PROCEDURE", "PROCESS", "PROCESSLIST", "PURGE", "QUICK", "RANGE", "RAID0", "RAID_CHUNKS", "RAID_CHUNKSIZE", "RAID_TYPE", "READ", "READ_ONLY", "READ_WRITE", "REFERENCES", "REGEXP", "RELOAD", "RENAME", "REPAIR", "REPEATABLE", "REPLACE", "REPLICATION", "RESET", "RESTORE", "RESTRICT", "RETURN", "RETURNS", "REVOKE", "RLIKE", "ROLLBACK", "ROW", "ROWS", "ROW_FORMAT", "SECOND", "SECURITY", "SEPARATOR", "SERIALIZABLE", "SESSION", "SHARE", "SHOW", "SHUTDOWN", "SLAVE", "SONAME", "SOUNDS", "SQL", "SQL_AUTO_IS_NULL", "SQL_BIG_RESULT", "SQL_BIG_SELECTS", "SQL_BIG_TABLES", "SQL_BUFFER_RESULT", "SQL_CALC_FOUND_ROWS", "SQL_LOG_BIN", "SQL_LOG_OFF", "SQL_LOG_UPDATE", "SQL_LOW_PRIORITY_UPDATES", "SQL_MAX_JOIN_SIZE", "SQL_QUOTE_SHOW_CREATE", "SQL_SAFE_UPDATES", "SQL_SELECT_LIMIT", "SQL_SLAVE_SKIP_COUNTER", "SQL_SMALL_RESULT", "SQL_WARNINGS", "SQL_CACHE", "SQL_NO_CACHE", "START", "STARTING", "STATUS", "STOP", "STORAGE", "STRAIGHT_JOIN", "STRING", "STRIPED", "SUPER", "TABLE", "TABLES", "TEMPORARY", "TERMINATED", "THEN", "TO", "TRAILING", "TRANSACTIONAL", "TRUE", "TRUNCATE", "TYPE", "TYPES", "UNCOMMITTED", "UNIQUE", "UNLOCK", "UNSIGNED", "USAGE", "USE", "USING", "VARIABLES", "VIEW", "WHEN", "WITH", "WORK", "WRITE", "YEAR_MONTH");
    protected static $reserved_toplevel = array("SELECT", "FROM", "WHERE", "SET", "ORDER BY", "GROUP BY", "LIMIT", "DROP", "VALUES", "UPDATE", "HAVING", "ADD", "AFTER", "ALTER TABLE", "DELETE FROM", "UNION ALL", "UNION", "EXCEPT", "INTERSECT");
    protected static $reserved_newline = array("LEFT OUTER JOIN", "RIGHT OUTER JOIN", "LEFT JOIN", "RIGHT JOIN", "OUTER JOIN", "INNER JOIN", "JOIN", "XOR", "OR", "AND");
    protected static $functions = array("ABS", "ACOS", "ADDDATE", "ADDTIME", "AES_DECRYPT", "AES_ENCRYPT", "AREA", "ASBINARY", "ASCII", "ASIN", "ASTEXT", "ATAN", "ATAN2", "AVG", "BDMPOLYFROMTEXT", "BDMPOLYFROMWKB", "BDPOLYFROMTEXT", "BDPOLYFROMWKB", "BENCHMARK", "BIN", "BIT_AND", "BIT_COUNT", "BIT_LENGTH", "BIT_OR", "BIT_XOR", "BOUNDARY", "BUFFER", "CAST", "CEIL", "CEILING", "CENTROID", "CHAR", "CHARACTER_LENGTH", "CHARSET", "CHAR_LENGTH", "COALESCE", "COERCIBILITY", "COLLATION", "COMPRESS", "CONCAT", "CONCAT_WS", "CONNECTION_ID", "CONTAINS", "CONV", "CONVERT", "CONVERT_TZ", "CONVEXHULL", "COS", "COT", "COUNT", "CRC32", "CROSSES", "CURDATE", "CURRENT_DATE", "CURRENT_TIME", "CURRENT_TIMESTAMP", "CURRENT_USER", "CURTIME", "DATABASE", "DATE", "DATEDIFF", "DATE_ADD", "DATE_DIFF", "DATE_FORMAT", "DATE_SUB", "DAY", "DAYNAME", "DAYOFMONTH", "DAYOFWEEK", "DAYOFYEAR", "DECODE", "DEFAULT", "DEGREES", "DES_DECRYPT", "DES_ENCRYPT", "DIFFERENCE", "DIMENSION", "DISJOINT", "DISTANCE", "ELT", "ENCODE", "ENCRYPT", "ENDPOINT", "ENVELOPE", "EQUALS", "EXP", "EXPORT_SET", "EXTERIORRING", "EXTRACT", "EXTRACTVALUE", "FIELD", "FIND_IN_SET", "FLOOR", "FORMAT", "FOUND_ROWS", "FROM_DAYS", "FROM_UNIXTIME", "GEOMCOLLFROMTEXT", "GEOMCOLLFROMWKB", "GEOMETRYCOLLECTION", "GEOMETRYCOLLECTIONFROMTEXT", "GEOMETRYCOLLECTIONFROMWKB", "GEOMETRYFROMTEXT", "GEOMETRYFROMWKB", "GEOMETRYN", "GEOMETRYTYPE", "GEOMFROMTEXT", "GEOMFROMWKB", "GET_FORMAT", "GET_LOCK", "GLENGTH", "GREATEST", "GROUP_CONCAT", "GROUP_UNIQUE_USERS", "HEX", "HOUR", "IF", "IFNULL", "INET_ATON", "INET_NTOA", "INSERT", "INSTR", "INTERIORRINGN", "INTERSECTION", "INTERSECTS", "INTERVAL", "ISCLOSED", "ISEMPTY", "ISNULL", "ISRING", "ISSIMPLE", "IS_FREE_LOCK", "IS_USED_LOCK", "LAST_DAY", "LAST_INSERT_ID", "LCASE", "LEAST", "LEFT", "LENGTH", "LINEFROMTEXT", "LINEFROMWKB", "LINESTRING", "LINESTRINGFROMTEXT", "LINESTRINGFROMWKB", "LN", "LOAD_FILE", "LOCALTIME", "LOCALTIMESTAMP", "LOCATE", "LOG", "LOG10", "LOG2", "LOWER", "LPAD", "LTRIM", "MAKEDATE", "MAKETIME", "MAKE_SET", "MASTER_POS_WAIT", "MAX", "MBRCONTAINS", "MBRDISJOINT", "MBREQUAL", "MBRINTERSECTS", "MBROVERLAPS", "MBRTOUCHES", "MBRWITHIN", "MD5", "MICROSECOND", "MID", "MIN", "MINUTE", "MLINEFROMTEXT", "MLINEFROMWKB", "MOD", "MONTH", "MONTHNAME", "MPOINTFROMTEXT", "MPOINTFROMWKB", "MPOLYFROMTEXT", "MPOLYFROMWKB", "MULTILINESTRING", "MULTILINESTRINGFROMTEXT", "MULTILINESTRINGFROMWKB", "MULTIPOINT", "MULTIPOINTFROMTEXT", "MULTIPOINTFROMWKB", "MULTIPOLYGON", "MULTIPOLYGONFROMTEXT", "MULTIPOLYGONFROMWKB", "NAME_CONST", "NULLIF", "NUMGEOMETRIES", "NUMINTERIORRINGS", "NUMPOINTS", "OCT", "OCTET_LENGTH", "OLD_PASSWORD", "ORD", "OVERLAPS", "PASSWORD", "PERIOD_ADD", "PERIOD_DIFF", "PI", "POINT", "POINTFROMTEXT", "POINTFROMWKB", "POINTN", "POINTONSURFACE", "POLYFROMTEXT", "POLYFROMWKB", "POLYGON", "POLYGONFROMTEXT", "POLYGONFROMWKB", "POSITION", "POW", "POWER", "QUARTER", "QUOTE", "RADIANS", "RAND", "RELATED", "RELEASE_LOCK", "REPEAT", "REPLACE", "REVERSE", "RIGHT", "ROUND", "ROW_COUNT", "RPAD", "RTRIM", "SCHEMA", "SECOND", "SEC_TO_TIME", "SESSION_USER", "SHA", "SHA1", "SIGN", "SIN", "SLEEP", "SOUNDEX", "SPACE", "SQRT", "SRID", "STARTPOINT", "STD", "STDDEV", "STDDEV_POP", "STDDEV_SAMP", "STRCMP", "STR_TO_DATE", "SUBDATE", "SUBSTR", "SUBSTRING", "SUBSTRING_INDEX", "SUBTIME", "SUM", "SYMDIFFERENCE", "SYSDATE", "SYSTEM_USER", "TAN", "TIME", "TIMEDIFF", "TIMESTAMP", "TIMESTAMPADD", "TIMESTAMPDIFF", "TIME_FORMAT", "TIME_TO_SEC", "TOUCHES", "TO_DAYS", "TRIM", "TRUNCATE", "UCASE", "UNCOMPRESS", "UNCOMPRESSED_LENGTH", "UNHEX", "UNIQUE_USERS", "UNIX_TIMESTAMP", "UPDATEXML", "UPPER", "USER", "UTC_DATE", "UTC_TIME", "UTC_TIMESTAMP", "UUID", "VARIANCE", "VAR_POP", "VAR_SAMP", "VERSION", "WEEK", "WEEKDAY", "WEEKOFYEAR", "WITHIN", "X", "Y", "YEAR", "YEARWEEK");
    protected static $boundaries = array(",", ";", ":", ")", "(", ".", "=", "<", ">", "+", "-", "*", "/", "!", "^", "%", "|", "&", "#");
    public static $quote_attributes = "style=\"color: blue;\"";
    public static $backtick_quote_attributes = "style=\"color: purple;\"";
    public static $reserved_attributes = "style=\"font-weight:bold;\"";
    public static $boundary_attributes = "";
    public static $number_attributes = "style=\"color: green;\"";
    public static $word_attributes = "style=\"color: #333;\"";
    public static $error_attributes = "style=\"background-color: red;\"";
    public static $comment_attributes = "style=\"color: #aaa;\"";
    public static $variable_attributes = "style=\"color: orange;\"";
    public static $pre_attributes = "style=\"color: black; background-color: white;\"";
    public static $cli = NULL;
    public static $cli_quote = "\33[34;1m";
    public static $cli_backtick_quote = "\33[35;1m";
    public static $cli_reserved = "\33[37m";
    public static $cli_boundary = "";
    public static $cli_number = "\33[32;1m";
    public static $cli_word = "";
    public static $cli_error = "\33[31;1;7m";
    public static $cli_comment = "\33[30;1m";
    public static $cli_functions = "\33[37m";
    public static $cli_variable = "\33[36;1m";
    public static $tab = "  ";
    public static $use_pre = true;
    protected static $init = NULL;
    protected static $regex_boundaries = NULL;
    protected static $regex_reserved = NULL;
    protected static $regex_reserved_newline = NULL;
    protected static $regex_reserved_toplevel = NULL;
    protected static $regex_function = NULL;
    public static $max_cachekey_size = 15;
    protected static $token_cache = array();
    protected static $cache_hits = 0;
    protected static $cache_misses = 0;
    const TOKEN_TYPE_WHITESPACE = 0;
    const TOKEN_TYPE_WORD = 1;
    const TOKEN_TYPE_QUOTE = 2;
    const TOKEN_TYPE_BACKTICK_QUOTE = 3;
    const TOKEN_TYPE_RESERVED = 4;
    const TOKEN_TYPE_RESERVED_TOPLEVEL = 5;
    const TOKEN_TYPE_RESERVED_NEWLINE = 6;
    const TOKEN_TYPE_BOUNDARY = 7;
    const TOKEN_TYPE_COMMENT = 8;
    const TOKEN_TYPE_BLOCK_COMMENT = 9;
    const TOKEN_TYPE_NUMBER = 10;
    const TOKEN_TYPE_ERROR = 11;
    const TOKEN_TYPE_VARIABLE = 12;
    const TOKEN_TYPE = 0;
    const TOKEN_VALUE = 1;
    /**
     * Get stats about the token cache
     * @return Array An array containing the keys 'hits', 'misses', 'entries', and 'size' in bytes
     */
    public static function getCacheStats()
    {
        return array("hits" => self::$cache_hits, "misses" => self::$cache_misses, "entries" => count(self::$token_cache), "size" => strlen(serialize(self::$token_cache)));
    }
    /**
     * Stuff that only needs to be done once.  Builds regular expressions and sorts the reserved words.
     */
    protected static function init()
    {
        if (self::$init) {
            return NULL;
        }
        $reservedMap = array_combine(self::$reserved, array_map("strlen", self::$reserved));
        arsort($reservedMap);
        self::$reserved = array_keys($reservedMap);
        self::$regex_boundaries = "(" . implode("|", array_map(array("SqlFormatter", "quote_regex"), self::$boundaries)) . ")";
        self::$regex_reserved = "(" . implode("|", array_map(array("SqlFormatter", "quote_regex"), self::$reserved)) . ")";
        self::$regex_reserved_toplevel = str_replace(" ", "\\s+", "(" . implode("|", array_map(array("SqlFormatter", "quote_regex"), self::$reserved_toplevel)) . ")");
        self::$regex_reserved_newline = str_replace(" ", "\\s+", "(" . implode("|", array_map(array("SqlFormatter", "quote_regex"), self::$reserved_newline)) . ")");
        self::$regex_function = "(" . implode("|", array_map(array("SqlFormatter", "quote_regex"), self::$functions)) . ")";
        self::$init = true;
    }
    /**
     * Return the next token and token type in a SQL string.
     * Quoted strings, comments, reserved words, whitespace, and punctuation are all their own tokens.
     *
     * @param String $string   The SQL string
     * @param array  $previous The result of the previous getNextToken() call
     *
     * @return Array An associative array containing the type and value of the token.
     */
    protected static function getNextToken($string, $previous = NULL)
    {
        if (preg_match("/^\\s+/", $string, $matches)) {
            return array(self::TOKEN_VALUE => $matches[0], self::TOKEN_TYPE => self::TOKEN_TYPE_WHITESPACE);
        }
        if ($string[0] === "#" || isset($string[1]) && $string[0] === "-" && $string[1] === "-" || $string[0] === "/" && $string[1] === "*") {
            if ($string[0] === "-" || $string[0] === "#") {
                $last = strpos($string, "\n");
                $type = self::TOKEN_TYPE_COMMENT;
            } else {
                $last = strpos($string, "*/", 2) + 2;
                $type = self::TOKEN_TYPE_BLOCK_COMMENT;
            }
            if ($last === false) {
                $last = strlen($string);
            }
            return array(self::TOKEN_VALUE => substr($string, 0, $last), self::TOKEN_TYPE => $type);
        }
        if ($string[0] === "\"" || $string[0] === "'" || $string[0] === "`") {
            $return = array(self::TOKEN_TYPE => $string[0] === "`" ? self::TOKEN_TYPE_BACKTICK_QUOTE : self::TOKEN_TYPE_QUOTE, self::TOKEN_VALUE => self::getQuotedString($string));
            return $return;
        }
        if ($string[0] === "@" && isset($string[1])) {
            $ret = array(self::TOKEN_VALUE => NULL, self::TOKEN_TYPE => self::TOKEN_TYPE_VARIABLE);
            if ($string[1] === "\"" || $string[1] === "'" || $string[1] === "`") {
                $ret[self::TOKEN_VALUE] = "@" . self::getQuotedString(substr($string, 1));
            } else {
                preg_match("/^(@[a-zA-Z0-9\\._\\\$]+)/", $string, $matches);
                if ($matches) {
                    $ret[self::TOKEN_VALUE] = $matches[1];
                }
            }
            if ($ret[self::TOKEN_VALUE] !== NULL) {
                return $ret;
            }
        }
        if (preg_match("/^([0-9]+(\\.[0-9]+)?|0x[0-9a-fA-F]+|0b[01]+)(\$|\\s|\"'`|" . self::$regex_boundaries . ")/", $string, $matches)) {
            return array(self::TOKEN_VALUE => $matches[1], self::TOKEN_TYPE => self::TOKEN_TYPE_NUMBER);
        }
        if (preg_match("/^(" . self::$regex_boundaries . ")/", $string, $matches)) {
            return array(self::TOKEN_VALUE => $matches[1], self::TOKEN_TYPE => self::TOKEN_TYPE_BOUNDARY);
        }
        if (!$previous || !isset($previous[self::TOKEN_VALUE]) || $previous[self::TOKEN_VALUE] !== ".") {
            $upper = strtoupper($string);
            if (preg_match("/^(" . self::$regex_reserved_toplevel . ")(\$|\\s|" . self::$regex_boundaries . ")/", $upper, $matches)) {
                return array(self::TOKEN_TYPE => self::TOKEN_TYPE_RESERVED_TOPLEVEL, self::TOKEN_VALUE => substr($string, 0, strlen($matches[1])));
            }
            if (preg_match("/^(" . self::$regex_reserved_newline . ")(\$|\\s|" . self::$regex_boundaries . ")/", $upper, $matches)) {
                return array(self::TOKEN_TYPE => self::TOKEN_TYPE_RESERVED_NEWLINE, self::TOKEN_VALUE => substr($string, 0, strlen($matches[1])));
            }
            if (preg_match("/^(" . self::$regex_reserved . ")(\$|\\s|" . self::$regex_boundaries . ")/", $upper, $matches)) {
                return array(self::TOKEN_TYPE => self::TOKEN_TYPE_RESERVED, self::TOKEN_VALUE => substr($string, 0, strlen($matches[1])));
            }
        }
        $upper = strtoupper($string);
        if (preg_match("/^(" . self::$regex_function . "[(]|\\s|[)])/", $upper, $matches)) {
            return array(self::TOKEN_TYPE => self::TOKEN_TYPE_RESERVED, self::TOKEN_VALUE => substr($string, 0, strlen($matches[1]) - 1));
        }
        preg_match("/^(.*?)(\$|\\s|[\"'`]|" . self::$regex_boundaries . ")/", $string, $matches);
        return array(self::TOKEN_VALUE => $matches[1], self::TOKEN_TYPE => self::TOKEN_TYPE_WORD);
    }
    protected static function getQuotedString($string)
    {
        $ret = NULL;
        if (preg_match("/^(((`[^`]*(\$|`))+)|((\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*(\"|\$))+)|(('[^'\\\\]*(?:\\\\.[^'\\\\]*)*('|\$))+))/s", $string, $matches)) {
            $ret = $matches[1];
        }
        return $ret;
    }
    /**
     * Takes a SQL string and breaks it into tokens.
     * Each token is an associative array with type and value.
     *
     * @param String $string The SQL string
     *
     * @return Array An array of tokens.
     */
    protected static function tokenize($string)
    {
        self::init();
        $tokens = array();
        $original_length = strlen($string);
        $old_string_len = strlen($string) + 1;
        $token = NULL;
        $current_length = strlen($string);
        while ($current_length) {
            if ($old_string_len <= $current_length) {
                $tokens[] = array(self::TOKEN_VALUE => $string, self::TOKEN_TYPE => self::TOKEN_TYPE_ERROR);
                return $tokens;
            }
            $old_string_len = $current_length;
            if (self::$max_cachekey_size <= $current_length) {
                $cacheKey = substr($string, 0, self::$max_cachekey_size);
            } else {
                $cacheKey = false;
            }
            if ($cacheKey && isset(self::$token_cache[$cacheKey])) {
                $token = self::$token_cache[$cacheKey];
                $token_length = strlen($token[self::TOKEN_VALUE]);
                self::$cache_hits++;
            } else {
                $token = self::getNextToken($string, $token);
                $token_length = strlen($token[self::TOKEN_VALUE]);
                self::$cache_misses++;
                if ($cacheKey && $token_length < self::$max_cachekey_size) {
                    self::$token_cache[$cacheKey] = $token;
                }
            }
            $tokens[] = $token;
            $string = substr($string, $token_length);
            $current_length -= $token_length;
        }
        return $tokens;
    }
    /**
     * Format the whitespace in a SQL string to make it easier to read.
     *
     * @param String  $string    The SQL string
     * @param boolean $highlight If true, syntax highlighting will also be performed
     *
     * @return String The SQL string with HTML styles and formatting wrapped in a <pre> tag
     */
    public static function format($string, $highlight = true)
    {
        $return = "";
        $tab = "\t";
        $indent_level = 0;
        $newline = false;
        $inline_parentheses = false;
        $increase_special_indent = false;
        $increase_block_indent = false;
        $indent_types = array();
        $added_newline = false;
        $inline_count = 0;
        $inline_indented = false;
        $clause_limit = false;
        $original_tokens = self::tokenize($string);
        $tokens = array();
        foreach ($original_tokens as $i => $token) {
            if ($token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                $token["i"] = $i;
                $tokens[] = $token;
            }
        }
        foreach ($tokens as $i => $token) {
            if ($highlight) {
                $highlighted = self::highlightToken($token);
            } else {
                $highlighted = $token[self::TOKEN_VALUE];
            }
            if ($increase_special_indent) {
                $indent_level++;
                $increase_special_indent = false;
                array_unshift($indent_types, "special");
            }
            if ($increase_block_indent) {
                $indent_level++;
                $increase_block_indent = false;
                array_unshift($indent_types, "block");
            }
            if ($newline) {
                $return .= "\n" . str_repeat($tab, $indent_level);
                $newline = false;
                $added_newline = true;
            } else {
                $added_newline = false;
            }
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                    $indent = str_repeat($tab, $indent_level);
                    $return .= "\n" . $indent;
                    $highlighted = str_replace("\n", "\n" . $indent, $highlighted);
                }
                $return .= $highlighted;
                $newline = true;
                continue;
            }
            if ($inline_parentheses) {
                if ($token[self::TOKEN_VALUE] === ")") {
                    $return = rtrim($return, " ");
                    if ($inline_indented) {
                        array_shift($indent_types);
                        $indent_level--;
                        $return .= "\n" . str_repeat($tab, $indent_level);
                    }
                    $inline_parentheses = false;
                    $return .= $highlighted . " ";
                    continue;
                }
                if ($token[self::TOKEN_VALUE] === "," && 30 <= $inline_count) {
                    $inline_count = 0;
                    $newline = true;
                }
                $inline_count += strlen($token[self::TOKEN_VALUE]);
            }
            if ($token[self::TOKEN_VALUE] === "(") {
                $length = 0;
                for ($j = 1; $j <= 250; $j++) {
                    if (!isset($tokens[$i + $j])) {
                        break;
                    }
                    $next = $tokens[$i + $j];
                    if ($next[self::TOKEN_VALUE] === ")") {
                        $inline_parentheses = true;
                        $inline_count = 0;
                        $inline_indented = false;
                        break;
                    }
                    if ($next[self::TOKEN_VALUE] === ";" || $next[self::TOKEN_VALUE] === "(") {
                        break;
                    }
                    if ($next[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_TOPLEVEL || $next[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_NEWLINE || $next[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $next[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                        break;
                    }
                    $length += strlen($next[self::TOKEN_VALUE]);
                }
                if ($inline_parentheses && 30 < $length) {
                    $increase_block_indent = true;
                    $inline_indented = true;
                    $newline = true;
                }
                if (isset($original_tokens[$token["i"] - 1]) && $original_tokens[$token["i"] - 1][self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                    $return = rtrim($return, " ");
                }
                if (!$inline_parentheses) {
                    $increase_block_indent = true;
                    $newline = true;
                }
            } else {
                if ($token[self::TOKEN_VALUE] === ")") {
                    $return = rtrim($return, " ");
                    $indent_level--;
                    while ($j = array_shift($indent_types)) {
                        if ($j === "special") {
                            $indent_level--;
                        } else {
                            break;
                        }
                    }
                    if ($indent_level < 0) {
                        $indent_level = 0;
                        if ($highlight) {
                            $return .= "\n" . self::highlightError($token[self::TOKEN_VALUE]);
                            continue;
                        }
                    }
                    if (!$added_newline) {
                        $return .= "\n" . str_repeat($tab, $indent_level);
                    }
                } else {
                    if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
                        $increase_special_indent = true;
                        reset($indent_types);
                        if (current($indent_types) === "special") {
                            $indent_level--;
                            array_shift($indent_types);
                        }
                        $newline = true;
                        if (!$added_newline) {
                            $return .= "\n" . str_repeat($tab, $indent_level);
                        } else {
                            $return = rtrim($return, $tab) . str_repeat($tab, $indent_level);
                        }
                        if (strpos($token[self::TOKEN_VALUE], " ") !== false || strpos($token[self::TOKEN_VALUE], "\n") !== false || strpos($token[self::TOKEN_VALUE], "\t") !== false) {
                            $highlighted = preg_replace("/\\s+/", " ", $highlighted);
                        }
                        if ($token[self::TOKEN_VALUE] === "LIMIT" && !$inline_parentheses) {
                            $clause_limit = true;
                        }
                    } else {
                        if ($clause_limit && $token[self::TOKEN_VALUE] !== "," && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_NUMBER && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                            $clause_limit = false;
                        } else {
                            if ($token[self::TOKEN_VALUE] === "," && !$inline_parentheses) {
                                if ($clause_limit === true) {
                                    $newline = false;
                                    $clause_limit = false;
                                } else {
                                    $newline = true;
                                }
                            } else {
                                if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_NEWLINE) {
                                    if (!$added_newline) {
                                        $return .= "\n" . str_repeat($tab, $indent_level);
                                    }
                                    if (strpos($token[self::TOKEN_VALUE], " ") !== false || strpos($token[self::TOKEN_VALUE], "\n") !== false || strpos($token[self::TOKEN_VALUE], "\t") !== false) {
                                        $highlighted = preg_replace("/\\s+/", " ", $highlighted);
                                    }
                                } else {
                                    if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BOUNDARY && $tokens[$i - 1][self::TOKEN_TYPE] === self::TOKEN_TYPE_BOUNDARY && isset($original_tokens[$token["i"] - 1]) && $original_tokens[$token["i"] - 1][self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE) {
                                        $return = rtrim($return, " ");
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($token[self::TOKEN_VALUE] === "." || $token[self::TOKEN_VALUE] === "," || $token[self::TOKEN_VALUE] === ";") {
                $return = rtrim($return, " ");
            }
            $return .= $highlighted . " ";
            if ($token[self::TOKEN_VALUE] === "(" || $token[self::TOKEN_VALUE] === ".") {
                $return = rtrim($return, " ");
            }
            if ($token[self::TOKEN_VALUE] === "-" && isset($tokens[$i + 1]) && $tokens[$i + 1][self::TOKEN_TYPE] === self::TOKEN_TYPE_NUMBER && isset($tokens[$i - 1])) {
                $prev = $tokens[$i - 1][self::TOKEN_TYPE];
                if ($prev !== self::TOKEN_TYPE_QUOTE && $prev !== self::TOKEN_TYPE_BACKTICK_QUOTE && $prev !== self::TOKEN_TYPE_WORD && $prev !== self::TOKEN_TYPE_NUMBER) {
                    $return = rtrim($return, " ");
                }
            }
        }
        if ($highlight && array_search("block", $indent_types) !== false) {
            $return .= "\n" . self::highlightError("WARNING: unclosed parentheses or section");
        }
        $return = trim(str_replace("\t", self::$tab, $return));
        if ($highlight) {
            $return = self::output($return);
        }
        return $return;
    }
    /**
     * Add syntax highlighting to a SQL string
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string with HTML styles applied
     */
    public static function highlight($string)
    {
        $tokens = self::tokenize($string);
        $return = "";
        foreach ($tokens as $token) {
            $return .= self::highlightToken($token);
        }
        return self::output($return);
    }
    /**
     * Split a SQL string into multiple queries.
     * Uses ";" as a query delimiter.
     *
     * @param String $string The SQL string
     *
     * @return Array An array of individual query strings without trailing semicolons
     */
    public static function splitQuery($string)
    {
        $queries = array();
        $current_query = "";
        $empty = true;
        $tokens = self::tokenize($string);
        foreach ($tokens as $token) {
            if ($token[self::TOKEN_VALUE] === ";") {
                if (!$empty) {
                    $queries[] = $current_query . ";";
                }
                $current_query = "";
                $empty = true;
                continue;
            }
            if ($token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_WHITESPACE && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_COMMENT && $token[self::TOKEN_TYPE] !== self::TOKEN_TYPE_BLOCK_COMMENT) {
                $empty = false;
            }
            $current_query .= $token[self::TOKEN_VALUE];
        }
        if (!$empty) {
            $queries[] = trim($current_query);
        }
        return $queries;
    }
    /**
     * Remove all comments from a SQL string
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string without comments
     */
    public static function removeComments($string)
    {
        $result = "";
        $tokens = self::tokenize($string);
        foreach ($tokens as $token) {
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                continue;
            }
            $result .= $token[self::TOKEN_VALUE];
        }
        $result = self::format($result, false);
        return $result;
    }
    /**
     * Compress a query by collapsing white space and removing comments
     *
     * @param String $string The SQL string
     *
     * @return String The SQL string without comments
     */
    public static function compress($string)
    {
        $result = "";
        $tokens = self::tokenize($string);
        $whitespace = true;
        foreach ($tokens as $token) {
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_COMMENT || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_BLOCK_COMMENT) {
                continue;
            }
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_NEWLINE || $token[self::TOKEN_TYPE] === self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
                $token[self::TOKEN_VALUE] = preg_replace("/\\s+/", " ", $token[self::TOKEN_VALUE]);
            }
            if ($token[self::TOKEN_TYPE] === self::TOKEN_TYPE_WHITESPACE) {
                if ($whitespace) {
                    continue;
                }
                $whitespace = true;
                $token[self::TOKEN_VALUE] = " ";
            } else {
                $whitespace = false;
            }
            $result .= $token[self::TOKEN_VALUE];
        }
        return rtrim($result);
    }
    /**
     * Highlights a token depending on its type.
     *
     * @param Array $token An associative array containing type and value.
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightToken($token)
    {
        $type = $token[self::TOKEN_TYPE];
        if (self::is_cli()) {
            $token = $token[self::TOKEN_VALUE];
        } else {
            $token = htmlentities($token[self::TOKEN_VALUE], ENT_COMPAT, "UTF-8");
        }
        if ($type === self::TOKEN_TYPE_BOUNDARY) {
            return self::highlightBoundary($token);
        }
        if ($type === self::TOKEN_TYPE_WORD) {
            return self::highlightWord($token);
        }
        if ($type === self::TOKEN_TYPE_BACKTICK_QUOTE) {
            return self::highlightBacktickQuote($token);
        }
        if ($type === self::TOKEN_TYPE_QUOTE) {
            return self::highlightQuote($token);
        }
        if ($type === self::TOKEN_TYPE_RESERVED) {
            return self::highlightReservedWord($token);
        }
        if ($type === self::TOKEN_TYPE_RESERVED_TOPLEVEL) {
            return self::highlightReservedWord($token);
        }
        if ($type === self::TOKEN_TYPE_RESERVED_NEWLINE) {
            return self::highlightReservedWord($token);
        }
        if ($type === self::TOKEN_TYPE_NUMBER) {
            return self::highlightNumber($token);
        }
        if ($type === self::TOKEN_TYPE_VARIABLE) {
            return self::highlightVariable($token);
        }
        if ($type === self::TOKEN_TYPE_COMMENT || $type === self::TOKEN_TYPE_BLOCK_COMMENT) {
            return self::highlightComment($token);
        }
        return $token;
    }
    /**
     * Highlights a quoted string
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightQuote($value)
    {
        if (self::is_cli()) {
            return self::$cli_quote . $value . "\33[0m";
        }
        return "<span " . self::$quote_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a backtick quoted string
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightBacktickQuote($value)
    {
        if (self::is_cli()) {
            return self::$cli_backtick_quote . $value . "\33[0m";
        }
        return "<span " . self::$backtick_quote_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a reserved word
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightReservedWord($value)
    {
        if (self::is_cli()) {
            return self::$cli_reserved . $value . "\33[0m";
        }
        return "<span " . self::$reserved_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a boundary token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightBoundary($value)
    {
        if ($value === "(" || $value === ")") {
            return $value;
        }
        if (self::is_cli()) {
            return self::$cli_boundary . $value . "\33[0m";
        }
        return "<span " . self::$boundary_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a number
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightNumber($value)
    {
        if (self::is_cli()) {
            return self::$cli_number . $value . "\33[0m";
        }
        return "<span " . self::$number_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights an error
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightError($value)
    {
        if (self::is_cli()) {
            return self::$cli_error . $value . "\33[0m";
        }
        return "<span " . self::$error_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a comment
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightComment($value)
    {
        if (self::is_cli()) {
            return self::$cli_comment . $value . "\33[0m";
        }
        return "<span " . self::$comment_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a word token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightWord($value)
    {
        if (self::is_cli()) {
            return self::$cli_word . $value . "\33[0m";
        }
        return "<span " . self::$word_attributes . ">" . $value . "</span>";
    }
    /**
     * Highlights a variable token
     *
     * @param String $value The token's value
     *
     * @return String HTML code of the highlighted token.
     */
    protected static function highlightVariable($value)
    {
        if (self::is_cli()) {
            return self::$cli_variable . $value . "\33[0m";
        }
        return "<span " . self::$variable_attributes . ">" . $value . "</span>";
    }
    /**
     * Helper function for building regular expressions for reserved words and boundary characters
     *
     * @param String $a The string to be quoted
     *
     * @return String The quoted string
     */
    private static function quote_regex($a)
    {
        return preg_quote($a, "/");
    }
    /**
     * Helper function for building string output
     *
     * @param String $string The string to be quoted
     *
     * @return String The quoted string
     */
    private static function output($string)
    {
        if (self::is_cli()) {
            return $string . "\n";
        }
        $string = trim($string);
        if (!self::$use_pre) {
            return $string;
        }
        return "<pre " . self::$pre_attributes . ">" . $string . "</pre>";
    }
    private static function is_cli()
    {
        if (isset($cli)) {
            return self::$cli;
        }
        return php_sapi_name() === "cli";
    }
}

?>