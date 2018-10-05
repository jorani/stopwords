<?php 
use PHPUnit\Framework\TestCase;

/**
 * Test Jorani\StopWords\StopWords class
 * @author Benjamin BALET <benjamin.balet@gmail.com>
 */
class StopWordsTest extends TestCase
{
	
  /**
   * Check the function that returns an array containing the list of words
   */
  public function testGetWordsList()
  {
    $unsupportedLangCode = new Jorani\StopWords\StopWords('zzzz');
    $list = $unsupportedLangCode->getWordsList();
    $this->assertEquals(count($list), 0);
   
    $englishLangCode = new Jorani\StopWords\StopWords('en');
    $list = $englishLangCode->getWordsList();
    $this->assertEquals(count($list), 546);
    $this->assertEquals(in_array('willing', $list), true);
    $this->assertEquals(in_array('bonjour', $list), false);
  }
  
  /**
   * Check the parsing of the language code
   */
  public function testGetActualLocaleName() {
    $this->assertEquals('km', Jorani\StopWords\StopWords::getActualLocaleName('km'));
    $this->assertEquals('tk', Jorani\StopWords\StopWords::getActualLocaleName('TK'));
    $this->assertEquals('fr', Jorani\StopWords\StopWords::getActualLocaleName('fr_FR'));
    $this->assertEquals('en', Jorani\StopWords\StopWords::getActualLocaleName('en_US_California'));
  }

  public function testGetSpportedLanguagesList() {
    $languages = Jorani\StopWords\StopWords::getSupportedLanguagesList();
    $this->assertEquals(in_array('pl', $languages), true);
    $this->assertEquals(in_array('ww', $languages), false);
  }

  /**
   * Check the language support feature
   */
  public function testIsLanguageSupported() {
    $this->assertEquals(false, Jorani\StopWords\StopWords::isLanguageSupported('yy'));
    $this->assertEquals(true, Jorani\StopWords\StopWords::isLanguageSupported('ar'));
    $this->assertEquals(true, Jorani\StopWords\StopWords::isLanguageSupported('bg'));
  }

  /**
   * Check the function that removes a list of words from a string
   */
  public function testRemoveWords() {
    //All human beings are born free and equal in dignity and rights. They are endowed with reason and conscience and should act towards one another in a spirit of brotherhood.
    $swEngine = new Jorani\StopWords\StopWords('en');
    $source = "I was walking along another watch tower, yet I saw nothing.";
    $expected = "walking watch tower, .";
    $result = $swEngine->remove($source);
    $this->assertEquals($expected, $result);

    $swEngine = new Jorani\StopWords\StopWords('fr');
    $source = "C'était la fin d'un bel après-midi d'été et j'étais assis à même la pelouse du parc d'en face.";
    $expected = "c' fin d' bel -midi d' j' assis pelouse parc d' face.";
    $result = $swEngine->remove($source);
    $this->assertEquals($expected, $result);
  }

  /**
   * Check the function that removes a list of words from a string
   * And strip punctation marks from the string
   */
  public function testRemoveWordsStripPuncts() {
    $swEngine = new Jorani\StopWords\StopWords('es');
    $source = "Todos los seres humanos nacen libres e iguales en dignidad y derechos y, dotados como están de razón y conciencia, deben comportarse fraternalmente los unos con los otros.";
    $expected = "seres humanos nacen libres e iguales dignidad derechos dotados razón conciencia deben comportarse fraternalmente";
    $result = $swEngine->remove($source, true);
    $this->assertEquals($expected, $result);
  }

  /**
   * Check stopwords removal with Unicode support
   */
  public function testRemoveWordsUnicodeSupport() {
    $swEngine = new Jorani\StopWords\StopWords('km');
    $source = "ខេមរភាសា: ផ្ទះ ស្កឹមស្កៃបីបួន ខ្នង នេះ បន្ថែម នៅពេលនោះ។";
    $expected = "ខេមរភាសា: ផ្ទះ ស្កឹមស្កៃបីបួន ខ្នង ។";
    $result = $swEngine->remove($source);
    $this->assertEquals($expected, $result);
  }

  /**
   * Check the function that removes a list of words from a string
   */
  public function testRemoveWordsAllLanguages() {
    $sources = Array (
      'ar' => "كنت أريد أن أقرأ كتابا عن تاريخ المرأة في فرنسا‬",
      'bg' => "Иван го боли гърлото, а мене ме боли главата.",
      /*'bn' => "নয়টা গরু	কয়টা বালিশ	অনেকজন লোক	চার-পাঁচজন শিক্ষক",*/
      'cs' => "o velkém psovi. velký pes. vidím malou kočku. o jestliže tvrdém dřevě",
      'de' => "Alle Menschen sind frei und gleich an Würde und Rechten geboren. Sie sind mit Vernunft und Gewissen begabt und sollen einander im Geist der Brüderlichkeit begegnen.",
      'en' => "All human beings are born free and equal in dignity and rights. They are endowed with reason and conscience and should act towards one another in a spirit of brotherhood.",
      'es' => "Todos los seres humanos nacen libres e iguales en dignidad y derechos y, dotados como están de razón y conciencia, deben comportarse fraternalmente los unos con los otros.",

      'it' => "Salute! Per favore... Che cosa? Non capisco. Parli inglese?",

      'km' => "ខេមរភាសា: ផ្ទះ ស្កឹមស្កៃបីបួន ខ្នង នេះ បន្ថែម នៅពេលនោះ។",
    );
    $expectedResults = Array (
      'ar' => "كنت أريد أن أقرأ كتابا تاريخ المرأة فرنسا‬",
      'bg' => "иван боли гърлото мене боли главата",
      'bn' => "নয়টা গরু কয়টা বালিশ অনেকজন লোক চারপাঁচজন শিক্ষক",
      'cs' => "o velkém psovi velký pes vidím malou kočku o tvrdém dřevě",
      'de' => "frei geboren vernunft gewissen begabt geist brüderlichkeit begegnen",
      'en' => "human beings born free equal dignity rights endowed reason conscience act spirit brotherhood",
      'es' => "seres humanos nacen libres e iguales dignidad derechos dotados razón conciencia deben comportarse fraternalmente",

      'it' => "salute capisco parli inglese",

      'km' => "ខេមរភាសា ផ្ទះ ស្កឹមស្កៃបីបួន ខ្នង",
    );

    foreach ($sources as $langCode => $source) {
      $swEngine = new Jorani\StopWords\StopWords($langCode);
      $result = $swEngine->remove($source, true);
      $this->assertEquals($expectedResults[$langCode], $result);
    }

  }
}
