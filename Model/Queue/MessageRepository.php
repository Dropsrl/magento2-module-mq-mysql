<?php

namespace Rcason\MqMysql\Model\Queue;

use Rcason\MqMysql\Api\Data\QueueMessageInterface;
use Rcason\MqMysql\Api\Data\QueueMessageInterfaceFactory;
use Rcason\MqMysql\Api\QueueMessageRepositoryInterface;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message as ResourceModel;
use Rcason\MqMysql\Model\ResourceModel\Queue\Message\CollectionFactory as CollectionFactory;

class MessageRepository implements QueueMessageRepositoryInterface
{
    /**
     * @var QueueMessageInterfaceFactory
     */
    protected $queueMessageFactory;
    
    /**
     * @var ResourceModel
     */
    protected $resourceModel;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var int
     */
    protected $maxRetries;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;    

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $timezone;    
    
    /**
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param int $maxRetries
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $maxRetries = 5
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->maxRetries = $maxRetries;
        $this->_date = $date;
        $this->timezone = $timezone;
    }
    
    /**
     * @inheritdoc
     */
    public function create(QueueMessageInterface $message)
    {
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function peek()
    {
        // Create collection instance and apply filter
        return $this->collectionFactory->create()
            ->addFieldToFilter('status', 0)
            ->setOrder('updated_at', 'ASC');
    }
    
    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if(!$id) {
            throw new \Exception('No id specified in queue message get');
        }
        
        $queueMessage = $this->queueMessageFactory->create();
        $this->resourceModel->load($queueMessage, $id);

        if($id != $queueMessage->getId()) {
            throw new \Exception('Queue message not found');
        }

        return $queueMessage;
    }
    
    /**
     * @inheritdoc
     */
    public function requeue(QueueMessageInterface $message)
    {
        // Trigger date update
        $message->setUpdatedAt(null);
        $message->setRetries(0);
        $message->setStatus(QueueMessageInterface::STATUS_TO_PROCESS);
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function remove(QueueMessageInterface $message)
    {
        $this->resourceModel->delete($message);
    }
    
    /**
     * @inheritdoc
     */
    public function setPending(QueueMessageInterface $message)
    {
        $message->setStatus(QueueMessageInterface::STATUS_TO_PROCESS);
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function setDone(QueueMessageInterface $message, $result)
    {
        $message->setStatus(QueueMessageInterface::STATUS_DONE);
        $message->setResult($result);
//        $message->setUpdatedAt($this->timezone->date()->format('Y-m-d H:i:s'));
        $message->setUpdatedAt($this->_date->gmtDate());
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function setError(QueueMessageInterface $message, $result)
    {
        $message->setStatus(QueueMessageInterface::STATUS_ERROR);
        $message->setResult($result);
//        $message->setUpdatedAt($this->timezone->date()->format('Y-m-d H:i:s'));
        $message->setUpdatedAt($this->_date->gmtDate());
        $this->resourceModel->save($message);
    }
    
    /**
     * @inheritdoc
     */
    public function setResult(QueueMessageInterface $message, $result)
    {
        $message->setResult($result);
        $this->resourceModel->save($message);
    }
}
