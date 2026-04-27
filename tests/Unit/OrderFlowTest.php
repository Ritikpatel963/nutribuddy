<?php

namespace Tests\Unit;

use App\Support\OrderFlow;
use PHPUnit\Framework\TestCase;

class OrderFlowTest extends TestCase
{
    public function test_it_allows_valid_status_transitions(): void
    {
        $this->assertTrue(OrderFlow::canMoveTo('pending', 'confirmed'));
        $this->assertTrue(OrderFlow::canMoveTo('processing', 'packed'));
        $this->assertTrue(OrderFlow::canMoveTo('shipped', 'delivered'));
        $this->assertTrue(OrderFlow::canMoveTo('delivered', 'returned'));
    }

    public function test_it_blocks_invalid_status_transitions(): void
    {
        $this->assertFalse(OrderFlow::canMoveTo('pending', 'delivered'));
        $this->assertFalse(OrderFlow::canMoveTo('cancelled', 'processing'));
        $this->assertFalse(OrderFlow::canMoveTo('returned', 'delivered'));
    }
}
