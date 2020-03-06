<?php namespace tests;
use blackcube\core\components\RouteEncoder;
use tests\RouteencoderTester;

class RouteEncoderCest
{
    public function testEncode(RouteencoderTester $I)
    {
        $encodedRoute = RouteEncoder::encode('tag', 12);
        $I->assertEquals('blackcube-tag-12', $encodedRoute);

        $encodedRoute = RouteEncoder::encode('tag');
        $I->assertEquals('blackcube-tag', $encodedRoute);
    }

    // tests
    public function testDecode(RouteencoderTester $I)
    {
        $decodedRoute = RouteEncoder::decode('blackcube-tag-12');
        $I->assertIsArray($decodedRoute);
        $I->assertEquals('tag', $decodedRoute['type']);
        $I->assertEquals(12, $decodedRoute['id']);

        $decodedRoute = RouteEncoder::decode('blackcube-tag12');
        $I->assertFalse($decodedRoute);

        $decodedRoute = RouteEncoder::decode('blackcube-tag');
        $I->assertIsArray($decodedRoute);
        $I->assertEquals('tag', $decodedRoute['type']);
        $I->assertArrayNotHasKey('id', $decodedRoute);

    }
}
