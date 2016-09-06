<?php

namespace VisualCraft\Bundle\MailerBundle;

class SendStatus
{
    /**
     * @var int
     */
    private $successRecipientsCount;

    /**
     * @var array
     */
    private $failedRecipients;

    /**
     * @param int $successRecipientsCount
     * @param array $failedRecipients
     */
    public function __construct($successRecipientsCount, $failedRecipients)
    {
        $this->successRecipientsCount = $successRecipientsCount;
        $this->failedRecipients = $failedRecipients;
    }

    /**
     * @return int
     */
    public function getSuccessRecipientsCount()
    {
        return $this->successRecipientsCount;
    }

    /**
     * @return array
     */
    public function getFailedRecipients()
    {
        return $this->failedRecipients;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->getSuccessRecipientsCount() > 0;
    }
}
