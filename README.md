# Gemblue\Woowa

Simple Woowa Wrapper API. Woowa is popular platform to send Whatsapp message ☎️
https://woo-wa.com/

# How to use

Download the package with composer

```
composer require gemblue\woowa
```

Then, consume the library and send message

```
<?php

use Gemblue\Woowa\Woowa;

$Woowa = new Woowa;

$Woowa->setup([
    'sender' => '{senderphonenumber}',
    'domain' => '{domain}',
    'license' => '{license}',
    'ip' => 'http://116.203.92.59',
    'key' => '{key}'
]);

$send = $Woowa->sendMessage('+62777777', 'Pesan ini adalah pesan percobaan dari library gemblue/woowa');

if ($send) {
    echo 'Pesan terkirim';
}
```
