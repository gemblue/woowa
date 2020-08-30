<?php

declare(strict_types=1);

namespace Gemblue\Woowa;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function is_array;
use function json_encode;
use function str_replace;
use function strlen;
use function substr;
use function substr_replace;
use function urldecode;

use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_VERBOSE;

class Woowa
{
    /** @var string Sender number */
    public $sender;

    /** @var string Client domain */
    public $domain;

    /** @var string License number */
    public $license;

    /** @var string Server ip */
    public $ip;

    /** @var string Server key */
    public $key;

    /**
     * Setup config.
     *
     * @param mixed $config
     */
    public function setup($config): void
    {
        $this->sender  = $config['sender'];
        $this->domain  = $config['domain'];
        $this->license = $config['license'];
        $this->ip      = $config['ip'];
        $this->key     = $config['key'];
    }

    /**
     * Send message
     */
    public function sendMessage(string $phoneNumber, string $message): bool
    {
        /** Reformat message */
        if (is_array($message)) {
            $find = $replace = [];

            foreach ($message[1] as $key => $value) {
                $find[]    = '{' . $key . '}';
                $replace[] = urldecode($value);
            }

            $message = str_replace($find, $replace, $message[0]);
        }

        /** Make sure the number begin with 62 */
        $phoneNumber = substr($phoneNumber, 0, 1) === '0' ? substr_replace($phoneNumber, '62', 0, 1) : $phoneNumber;

        /** Setup payload and send request to Woowa */
        $request = $this->request('send_message', [
            'key' => $this->key,
            'phone_no' => $phoneNumber,
            'message' => $message,
        ]);

        return $request === 'Success';
    }

    /**
     * Curl request to server
     *
     * @param mixed $payload
     *
     * @return mixed
     */
    private function request(string $action, $payload)
    {
        $url = $this->ip . '/api/' . $action;

        $content = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($content),
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        if (! empty($response)) {
            return $response;
        }

        return null;
    }
}
