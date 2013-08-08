<?php
require_once( dirname( dirname( __FILE__ ) ) . '/Phockito.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/Phockito_Globals.php' );

class PhockitoNonSerializableMockTest extends PHPUnit_Framework_TestCase {
    const CLASS_NAME = __CLASS__;

    /** @var phockitoNonSerializableMockTestHarness */
    public $phockitoNonSerializableMockTestHarness;

    /** @var  PhockitoMockParameterCall */
    private $phockitoMockParameterCall;


    protected function setUp() {
        $this->phockitoMockParameterCall              = mock( PhockitoMockParameterCall::CLASS_NAME );
        $this->phockitoNonSerializableMockTestHarness = new PhockitoNonSerializableMockTestHarness( $this->phockitoMockParameterCall );
    }


    /**
     * Does not work as DDMDocument cannot serialize properly, no exception though just a flakey test that cannot
     * tell which of the DOMDocuments is being passed
     */
    public function test_domDocumentTest_native() {
        $domDocument1 = new DOMDocument();
        $domDocument1->loadXML( '<xml1/>' );

        $domDocument2 = new DOMDocument();
        $domDocument2->loadXML( '<xml2/>' );

        $this->phockitoNonSerializableMockTestHarness->passSecondObjectOnly( $domDocument1, $domDocument2 );
        verify( $this->phockitoMockParameterCall, 0 )->execute( $domDocument1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $domDocument2 );
    }


    /**
     * Works as compatibility can be inferred from generated instanceids on the mock
     */
    public function test_domDocumentTest_mocked() {
        /** @var $domDocument1 DOMDocument */
        $domDocument1 = mock( 'DOMDocument' );

        /** @var $domDocument2 DOMDocument */
        $domDocument2 = mock( 'DOMDocument' );

        when( $this->phockitoMockParameterCall->execute( $domDocument2 ) )->thenReturn( 'result' );

        $result = $this->phockitoNonSerializableMockTestHarness->passSecondObjectOnly( $domDocument1, $domDocument2 );
        $this->assertEquals( $result, 'result' );
        verify( $this->phockitoMockParameterCall, 0 )->execute( $domDocument1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $domDocument2 );
    }


    /**
     * Exceptions as it cannot serialize the native instance in Phockito::_arguments_match
     */
    public function test_splFileInfoTest_native() {
        $splFileInfo1 = new SplFileInfo( __DIR__ );
        $splFileInfo2 = new SplFileInfo( __FILE__ );

        $this->phockitoNonSerializableMockTestHarness->passSecondObjectOnly( $splFileInfo1, $splFileInfo2 );
        verify( $this->phockitoMockParameterCall, 0 )->execute( $splFileInfo1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo2 );
    }


    /**
     *  Exceptions as it cannot serialize the mock instance in Phockito::_arguments_match
     */
    public function test_splFileInfoTest_mocked() {
        $splFileInfo1 = mock( 'SplFileInfo' );
        $splFileInfo2 = mock( 'SplFileInfo' );

        $this->phockitoNonSerializableMockTestHarness->passSecondObjectOnly( $splFileInfo1, $splFileInfo2 );
        verify( $this->phockitoMockParameterCall, 0 )->execute( $splFileInfo1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo2 );
    }


    public function test_mixedNonSerializableAndSerializable() {
        $splFileInfo1 = mock( 'SplFileInfo' );
        $stdClass1    = mock( 'StdClass' );

        $this->phockitoNonSerializableMockTestHarness->passBothParameters( $splFileInfo1, $stdClass1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo1 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $stdClass1 );
    }


    public function test_invalidMixedTypeThrowsUsual_PHPUnitFrameworkAssertionFailedError() {
        $splFileInfo1 = mock( 'SplFileInfo' );
        $splFileInfo2 = mock( 'SplFileInfo' );
        $stdClass1    = mock( 'StdClass' );

        $this->phockitoNonSerializableMockTestHarness->passBothParameters( $splFileInfo1, $splFileInfo2 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo1 );

        $this->setExpectedException( 'PHPUnit_Framework_AssertionFailedError' );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $stdClass1 );
    }


    public function test_invalidNullExpectedOnlyThrowsUsual_PHPUnitFrameworkAssertionFailedError() {
        $splFileInfo1 = mock( 'SplFileInfo' );
        $splFileInfo2 = mock( 'SplFileInfo' );

        $this->phockitoNonSerializableMockTestHarness->passBothParameters( $splFileInfo1, $splFileInfo2 );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo1 );

        $this->setExpectedException( 'PHPUnit_Framework_AssertionFailedError' );
        verify( $this->phockitoMockParameterCall, 1 )->execute( null );
    }


    public function test_invalidObjectExpectedOnlyThrowsUsual_PHPUnitFrameworkAssertionFailedError() {
        $splFileInfo1 = mock( 'SplFileInfo' );
        $splFileInfo2 = mock( 'SplFileInfo' );

        $this->phockitoNonSerializableMockTestHarness->passBothParameters( $splFileInfo1, null );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo1 );

        $this->setExpectedException( 'PHPUnit_Framework_AssertionFailedError' );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $splFileInfo2 );
    }


    public function test_mockingWithCustomSleep_shouldNotFatal() {
        $phockitoClassWithCustomSerializeAction1 = mock( 'PhockitoClassWithCustomSerializeAction' );
        $phockitoClassWithCustomSerializeAction2 = mock( 'PhockitoClassWithCustomSerializeAction' );

        $this->phockitoNonSerializableMockTestHarness->passBothParameters( $phockitoClassWithCustomSerializeAction1, null );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $phockitoClassWithCustomSerializeAction1 );

        $this->setExpectedException( 'PHPUnit_Framework_AssertionFailedError' );
        verify( $this->phockitoMockParameterCall, 1 )->execute( $phockitoClassWithCustomSerializeAction2 );
    }


}

class PhockitoNonSerializableMockTestHarness {
    const CLASS_NAME = __CLASS__;

    /** @var PhockitoMockParameterCall */
    private $phockitoMockParameterCall;


    function __construct( PhockitoMockParameterCall $phockitoMockParameterCall ) {
        $this->phockitoMockParameterCall = $phockitoMockParameterCall;
    }


    public function passSecondObjectOnly( $object1, $object2 ) {
        return $this->phockitoMockParameterCall->execute( $object2 );
    }


    public function passBothParameters( $object1, $object2 ) {
        $this->phockitoMockParameterCall->execute( $object1 );
        $this->phockitoMockParameterCall->execute( $object2 );
    }
}

class PhockitoMockParameterCall {

    const CLASS_NAME = __CLASS__;


    /**
     * @param $value
     *
     * @return mixed
     */
    public function execute( $value ) {

    }
}

class PhockitoClassWithCustomSerializeAction {
    private $property = 'a';


    function __sleep() {
        return array( 'property' );
    }
}

