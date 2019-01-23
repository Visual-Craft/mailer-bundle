<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use PHPUnit\Framework\TestCase;
use VisualCraft\Bundle\MailerBundle\SendStatus;

class SendStatusTest extends TestCase
{
    /**
     * @dataProvider getIsSuccessfulData
     * @param int $successRecipientsCount
     * @param bool $isSuccessful
     */
    public function testIsSuccessful($successRecipientsCount, $isSuccessful)
    {
        $sendStatus = new SendStatus($successRecipientsCount, []);
        $this->assertSame($isSuccessful, $sendStatus->isSuccessful());
    }

    /**
     * @return array
     */
    public function getIsSuccessfulData()
    {
        return [
            [1, true],
            [0, false],
            [-1, false],
            [null, false],
        ];
    }

    public function testGetSuccessRecipientsCount()
    {
        $successRecipientsCount = 2;
        $sendStatus = new SendStatus($successRecipientsCount, []);
        $this->assertSame($successRecipientsCount, $sendStatus->getSuccessRecipientsCount());
    }

    public function testGetFailedRecipients()
    {
        $failedRecipients = ['foo'];
        $sendStatus = new SendStatus(2, $failedRecipients);
        $this->assertSame($failedRecipients, $sendStatus->getFailedRecipients());
    }
}
