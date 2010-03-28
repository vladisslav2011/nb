<?php
// Call SpreadsheetReaderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "SpreadsheetReaderTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'SpreadsheetReader.php';

/**
 * Test class for SpreadsheetReader.
 * Generated by PHPUnit_Util_Skeleton on 2007-03-22 at 08:43:32.
 */
class SpreadsheetReaderTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("SpreadsheetReaderTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    public function testRead() {
        // Remove the following line when you implement this test.
        $reader = new SpreadsheetReader;
        $sheets = $reader->read('test.workbook.xml');
        $this->assertTrue($sheets !== false);
        $this->assertEquals(1, count($sheets));
        $this->assertEquals(129, count($sheets[0]));
        $this->assertEquals(2, $sheets[0][8][0]);
        $this->assertEquals(126779, $sheets[0][8][1]);
        $this->assertEquals(121188, $sheets[0][8][10]);
    }

    public function testReadAsHash() {
        // Remove the following line when you implement this test.
        $reader = new SpreadsheetReader;
        $sheets = $reader->read('test.workbook.xml', SpreadsheetReader::READ_HASH);
        $this->assertTrue($sheets !== false);
        $this->assertEquals(1, count($sheets));
        $this->assertEquals(128, count($sheets[0]));
        $this->assertEquals('324212137', $sheets[0][0]['EAN13']);
    }

    public function testReadExcel2kXML() {
        $reader = new SpreadsheetReader;
        $sheets = $reader->read('test.excel2k.xml');
        $this->assertTrue($sheets !== false);
        $this->assertEquals(3, count($sheets));
        $this->assertEquals('c', $sheets[0][2][5]);
    }

    public function testReadExcel2kXmlAsHash() {
        $reader = new SpreadsheetReader;
        $sheets = $reader->read('test.excel2k_2.xml', SpreadsheetReader::READ_HASH);
        $this->assertTrue($sheets !== false);
        $this->assertEquals(2, count($sheets));
        $this->assertEquals(45665, $sheets[0][0]['EAN13']);
        $this->assertEquals('BVCF', $sheets[1][1]['NAME']);
    }

    public function testSheetsAsXml() {
        $reader = new SpreadsheetReader;
        $sheets = array(
            array(
                array('Field1', 'Field2'),
                array(1,2)
            ),
            array(
                array('Field1', 'Field2'),
                array('abc','xyz')
            )
        );
        $xmlString = $reader->asXml($sheets);
        //file_put_contents('test.asXml_sample.xml', $xmlString);
        $sampleXmlString = file_get_contents('test.asXml_sample.xml');
        $this->assertTrue($sampleXmlString == $xmlString);
    }

    public function testSheetsWidthFieldHeaderAsXml() {
        $reader = new SpreadsheetReader;
        $sheets = $reader->read('test.excel2k_2.xml', SpreadsheetReader::READ_HASH);
        $this->assertTrue($sheets !== false);
        $this->assertEquals(2, count($sheets));
        $this->assertEquals(45665, $sheets[0][0]['EAN13']);
        $this->assertEquals('BVCF', $sheets[1][1]['NAME']);
        $xmlString = $reader->asXml($sheets);
        //file_put_contents('test.asXml_sample2.xml', $xmlString);
        $sampleXmlString = file_get_contents('test.asXml_sample2.xml');
        $this->assertTrue($sampleXmlString == $xmlString);
        //$this->assertEquals($sampleXmlString, $xmlString);
    }
}

// Call SpreadsheetReaderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "SpreadsheetReaderTest::main") {
    SpreadsheetReaderTest::main();
}
?>
