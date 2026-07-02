<?php
namespace common\library;

use yii\httpclient\Client;

class CurrencyHelper
{
    const CACHE_KEY = 'currency_symbols';
    const CACHE_DURATION = 60*60*24*7; //7 days

     // Standard ISO 4217 currency codes (active + commonly used)
    private static $iso4217Codes = [
        'AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN',
        'BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL',
        'BSD','BTN','BWP','BYN','BZD','CAD','CDF','CHF','CLP','CNY',
        'COP','CRC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EGP',
        'ERN','ETB','EUR','FJD','FKP','GBP','GEL','GHS','GIP','GMD',
        'GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS',
        'INR','IQD','IRR','ISK','JMD','JOD','JPY','KES','KGS','KHR',
        'KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD',
        'LSL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRU',
        'MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK',
        'NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG',
        'QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK',
        'SGD','SHP','SLE','SLL','SOS','SRD','SSP','STN','SVC','SYP',
        'SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS',
        'UAH','UGX','USD','UYU','UZS','VES','VND','VUV','WST','XAF',
        'XCD','XDR','XOF','XPF','YER','ZAR','ZMW','ZWL',
    ];

    public static function getSymbolList($isoOnly = true)
    {

        $cacheKey = self::CACHE_KEY . ($isoOnly ? '-iso' : '-all');
        return \Yii::$app->cache->getOrSet($cacheKey, function() use ($isoOnly) {
            $client = new Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl(\Yii::$app->params['currencyApi'])
                ->setData(['apiKey' => \Yii::$app->params['currencyFreaksApiKey']])
                ->send();

            if(!$response->isOK) {
                return []; // degrade gracefully
            }

            $data = $response->data; // decode to array
            $symbols = $data['currencySymbols'] ?? [];

            if ($isoOnly) {
                $symbols = array_intersect_key($symbols, array_flip(self::$iso4217Codes));
            }

            // sort alphabetically
            asort($symbols);
            return $symbols;
        },self::CACHE_DURATION);
    }

}