<?php

class PeoplefinderRecordTest extends \PHPUnit\Framework\TestCase {
    protected $record;

    protected function setUp() {
        $this->record = $this->getMockBuilder('UNL_Peoplefinder_Record')
                                ->setMethods(array('getBuildings'))
                                ->getMock();
        $this->record->method('getBuildings')
                        ->willReturn(array(
            '420' => '420 University Terrace',
            'ADMS' => 'Canfield Administration Building South',
            'AVH' => 'Avery Hall',
            '17PG' => '17th and R Parking Garage'
        ));
    }

    public function testFormatterDoesNotThrowError() {
        $this->record->postalAddress = '420 112, UNL, 68588-0684';
        $this->record->formatPostalAddress();
        $this->assertEquals(1,1);
    }

    public function testFormatStandardPostalAddress() {
        $this->record->postalAddress = 'ADMS 313, UNL, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => 'ADMS 313',
            'locality' => 'Lincoln',
            'region' => 'NE',
            'postal-code' => '68588-0429',
            'unlBuildingCode' => 'ADMS',
            'roomNumber' => '313'
        ), $formatted);
    }

    public function testFormatLegacyPostalAddress() {
        // backwards...room number then building code
        $this->record->postalAddress = '313 ADMS, UNL, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => '313 ADMS',
            'locality' => 'Lincoln',
            'region' => 'NE',
            'postal-code' => '68588-0429',
            'unlBuildingCode' => 'ADMS',
            'roomNumber' => '313'
        ), $formatted);
    }

    public function testFormatWeirdLegacyPostalAddress() {
        // both are building codes. Figure out which one is best to use
        $this->record->postalAddress = '420 AVH, UNL, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => '420 AVH',
            'locality' => 'Lincoln',
            'region' => 'NE',
            'postal-code' => '68588-0429',
            'unlBuildingCode' => 'AVH',
            'roomNumber' => '420'
        ), $formatted);
    }

    public function testDoubleNumberLegacyPostalAddress() {
        // both are building codes, but it is ambiguous which is best.
        // default to the first one, as this is the new standard
        $this->record->postalAddress = '420 501, UNL, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => '420 501',
            'locality' => 'Lincoln',
            'region' => 'NE',
            'postal-code' => '68588-0429',
            'unlBuildingCode' => '420',
            'roomNumber' => '501'
        ), $formatted);
    }

    public function testFormatExtensionAddress() {
        $this->record->postalAddress = 'Extension 148 W 4th St, Ainsworth, 69210-1696';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => '148 W 4th St',
            'locality' => 'Ainsworth',
            'region' => 'NE',
            'postal-code' => '69210-1696'
        ), $formatted);
        $this->assertFalse(array_key_exists('unlBuildingCode', $formatted));
        $this->assertFalse(array_key_exists('roomNumber', $formatted));
    }

    public function testFormatMobileAddress() {
        $this->record->postalAddress = '17PG mobile, UNL, 68588-0634';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals(array(
            'street-address' => '17PG mobile',
            'locality' => 'Lincoln',
            'region' => 'NE',
            'postal-code' => '68588-0634',
            'unlBuildingCode' => '17PG'
        ), $formatted);
        $this->assertFalse(array_key_exists('roomNumber', $formatted));
    }

    public function testCityCampusLocality() {
        $this->record->postalAddress = 'ADMS 313, City Campus, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals('Lincoln', $formatted['locality']);
    }

    public function testUNOLocality() {
        $this->record->postalAddress = 'ADMS 313, UNO, 68588-0429';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals('Omaha', $formatted['locality']);
    }

    public function testFiveDigitZipCode() {
        $this->record->postalAddress = 'ADMS 313, UNL, 68588';

        $formatted = $this->record->formatPostalAddress();
        $this->assertEquals('68588', $formatted['postal-code']);
    }

}


