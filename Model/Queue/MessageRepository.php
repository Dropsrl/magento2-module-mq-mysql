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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;    

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $timezone;
    /**
     * @var \Rcason\Mq\Helper\Data
     */
    private $helper;

    /**
     * @param QueueMessageInterfaceFactory $queueMessageFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Rcason\Mq\Helper\Data $helper
     */
    public function __construct(
        QueueMessageInterfaceFactory $queueMessageFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Rcason\Mq\Helper\Data $helper
    ) {
        $this->queueMessageFactory = $queueMessageFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
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
    public function peek($queue)
    {
        // Create collection instance and apply filter
        $status = [QueueMessageInterface::STATUS_ERROR,QueueMessageInterface::STATUS_TO_PROCESS];
        return $this->collectionFactory->create()
            ->addFieldToFilter('status', ["in"=> $status])
            ->addFieldToFilter('queue_name', $queue)
            ->addFieldToFilter('retries', array('lt' => $this->helper->getMaxRetries()))
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
        $message->setUpdatedAt($this->_date->gmtDate());
        $message->setRetries($message->getRetries()+1);
        $this->resourceModel->save($message);
    }

    /**
     * @param QueueMessageInterface $message
     * @param $retries
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function setRetries( QueueMessageInterface $message){
        $message->setRetries($message->getRetries()+1);
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
