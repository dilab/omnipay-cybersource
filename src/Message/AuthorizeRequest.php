<?php

namespace Dilab\Cybersource\Message;

/**
 * Cybersource Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{

    public function getData()
    {
        $this->validate('currency', 'amount');

        $fieldsToSign = ['access_key', 'profile_id', 'transaction_uuid', 'signed_field_names', 'unsigned_field_names', 'signed_date_time', 'locale', 'transaction_type', 'reference_number', 'amount', 'currency'];
        
        $data = $this->getBaseData() + $this->getTransactionData();
        $data['signed_date_time'] = gmdate("Y-m-d\TH:i:s\Z");
        $data['unsigned_field_names'] = '';
        $data['signed_field_names'] = implode(',', $fieldsToSign);
        $data['signature'] = $this->signData($data);
        return $data;
    }

    public function signData($data)
    {
        return base64_encode(hash_hmac('sha256', $this->buildDataToSign($data), $this->getSecretKey(), true));
    }

    public function buildDataToSign($data)
    {
        $signedFieldNames = explode(",", $data["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
            $dataToSign[] = $field . "=" . $data[$field];
        }
        return implode(",", $dataToSign);
    }

    public function getRequiredFields()
    {
        $extraFields = $this->getIsUsOrCanada() ? $this->getRequiredFieldsUsAndCanada() : array();
        return array_merge(array(
            'amount',
            'city',
            'country',
            'address1',
            'email',
            'firstName',
            'lastName',
            'currency',
        ), $extraFields);
    }

    public function getRequiredFieldsUsAndCanada()
    {
        return array(
            'postcode',
            'billingState',
        );
    }

    public function getTransactionData()
    {
        return array(
            'reference_number' => $this->getTransactionId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'payment_method' => $this->getPaymentMethod(),
            'bill_to_forename' => $this->getCard()->getFirstName(),
            'bill_to_surname' => $this->getCard()->getLastName(),
            'bill_to_email' => $this->getCard()->getEmail(),
            'bill_to_phone' => $this->getCard()->getBillingPhone(),
            'bill_to_address_line1' => $this->getCard()->getAddress1(),
            'bill_to_address_line2' => $this->getCard()->getAddress2(),
            'bill_to_address_city' => $this->getCard()->getCity(),
            'bill_to_address_state' => $this->getCard()->getBillingState(),
            'bill_to_address_country' => strtoupper($this->getCard()->getCountry()),
            'bill_to_address_postal_code' => $this->getCard()->getPostcode(),
            'bill_to_company_name' => $this->getCard()->getCompany(),
        );
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        return array(
            'access_key' => $this->getAccessKey(),
            'profile_id' => $this->getProfileId(),
            'locale' => 'en',
            'transaction_uuid' => $this->getUniqueID(),
            'transaction_type' => $this->getTransactionType(),
        );
    }

    /**
     * @return string
     */
    public function getUniqueID()
    {
        return uniqid();
    }

    public function getEndpoint()
    {
        return parent::getEndpoint() . '/pay';
    }

    public function getPaymentMethod()
    {
        return 'card';
    }

    public function getTransactionType()
    {
        return 'authorization';
    }
}
