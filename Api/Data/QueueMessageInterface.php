<?php

namespace Rcason\MqMysql\Api\Data;

interface QueueMessageInterface
{
    const STATUS_TO_PROCESS = 0;
    const STATUS_DONE = 1;
    const STATUS_ERROR = 2;
    
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    
    const ENTITY_ID = 'entity_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    const RETRIES = 'retries';
    const QUEUE_NAME = 'queue_name';
    const RESULT = 'result';
    const MESSAGE_BODY = 'message_body';
    
    /**
     * Gets the entity ID.
     *
     * @return int|null Entity ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);
    
    /**
     * Gets creation timestamp.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Sets creation timestamp.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setCreatedAt($timestamp);
    
    /**
     * Gets last update timestamp.
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Sets last update timestamp.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setUpdatedAt($timestamp);
    
    /**
     * Gets the message status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Sets the message status.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);
    
    /**
     * Gets the retries count.
     *
     * @return int
     */
    public function getRetries();

    /**
     * Sets retries count.
     *
     * @param int $retries
     * @return $this
     */
    public function setRetries($retries);
    
    /**
     * Gets the queue name.
     *
     * @return string
     */
    public function getQueueName();

    /**
     * Sets the queue name.
     *
     * @param string $queueName
     * @return $this
     */
    public function setQueueName($queueName);
    
    /**
     * Gets the message body.
     *
     * @return string
     */
    public function getMessageBody();

    /**
     * Sets the message body.
     *
     * @param string $messageBody
     * @return $this
     */
    public function setMessageBody($messageBody);
    
    /**
     * Gets the message result.
     *
     * @return string
     */
    public function getResult();
    
    /**
     * Sets the message result.
     *
     * @param string $result
     * @return $this
     */
    public function setResult($result);
}
