<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Person;
use App\Entity\Wallet;
use App\Entity\Product;

class PersonTest extends TestCase
{
    public function testPersonInitialization(): void
    {
        $person = new Person('Alice', 'EUR');
        $this->assertEquals('Alice', $person->getName());
        $this->assertInstanceOf(Wallet::class, $person->getWallet());
        $this->assertEquals('EUR', $person->getWallet()->getCurrency());
    }

    /**
     * @dataProvider nameProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('nameProvider')]
    public function testSetName($name): void
    {
        $person = new Person($name, 'USD');
        $this->assertEquals($name, $person->getName());
    }

    public static function nameProvider(): array
    {
        return [
            ['Bob'],
            ['Charlie'],
            ['Émilie'],
        ];
    }

    public function testSetWallet(): void
    {
        $person = new Person('Alice', 'USD');
        $wallet = new Wallet('EUR');
        $person->setWallet($wallet);
        $this->assertSame($wallet, $person->getWallet());
    }

    public function testHasFundTrueAndFalse(): void
    {
        $person = new Person('Test', 'EUR');
        $person->getWallet()->setBalance(50);
        $this->assertTrue($person->hasFund());
        $person->getWallet()->setBalance(0);
        $this->assertFalse($person->hasFund());
    }

    public function testTransfertFundSameCurrency(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'EUR');
        $alice->getWallet()->setBalance(100);
        $bob->getWallet()->setBalance(0);
        $alice->transfertFund(40, $bob);
        $this->assertEquals(60, $alice->getWallet()->getBalance());
        $this->assertEquals(40, $bob->getWallet()->getBalance());
    }

    public function testTransfertFundDifferentCurrencyThrows(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'USD');
        $alice->getWallet()->setBalance(100);
        $this->expectException(\Exception::class);
        $alice->transfertFund(10, $bob);
    }

    public function testDivideWallet(): void
    {
        $alice = new Person('Alice', 'EUR');
        $bob = new Person('Bob', 'EUR');
        $charlie = new Person('Charlie', 'EUR');
        $alice->getWallet()->setBalance(90);
        $bob->getWallet()->setBalance(0);
        $charlie->getWallet()->setBalance(0);
        $alice->divideWallet([$bob, $charlie]);
        $this->assertEquals(0, $alice->getWallet()->getBalance());
        $this->assertEquals(45, $bob->getWallet()->getBalance());
        $this->assertEquals(45, $charlie->getWallet()->getBalance());
    }

    public function testBuyProduct(): void
    {
        $person = new Person('Alice', 'EUR');
        $person->getWallet()->setBalance(100);
        $product = new Product('Bread', ['EUR' => 10], 'food');
        $person->buyProduct($product);
        $this->assertEquals(90, $person->getWallet()->getBalance());
    }

    public function testBuyProductThrowsIfCurrencyNotAvailable(): void
    {
        $person = new Person('Alice', 'USD');
        $person->getWallet()->setBalance(100);
        $product = new Product('Bread', ['EUR' => 10], 'food');
        $this->expectException(\Exception::class);
        $person->buyProduct($product);
    }
}
