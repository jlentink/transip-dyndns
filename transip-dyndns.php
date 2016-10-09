#!/usr/bin/env php
<?php

define('APP_ROOT', dirname(__FILE__) . '/');

function writeError($message){
    $fh = fopen('php://stderr','a');
    fwrite($fh, $message);
    fclose($fh);
}

if(!is_file(APP_ROOT . 'lib/Transip/ApiSettings.php')){
    writeError("Could not find transip api.\n");
    exit(2);
}

if(!class_exists("SOAPClient")){
    writeError("The soap module is not enabled.\n");
    exit(2);
}


require APP_ROOT . 'lib/Transip/ApiSettings.php';
require APP_ROOT . 'lib/Transip/DomainService.php';

$username = null;
$privateKey = null;
$domain = null;
$domainTld  = null;
$domainEntry = null;
$publicIp = file_get_contents('https://api.ipify.org');
$options = getopt("u:p:d:", ["username:", "private-key:", "domain:"]);

if(!isset($options['u']) && !isset($options['username'])){
    writeError("No username provider\n");
    exit(2);
} else {
    if(isset($options['username'])){
        $username = $options['username'];
    }else {
        $username = $options['u'];
    }
}

if(!isset($options['p']) && !isset($options['private-key'])){
    writeError("No private key provider\n");
    exit(2);
}else {
    if(isset($options['private-key'])){
        $privateKey = $options['private-key'];
    }else {
        $privateKey = $options['p'];
    }

    if(!is_readable($privateKey) || !is_file($privateKey)){
        writeError("Private key is not readable\n");
        exit(2);
    }

}

if(!isset($options['d']) && !isset($options['domain'])){
    writeError("No domain provider\n");
    exit(2);
}else {
    if(isset($options['domain'])){
        $domain = $options['domain'];
    }else {
        $domain = $options['d'];
    }
}

if(substr_count($domain, '.') >  1){
    $parts = explode('.', $domain);
    $domainTld = array_pop($parts);
    $domainTld = array_pop($parts) . '.' . $domainTld;
    $domainEntry = implode('.', $parts);
}else {
    $domainTld = $domain;
    $domainEntry = '@';
}


Transip_ApiSettings::$login = $username;
Transip_ApiSettings::$privateKey = file_get_contents($privateKey);


try {
    $entrySet = false;
    $transipDomain = Transip_DomainService::getInfo($domainTld);
    /** @var Transip_DnsEntry $entry */
    foreach($transipDomain->dnsEntries as $entry){
        if($entry->type == $entry::TYPE_A && $entry->name === $domainEntry){
            $entrySet = true;
            $entry->content = $publicIp;
            $entry->expire = 60;
        }
    }

    if(!$entrySet)
        $transipDomain->dnsEntries[] = new Transip_DnsEntry($domainEntry, 60, Transip_DnsEntry::TYPE_A, $publicIp);

    Transip_DomainService::setDnsEntries($domainTld, $transipDomain->dnsEntries);
    print $domain . " is set to " . $publicIp . "\n";
    exit(0);
}

catch(SoapFault $e) {
    writeError('An error occurred: ' . $e->getMessage() . "\n");
}

