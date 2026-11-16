<?php

namespace Tests\Unit;

use App\Support\UmrahMoney;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UmrahMoneyTest extends TestCase
{
    public function test_money_round_trips_without_float_rounding(): void
    {
        $this->assertSame(1010, UmrahMoney::cents('10.10'));
        $this->assertSame('30.30', UmrahMoney::decimal(UmrahMoney::cents('10.10') + UmrahMoney::cents('20.20')));
        $this->assertSame('-8000.00', UmrahMoney::decimal(-800000));
    }

    public function test_more_than_two_decimal_places_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        UmrahMoney::cents('1.001');
    }
}
