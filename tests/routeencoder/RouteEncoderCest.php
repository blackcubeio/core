<?php namespace tests;
use blackcube\core\components\RouteEncoder;
use tests\RouteencoderTester;

class RouteEncoderCest
{
    public function testEncode(RouteencoderTester $I)
    {
        $encodedRoute = RouteEncoder::encode('tag', 12);
        $I->assertEquals('/blackcube/tag-12', $encodedRoute);
    }

    // tests
    public function testDecode(RouteencoderTester $I)
    {
        $decodedRoute = RouteEncoder::decode('tag-12');
        $I->assertIsArray($decodedRoute);
        $I->assertEquals('tag', $decodedRoute['type']);
        $I->assertEquals(12, $decodedRoute['id']);

        $decodedRoute = RouteEncoder::decode('tag12');
        $I->assertFalse($decodedRoute);

        $decodedRoute = RouteEncoder::decode('tag');
        $I->assertFalse($decodedRoute);

    }
}
