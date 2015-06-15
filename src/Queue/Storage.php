<?php

namespace Realtor\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Storage
 * @package Realtor\Queue
 */
class Storage
{
    /**
     * @var AMQPStreamConnection
     */
    private $_connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $_channel;

    /**
     * @var string
     */
    private $_queueKey;

    /**
     * Init logic
     *
     * @param string $queueKey
     */
    public function __construct($queueKey)
    {
        $this->_queueKey = $queueKey;

        $this->_connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->_channel = $this->_connection->channel();

        $this->_channel->queue_declare($queueKey, false, false, false, false);
    }

    /**
     * Closes connection with queue
     */
    public function __destruct()
    {
        $this->_channel->close();
        $this->_connection->close();
    }

    /**
     * Adds new message to the queue
     *
     * @param mixed $message
     */
    public function enqueue($message)
    {
        $messageObj = new AMQPMessage($message);
        $this->_channel->basic_publish($messageObj, '', $this->_queueKey);
    }

    /**
     * Returns next message from the queue
     *
     * @param callable $callback
     */
    public function dequeue(callable $callback)
    {
        $this->_channel->basic_consume($this->_queueKey, '', false, true, false, false, $callback);

        while (count($this->_channel->callbacks)) {
            $this->_channel->wait();
        }

    }
}