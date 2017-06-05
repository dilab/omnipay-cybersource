<?php
namespace Omnipay\Cybersource\Message;

use Omnipay\Common\Message\RequestInterface;

/**
 *
 * @method \Omnipay\Cybersource\Message\Response send()
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://secureacceptance.cybersource.com';
    protected $testEndpoint = 'https://testsecureacceptance.cybersource.com';
    protected $endpoint = '';
    protected $isUsOrCanada = false;

    public function sendData($data)
    {
        return $this->response = new Response($this, $data, $this->getEndpoint());
    }

    public function getProfileId()
    {
        return $this->getParameter('profileId');
    }

    public function setProfileId($value)
    {
        return $this->setParameter('profileId', $value);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getAccessKey()
    {
        return $this->getParameter('accessKey');
    }

    public function setAccessKey($value)
    {
        return $this->setParameter('accessKey', $value);
    }

    public function getTransactionType()
    {
        return $this->getParameter('transactionType');
    }

    public function setTransactionType($value)
    {
        return $this->setParameter('transactionType', $value);
    }

    public function getIsUsOrCanada()
    {
        return $this->getParameter('isUsOrCanada');
    }

    public function setIsUsOrCanada($value)
    {
        return $this->setParameter('isUsOrCanada', $value);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     *
     * @param array $data
     * @param array $fields
     * @param string $secretKey
     *
     * @return string
     */
    public function generateSignature($data, $fields, $secretKey)
    {
        $data_to_sign = array();
        foreach ($fields as $field) {
            $data_to_sign[] = $field . "=" . $data[$field];
        }
        $pairs = implode(',', $data_to_sign);
        return base64_encode(hash_hmac('sha256', $pairs, $secretKey, true));
    }

}
