<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Product;

class ProductTest extends TestCase
{
    /**
     * @dataProvider validProductProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('validProductProvider')]
    public function testProductInitialization($name, $prices, $type): void
    {
        $product = new Product($name, $prices, $type);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($prices, $product->getPrices());
        $this->assertEquals($type, $product->getType());
    }

    public static function validProductProvider(): array
    {
        return [
            ['Bread', ['EUR' => 2.5, 'USD' => 3], 'food'],
            ['Laptop', ['EUR' => 1000, 'USD' => 1100], 'tech'],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidTypeProvider')]
    public function testSetTypeThrowsException($type): void
    {
        $this->expectException(\Exception::class);
        new Product('Test', ['EUR' => 1], $type);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            ['invalid'],
            [''],
            ['123'],
        ];
    }

    public function testSetPricesSkipsInvalidCurrencyAndNegative(): void
    {
        $product = new Product('Test', ['EUR' => 10, 'USD' => -5, 'GBP' => 20], 'other');
        $this->assertArrayHasKey('EUR', $product->getPrices());
        $this->assertArrayNotHasKey('USD', $product->getPrices());
        $this->assertArrayNotHasKey('GBP', $product->getPrices());
    }

    public function testGetTVA(): void
    {
        $food = new Product('Bread', ['EUR' => 2], 'food');
        $tech = new Product('Laptop', ['EUR' => 1000], 'tech');
        $this->assertEquals(0.1, $food->getTVA());
        $this->assertEquals(0.2, $tech->getTVA());
    }

    public function testListCurrencies(): void
    {
        $product = new Product('Test', ['EUR' => 10, 'USD' => 20], 'other');
        $currencies = $product->listCurrencies();
        $this->assertContains('EUR', $currencies);
        $this->assertContains('USD', $currencies);
    }

    public function testGetPriceValidAndInvalid(): void
    {
        $product = new Product('Test', ['EUR' => 10, 'USD' => 20], 'other');
        $this->assertEquals(10, $product->getPrice('EUR'));
        $this->assertEquals(20, $product->getPrice('USD'));
        $this->expectException(\Exception::class);
        $product->getPrice('GBP');
    }

    public function testGetPriceThrowsIfCurrencyNotAvailable(): void
    {
        $product = new Product('Test', ['EUR' => 10], 'other');
        $this->expectException(\Exception::class);
        $product->getPrice('USD');
    }
}
