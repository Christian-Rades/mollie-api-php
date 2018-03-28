<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;

class PaymentTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCancelledReturnsTrueWhenStatusIsCancelled()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_CANCELLED;
        $this->assertTrue($payment->isCancelled());
    }

    public function testIsCancelledReturnsFalseWhenStatusIsNotCancelled()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isCancelled());

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertFalse($payment->isCancelled());
    }

    public function testIsExpiredReturnsTrueWhenStatusIsExpired()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_EXPIRED;
        $this->assertTrue($payment->isExpired());
    }

    public function testIsExpiredReturnsFalseWhenStatusIsNotExpired()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isExpired());

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertFalse($payment->isExpired());
    }

    public function testIsOpenReturnsTrueWhenStatusIsOpen()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_OPEN;
        $this->assertTrue($payment->isOpen());
    }

    public function testIsOpenReturnsFalseWhenStatusIsNotOpen()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isOpen());

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertFalse($payment->isOpen());
    }

    public function testIsPendingReturnsTrueWhenStatusIsPending()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_PENDING;
        $this->assertTrue($payment->isPending());
    }

    public function testIsPendingReturnsFalseWhenStatusIsNotPending()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isPending());

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertFalse($payment->isPending());
    }

    public function testIsPaidReturnsTrueWhenPaidDatetimeIsSet()
    {
        $payment = new Payment();

        $payment->paidAt = "2016-10-24";
        $this->assertTrue($payment->isPaid());
    }

    public function testIsPaidReturnsFalseWhenStatusIsPaid()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_PAID;
        $this->assertFalse($payment->isPaid());
    }

    public function testIsPaidReturnsFalseWhenStatusIsNotPaid()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isPaid());

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertFalse($payment->isPaid());
    }

    public function testHasSettlementReturnsTrueWhenThereIsASettlementAmount()
    {
        $payment = new Payment();

        $payment->settlementAmount = (object) ["value" => "10.00", "currecy" => "EUR"];
        $this->assertTrue($payment->hasSettlement());
    }

    public function testHasSettlementReturnsFalseWhenThereIsNoSettlementAmount()
    {
        $payment = new Payment();

        $payment->settlementAmount = null;
        $this->assertFalse($payment->hasSettlement());
    }

    public function testHasRefundsReturnsTrueWhenPaymentHasRefunds()
    {
        $payment = new Payment();

        $payment->_links = new stdClass();
        $payment->_links->refunds = (object) ["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds", "type" => "application/json"];

        $this->assertTrue($payment->hasRefunds());
    }

    public function testHasRefundsReturnsFalseWhenPaymentHasNoRefunds()
    {
        $payment = new Payment();

        $payment->_links = new stdClass();
        $this->assertFalse($payment->hasRefunds());
    }

    public function testHasChargedbacksReturnsTrueWhenPaymentHasChargebacks()
    {
        $payment = new Payment();

        $payment->_links = new stdClass();
        $payment->_links->chargebacks = (object) ["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks", "type" => "application/json"];

        $this->assertTrue($payment->hasChargebacks());
    }

    public function testHasChargedbacksReturnsFalseWhenPaymentHasNoChargebacks()
    {
        $payment = new Payment();

        $payment->_links = new stdClass();
        $this->assertFalse($payment->hasChargebacks());
    }

    public function testIsFailedReturnsTrueWhenStatusIsFailed()
    {
        $payment = new Payment();

        $payment->status = PaymentStatus::STATUS_FAILED;
        $this->assertTrue($payment->isFailed());
    }

    public function testIsFailedReturnsFalseWhenStatusIsNotFailed()
    {
        $payment = new Payment();

        $payment->status = null;
        $this->assertFalse($payment->isFailed());

        $payment->status = PaymentStatus::STATUS_OPEN;
        $this->assertFalse($payment->isFailed());
    }

    public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsFirst()
    {
        $payment = new Payment();

        $payment->sequenceType = SequenceType::SEQUENCETYPE_FIRST;
        $this->assertFalse($payment->hasSequenceTypeRecurring());
        $this->assertTrue($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsRecurring()
    {
        $payment = new Payment();

        $payment->sequenceType = SequenceType::SEQUENCETYPE_RECURRING;
        $this->assertTrue($payment->hasSequenceTypeRecurring());
        $this->assertFalse($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsFalseWhenRecurringTypeIsNone()
    {
        $payment = new Payment();

        $payment->sequenceType = SequenceType::SEQUENCETYPE_ONEOFF;
        $this->assertFalse($payment->hasSequenceTypeFirst());
        $this->assertFalse($payment->hasSequenceTypeRecurring());
    }

    public function testGetCheckoutUrlReturnsPaymentUrlFromLinksObject()
    {
        $payment = new Payment();

        $payment->_links = new stdClass();
        $payment->_links->checkout = new stdClass();
        $payment->_links->checkout->href = "https://example.com";

        $this->assertSame($payment->getCheckoutUrl(), "https://example.com");
    }

    public function testCanBeRefundedReturnsTrueWhenAmountRemainingIsSet()
    {
        $payment = new Payment();

        $payment->amountRemaining = 15;
        $this->assertTrue($payment->canBeRefunded());
        $this->assertTrue($payment->canBePartiallyRefunded());
    }

    public function testCanBeRefundedReturnsFalseWhenAmountRemainingIsNull()
    {
        $payment = new Payment();

        $payment->amountRemaining = null;
        $this->assertFalse($payment->canBeRefunded());
        $this->assertFalse($payment->canBePartiallyRefunded());
    }

    public function testGetAmountRefundedReturnsAmountRefundedAsFloat()
    {
        $payment = new Payment();

        $payment->amountRefunded = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getAmountRefunded());
    }

    public function testGetAmountRefundedReturns0WhenAmountRefundedIsSetToNull()
    {
        $payment = new Payment();

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRefunded());
    }

    public function testGetAmountRemainingReturnsAmountRemainingAsFloat()
    {
        $payment = new Payment();

        $payment->amountRemaining = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getAmountRemaining());
    }

    public function testGetAmountRemainingReturns0WhenAmountRemainingIsSetToNull()
    {
        $payment = new Payment();

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRemaining());
    }
}
