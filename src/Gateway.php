<?php
namespace Omnipay\Cybersource;

use Omnipay\Common\AbstractGateway;

/**
 * CyberSource Secure Acceptance Silent Order POST Gateway
 *
 * @link http:
 * http://apps.cybersource.com/library/documentation/dev_guides/Secure_Acceptance_WM/html/wwhelp/wwhimpl/js/html/wwhelp.htm#href=cover_WM.html
 */
class Gateway extends AbstractGateway
{

    public function getName()
    {
        return 'Cybersource';
    }

    public function getDefaultParameters()
    {
        return array(
            'profileId' => '',
            'secretKey' => '',
            'accessKey' => '',
            'testMode' => false,
        );
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\PurchaseRequest', $parameters);
    }

    /**
     *
     * @param array $parameters
     * @return \Omnipay\Cybersource\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Cybersource\Message\CompletePurchaseRequest', $parameters);
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

    public function generateSignature($data)
    {
        $data_to_sign = array();
        foreach ($data as $key => $value) {
            $data_to_sign[] = $key . "=" . $value;
        }
        $pairs = implode(',', $data_to_sign);

        return base64_encode(hash_hmac('sha256', $pairs, $this->getSecretKey(), true));
    }
}
