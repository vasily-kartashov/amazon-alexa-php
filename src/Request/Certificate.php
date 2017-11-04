<?php

/**
 * Validate the request signature
 * Based on code from alexa-app: https://github.com/develpr/alexa-app by Kevin Mitchell
 */

namespace Alexa\Request;

use DateTime;
use Exception;
use InvalidArgumentException;

class Certificate
{
    const TIMESTAMP_VALID_TOLERANCE_SECONDS = 30;
    const SIGNATURE_VALID_PROTOCOL = 'https';
    const SIGNATURE_VALID_HOSTNAME = 's3.amazonaws.com';
    const SIGNATURE_VALID_PATH = '/echo.api/';
    const SIGNATURE_VALID_PORT = 443;
    const ECHO_SERVICE_DOMAIN = 'echo-api.amazon.com';
    const ENCRYPT_METHOD = "sha1WithRSAEncryption";

    /** @var string */
    public $requestId;

    /** @var mixed */
    public $timestamp;

    /** @var Session */
    public $session;

    /** @var string|null */
    public $certificateUrl;

    /** @var string|null */
    public $certificateFile;

    /** @var string|null */
    public $certificateContent;

    /** @var string|null */
    public $requestSignature;

    /** @var mixed */
    public $requestData;

    /**
     * @param $certificateUrl
     * @param $signature
     */
    public function __construct($certificateUrl, $signature)
    {
        $this->certificateUrl = $certificateUrl;
        $this->requestSignature = $signature;
    }

    /**
     * @param string $requestData
     * @return void
     */
    public function validateRequest($requestData)
    {
        $requestParsed = json_decode($requestData, true);
        // Validate the entire request by:

        // 1. Checking the timestamp.
        $this->validateTimestamp($requestParsed['request']['timestamp']);

        // 2. Checking if the certificate URL is correct.
        $this->verifySignatureCertificateURL();

        // 3. Checking if the certificate is not expired and has the right SAN
        $this->validateCertificate();

        // 4. Verifying the request signature
        $this->validateRequestSignature($requestData);
    }

    /**
     * Check if request is within the allowed time.
     * @param $timestamp
     * @return void
     */
    public function validateTimestamp($timestamp)
    {
        $now = new DateTime;
        $timestamp = new DateTime($timestamp);
        $differenceInSeconds = $now->getTimestamp() - $timestamp->getTimestamp();

        if ($differenceInSeconds > self::TIMESTAMP_VALID_TOLERANCE_SECONDS) {
            throw new InvalidArgumentException('Request timestamp was too old. Possible replay attack.');
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function validateCertificate()
    {
        $this->certificateContent = $this->getCertificate();
        $parsedCertificate = $this->parseCertificate($this->certificateContent);
        if ($parsedCertificate === false) {
            throw new Exception('Cannot parse certificate');
        }
        if (!$this->validateCertificateDate($parsedCertificate) || !$this->validateCertificateSAN($parsedCertificate, static::ECHO_SERVICE_DOMAIN)) {
            throw new InvalidArgumentException("The remote certificate doesn't contain a valid SANs in the signature or is expired.");
        }
    }

    /**
     * @param string $requestData
     * @throws InvalidArgumentException
     * @return void
     */
    public function validateRequestSignature($requestData)
    {
        if ($this->certificateContent === null) {
            throw new InvalidArgumentException('Empty certificate content');
        }
        if ($this->requestSignature === null) {
            throw new InvalidArgumentException('Empty request signature');
        }
        $certKey = openssl_pkey_get_public($this->certificateContent);
        $valid = openssl_verify($requestData, base64_decode($this->requestSignature), $certKey, self::ENCRYPT_METHOD);
        if (!$valid) {
            throw new InvalidArgumentException('Request signature could not be verified');
        }
    }

    /**
     * Returns true if the certificate is not expired.
     *
     * @param array $parsedCertificate
     * @return boolean
     */
    public function validateCertificateDate(array $parsedCertificate)
    {
        $validFrom = $parsedCertificate['validFrom_time_t'];
        $validTo = $parsedCertificate['validTo_time_t'];
        $time = time();
        return ($validFrom <= $time && $time <= $validTo);
    }

    /**
     * Returns true if the configured service domain is present/valid, false if invalid/not present
     * @param array $parsedCertificate
     * @param $amazonServiceDomain
     * @return bool
     */
    public function validateCertificateSAN(array $parsedCertificate, $amazonServiceDomain)
    {
        if (strpos($parsedCertificate['extensions']['subjectAltName'], $amazonServiceDomain) === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verify URL of the certificate
     * @return void
     * @throws InvalidArgumentException
     * @author Emanuele Corradini <emanuele@evensi.com>
     */
    public function verifySignatureCertificateURL()
    {
        if ($this->certificateUrl === null) {
            throw new InvalidArgumentException('Certificate URL not set');
        }
        $url = parse_url($this->certificateUrl);

        if ($url['scheme'] !== static::SIGNATURE_VALID_PROTOCOL) {
            throw new InvalidArgumentException('Protocol isn\'t secure. Request isn\'t from Alexa.');
        } else if ($url['host'] !== static::SIGNATURE_VALID_HOSTNAME) {
            throw new InvalidArgumentException('Certificate isn\'t from Amazon. Request isn\'t from Alexa.');
        } else if (strpos($url['path'], static::SIGNATURE_VALID_PATH) !== 0) {
            throw new InvalidArgumentException('Certificate isn\'t in "' . static::SIGNATURE_VALID_PATH . '" folder. Request isn\'t from Alexa.');
        } else if (isset($url['port']) && $url['port'] !== static::SIGNATURE_VALID_PORT) {
            throw new InvalidArgumentException('Port isn\'t ' . static::SIGNATURE_VALID_PORT . '. Request isn\'t from Alexa.');
        }
    }


    /**
     * Parse the X509 certificate
     * @param mixed $certificate certificate contents
     * @return array
     * @psalm-return array<mixed, mixed>|false
     */
    public function parseCertificate($certificate)
    {
        return openssl_x509_parse($certificate, true);
    }

    /**
     * Return the certificate to the underlying code by fetching it from its location.
     * Override this function if you wish to cache the certificate for a specific time.
     * @return string
     */
    public function getCertificate()
    {
        return $this->fetchCertificate();
    }

    /**
     * Perform the actual download of the certificate
     * @return string
     * @throws Exception
     */
    public function fetchCertificate()
    {
        if (!function_exists("curl_init")) {
            throw new InvalidArgumentException('CURL is required to download the Signature Certificate.');
        }
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('Cannot initialize CURL');
        }
        curl_setopt($ch, CURLOPT_URL, $this->certificateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $payload = curl_exec($ch);
        curl_close($ch);
        if (is_bool($payload)) {
            throw new Exception('Cannot load certificate');
        }
        return $payload;
    }
}
