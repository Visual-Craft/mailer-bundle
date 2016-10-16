<?php

namespace VisualCraft\Bundle\MailerBundle\Tests;

use VisualCraft\Bundle\MailerBundle\SendStatus;

class SendStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getIsSuccessfulData
     * @param int $successRecipientsCount
     * @param bool $isSuccessful
     */
    public function testIsSuccessful($successRecipientsCount, $isSuccessful)
    {
        $sendStatus = new SendStatus($successRecipientsCount, []);
        self::assertEquals($isSuccessful, $sendStatus->isSuccessful());
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
        self::assertEquals($successRecipientsCount, $sendStatus->getSuccessRecipientsCount());
    }

    public function testGetFailedRecipients()
    {
        $failedRecipients = ['foo'];
        $sendStatus = new SendStatus(2, $failedRecipients);
        self::assertEquals($failedRecipients, $sendStatus->getFailedRecipients());
    }
}
