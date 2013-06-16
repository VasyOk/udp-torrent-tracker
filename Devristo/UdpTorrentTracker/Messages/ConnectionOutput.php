<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Chris
 * Date: 16-6-13
 * Time: 20:24
 * To change this template use File | Settings | File Templates.
 */

namespace Devristo\UdpTorrentTracker\Messages;


use Devristo\UdpTorrentTracker\Exceptions\ProtocolViolationException;

class ConnectionOutput {
    protected $action = 0;
    protected $transactionId;
    protected $connectionId;

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $connectionId
     */
    public function setConnectionId($connectionId)
    {
        $this->connectionId = $connectionId;
    }

    /**
     * @return mixed
     */
    public function getConnectionId()
    {
        return $this->connectionId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function isOpenHandshake(){
        return $this->connectionId == hex2bin("41727101980");
    }

    public function toBytes(){
        return pack("NN",$this->getAction(), $this->getTransactionId()).$this->getConnectionId();
    }

    public static function fromBytes($data){
        if(strlen($data) < 16)
            throw new ProtocolViolationException("Data packet should be at least 16 bytes long");

        $o = new self();

        $offset = 0;
        list($action, $transactionId) = unpack("NN", substring($data, $offset, 8));
        $offset += 8;

        $o->setConnectionId(substr($data, $offset, 8));
        $o->setAction($action);
        $o->setTransactionId($transactionId);

        return $o;
    }
}