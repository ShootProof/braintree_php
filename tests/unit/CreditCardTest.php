<?php

namespace Test\Unit;

require_once dirname(__DIR__) . '/Setup.php';

use DateTime;
use Test\Setup;
use Braintree;

class CreditCardTest extends Setup
{
    public function testGet_givesErrorIfInvalidProperty()
    {
        $this->expectError();
        $cc = Braintree\CreditCard::factory([]);
        $cc->foo;
    }

    public function testCreate_throwsIfInvalidKey()
    {
        $this->expectException('InvalidArgumentException', 'invalid keys: invalidKey');
        Braintree\CreditCard::create(['invalidKey' => 'foo']);
    }

    public function testIsDefault()
    {
        $creditCard = Braintree\CreditCard::factory(['default' => true]);
        $this->assertTrue($creditCard->isDefault());

        $creditCard = Braintree\CreditCard::factory(['default' => false]);
        $this->assertFalse($creditCard->isDefault());
    }

    public function testMaskedNumber()
    {
        $creditCard = Braintree\CreditCard::factory(['bin' => '123456', 'last4' => '7890']);
        $this->assertEquals('123456******7890', $creditCard->maskedNumber);
    }

    public function testCreateSignature()
    {
        $expected = [
            'billingAddressId', 'cardholderName', 'cvv', 'number',
            'expirationDate', 'expirationMonth', 'expirationYear', 'token', 'venmoSdkPaymentMethodCode',
            'deviceData', 'paymentMethodNonce',
            ['options' => ['makeDefault', 'verificationMerchantAccountId', 'verifyCard', 'verificationAmount', 'verificationAccountType', 'venmoSdkSession', 'failOnDuplicatePaymentMethod']],
            [
                'billingAddress' => [
                    'firstName',
                    'lastName',
                    'company',
                    'countryCodeAlpha2',
                    'countryCodeAlpha3',
                    'countryCodeNumeric',
                    'countryName',
                    'extendedAddress',
                    'locality',
                    'region',
                    'postalCode',
                    'streetAddress',
                ],
            ],
            'customerId',
            [
                'threeDSecurePassThru' => [
                    'eciFlag',
                    'cavv',
                    'xid',
                    'threeDSecureVersion',
                    'authenticationResponse',
                    'directoryResponse',
                    'cavvAlgorithm',
                    'dsTransactionId',
                ]
            ]
        ];
        $this->assertEquals($expected, Braintree\CreditCardGateway::createSignature());
    }

    public function testUpdateSignature()
    {
        $expected = [
            'billingAddressId', 'cardholderName', 'cvv', 'number',
            'expirationDate', 'expirationMonth', 'expirationYear', 'token', 'venmoSdkPaymentMethodCode',
            'deviceData', 'paymentMethodNonce',
            [
                'options' => [
                    'makeDefault',
                    'verificationMerchantAccountId',
                    'verifyCard',
                    'verificationAmount',
                    'verificationAccountType',
                    'venmoSdkSession',
                    'failOnDuplicatePaymentMethod',
                ]
            ],
            [
                'billingAddress' => [
                    'firstName',
                    'lastName',
                    'company',
                    'countryCodeAlpha2',
                    'countryCodeAlpha3',
                    'countryCodeNumeric',
                    'countryName',
                    'extendedAddress',
                    'locality',
                    'region',
                    'postalCode',
                    'streetAddress',
                    [
                        'options' => [
                            'updateExisting'
                        ]
                    ]
                ],
            ],
            [
            'threeDSecurePassThru' => [
                'eciFlag',
                'cavv',
                'xid',
                'threeDSecureVersion',
                'authenticationResponse',
                'directoryResponse',
                'cavvAlgorithm',
                'dsTransactionId',
            ]
            ],
        ];
        $this->assertEquals($expected, Braintree\CreditCardGateway::updateSignature());
    }

    public function testErrorsOnFindWithBlankArgument()
    {
        $this->expectException('InvalidArgumentException');
        Braintree\CreditCard::find('');
    }

    public function testErrorsOnFindWithWhitespaceArgument()
    {
        $this->expectException('InvalidArgumentException');
        Braintree\CreditCard::find('  ');
    }

    public function testErrorsOnFindWithWhitespaceCharacterArgument()
    {
        $this->expectException('InvalidArgumentException');
        Braintree\CreditCard::find('\t');
    }

    public function testVerificationIsLatestVerification()
    {
        $creditCard = Braintree\CreditCard::factory(
            [
                'verifications' => [
                    [
                        'id' => '123',
                        'createdAt' => DateTime::createFromFormat('Ymd', '20121212')
                    ],
                    [
                        'id' => '932',
                        'createdAt' => DateTime::createFromFormat('Ymd', '20121215')
                    ],
                    [
                        'id' => '456',
                        'createdAt' => DateTime::createFromFormat('Ymd', '20121213')
                    ]
                ]
            ]
        );

        $this->assertEquals('932', $creditCard->verification->id);
    }
}
