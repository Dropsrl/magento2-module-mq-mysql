<?php

namespace Rcason\MqMysql\Model;

use Rcason\Mq\Api\Data\MessageEnvelopeInterface;
use Rcason\Mq\Api\Data\MessageEnvelopeInterfaceFactory;
use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;

class MysqlBroker implements \Rcason\Mq\Api\BrokerInterface
{
    /**
     * @var QueueMessageInterfaceFactory
     */
    protected $queueMessageFactory;
    
    /**
     * @var MessageEnvelopeInterfaceFactory
     */
    protected $messageEnvelopeFactory;
    
    /**
     * @var QueueMessageRepositoryInterface
     */
    protected $queueMessageRepository;
    
    /**
     * @var string
     */
    protected $queueName;
    
    protected $logger;
    
    /**
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param MessageEnvelopeInterfaceFactory $messageEnvelopeFactory
     * @param QueueMessageRepositoryInterface $queueMessageRepository
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        MessageEnvelopeInterfaceFactory $messageEnvelopeFactory,
        QueueMessageRepositoryInterface $queueMessageRepository,
        \Psr\Log\LoggerInterface $logger,
        $queueName = null
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->messageEnvelopeFactory = $messageEnvelopeFactory;
        $this->queueMessageRepository = $queueMessageRepository;
        $this->queueName = $queueName;
        $this->logger = $logger;
    }
    
    /**
     * {@inheritdoc}
     */
    public function enqueue(MessageEnvelopeInterface $messageEnvelope)
    {
        $queueMessage = $this->queueMessageFactory->create()
            ->setQueueName($this->queueName)
            ->setName($messageEnvelope->getName())
            ->setMessageContent($messageEnvelope->getContent());
        
        return $this->queueMessageRepository->create($queueMessage);
    }
    
    /**
     * {@inheritdoc}
     */
<<<<<<< HEAD
    public function peek($queueMessage)
=======
    public function peek($queueName)
>>>>>>> 3cf166ccf17b66bd67df413509637342b296bf8a
    {
        $queueMessage = $this->queueMessageRepository->peek($queueName);

        if(!$queueMessage) {
            return false;
        }
        return $queueMessage;
    }
    
    /**
     * {@inheritdoc}
     */
    public function acknowledge(MessageEnvelopeInterface $queueMessage, $result)
    {
        $message = $this->queueMessageRepository->get($queueMessage->getBrokerRef());
        $this->queueMessageRepository->setDone($message, 'DONE: ' . $result);
    }
    
    /**
     * {@inheritdoc}
     */
    public function reject(MessageEnvelopeInterface $queueMessage, $result)
    {
        $message = $this->queueMessageRepository->get($queueMessage->getBrokerRef());
        $this->queueMessageRepository->setError($message, 'ERROR: ' . $result);
//        $this->logger->info("Can't execute {$message->getEntityId()} message.");
//        $this->logger->critical($result);
    }
    
}
