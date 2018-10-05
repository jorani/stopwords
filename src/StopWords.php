<?php 
namespace Jorani\StopWords;

/**
* Utility allowing to remove stopwords from a string
*  
*
* @author yourname
*/
class StopWords {

    /**
     * Regex expression containing the list of stop words to be removed
     *
     * @var string 
     */
    private $wordsList;

    /**
     * Two letters ISO 639-1 language code
     *
     * @var string 
     */
    private $langCode;

    function __construct($langCode) {
        $this->langCode = $langCode;
        $words = $this->getWordsList();
        $this->wordsList = "/(?<=^|[\p{P}\p{Z}])" .     /*Look behind: beginning of the string or any space/punct. */
                    "(" . implode('|', $words) . ")" .  /* Any word of the list */
                    "(?=$|[\p{P}\p{Z}])/mui";           /*Look after: end of the string or any space/punct. */
        iconv(mb_detect_encoding($this->wordsList, mb_detect_order(), true), "UTF-8", $this->wordsList);
    }

    /**
     * Return TRUE if the language is supported by the library, FALSE otherwise
     *
     * @param string $langCode Language code that can contain the regional variant
     * @return boolean is the language code supported
     */
    public static function isLanguageSupported($langCode) {
        $stopWordsFilePath = __DIR__ . '/stopwords/' . self::getActualLocaleName($langCode) . '.txt';
        if (file_exists($stopWordsFilePath)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Returnthe list of supported languages
     *
     * @return array list of ISO codes forthe supported languages
     */
    public static function getSupportedLanguagesList() {
        $languages = array();
        foreach (glob(__DIR__ . '/stopwords/*.txt') as $file) {
            $pathParts = pathinfo($file);
            array_push($languages, $pathParts['filename']);
        }
        return $languages;
    }

    /**
     * Returns the two letters ISO code for the langCode passed as paramter
     * For example fr_FR would result in fr
     *
     * @param string $langCode
     * @return void
     */
    public static function getActualLocaleName($langCode) {
        $actualLocaleName = strtolower(substr($langCode, 0, 2));
        return $actualLocaleName;
    }

    /**
     * Return the list of stop words as an array.
     * Note that the language is initialized by the constructor.
     * If the language is not supported, returns an empty array
     * @return array list of the stop words
     */
    public function getWordsList() {
        $stopWordsFilePath = __DIR__ . '/stopwords/' . self::getActualLocaleName($this->langCode) . '.txt';
        if (file_exists($stopWordsFilePath)) {
            return file($stopWordsFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        return array();
    }

    /**
     * Removes the stop words from a string
     *
     * @param string $string String to be cleaned
     * @param boolean $stripPunct Remove punctuation
     * @return string Cleaned string
     */
    public function remove($string, $stripPunct = false) {
        iconv(mb_detect_encoding($string, mb_detect_order(), true), "UTF-8", $string);  //Convert input string to UTF-8
        $string = mb_strtolower($string);
        //mb_regex_encoding();                           //Convert string to lower
        $string = preg_replace($this->wordsList, ' ', $string);  //Remove stop words
        switch (preg_last_error()) {
            //TODO: Emit an exception
            //case PREG_NO_ERROR: echo PHP_EOL . "preg:PREG_NO_ERROR" . PHP_EOL; break;
            case PREG_BACKTRACK_LIMIT_ERROR: echo PHP_EOL . "preg:PREG_BACKTRACK_LIMIT_ERROR" . PHP_EOL; break;
            case PREG_RECURSION_LIMIT_ERROR: echo PHP_EOL . "preg:PREG_RECURSION_LIMIT_ERROR" . PHP_EOL; break;
            case PREG_BAD_UTF8_ERROR: echo PHP_EOL . "preg:PREG_BAD_UTF8_ERROR" . PHP_EOL; break;
            case PREG_BAD_UTF8_OFFSET_ERROR: echo PHP_EOL . "preg:PREG_BAD_UTF8_OFFSET_ERROR" . PHP_EOL; break;
            case PREG_JIT_STACKLIMIT_ERROR: echo PHP_EOL . "preg:PREG_JIT_STACKLIMIT_ERROR" . PHP_EOL; break;
        }
        if ($stripPunct) {
            $string = preg_replace("/\p{P}*/mui", '', $string);       //Remove punctuation signs
        }
        $string = preg_replace("/\p{Z}+/mui", ' ', $string);       //Remove duplicated spaces
        return trim($string);
   }
}