<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Entity\Wallet;

class WalletTest extends TestCase
{
    /**
     * @dataProvider currencyProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('currencyProvider')]
    public function testWalletInitialization($currency): void
    {
        $wallet = new Wallet($currency);
        $this->assertEquals($currency, $wallet->getCurrency());
        $this->assertEquals(0, $wallet->getBalance());
    }

    public static function currencyProvider(): array
    {
        return [
            ['USD'],
            ['EUR'],
        ];
    }

    public function testSetBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100.5);
        $this->assertEquals(100.5, $wallet->getBalance());
    }

    public function testSetBalanceThrowsExceptionOnNegative(): void
    {
        $this->expectException(\Exception::class);
        $wallet = new Wallet('USD');
        $wallet->setBalance(-10);
    }

    public function testSetCurrencyValidAndInvalid(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setCurrency('USD');
        $this->assertEquals('USD', $wallet->getCurrency());
        $this->expectException(\Exception::class);
        $wallet->setCurrency('GBP');
    }

    public function testRemoveFundValidAndInvalid(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(50);
        $wallet->removeFund(20);
        $this->assertEquals(30, $wallet->getBalance());
        $this->expectException(\Exception::class);
        $wallet->removeFund(-10);
        $this->expectException(\Exception::class);
        $wallet->removeFund(100);
    }

    public function testRemoveFundThrowsOnInsufficientFunds(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(10);
        $this->expectException(\Exception::class);
        $wallet->removeFund(20);
    }

    public function testAddFundValidAndInvalid(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->addFund(25);
        $this->assertEquals(25, $wallet->getBalance());
        $this->expectException(\Exception::class);
        $wallet->addFund(-5);
    }
}
