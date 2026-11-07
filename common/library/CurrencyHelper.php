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

    /**
     * ISO code => symbol. Fetched from CurrencyFreaks and cached for a week.
     *
     * If the API is unreachable, misconfigured or returns nothing, falls back to the built-in
     * ISO 4217 list (code => code) and does NOT cache the failure, so validation of the
     * Funding currency keeps working and the next request retries the API.
     */
    public static function getSymbolList($isoOnly = true)
    {
        $cache = \Yii::$app->cache;
        $cacheKey = self::CACHE_KEY . ($isoOnly ? '-iso' : '-all');

        $cached = $cache->get($cacheKey);
        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $symbols = [];
        try {
            $apiKey = \Yii::$app->params['currencyFreaksApiKey'] ?? null;
            if ($apiKey) {
                $response = (new Client(['requestConfig' => ['options' => ['timeout' => 5]]]))
                    ->createRequest()
                    ->setMethod('GET')
                    ->setUrl(\Yii::$app->params['currencyApi'])
                    ->setData(['apiKey' => $apiKey])
                    ->send();
                if ($response->isOk) {
                    $symbols = $response->data['currencySymbols'] ?? [];
                }
            }
        } catch (\Throwable $e) {
            \Yii::warning('Currency API unavailable: ' . $e->getMessage(), __METHOD__);
        }

        if ($isoOnly && $symbols !== []) {
            $symbols = array_intersect_key($symbols, array_flip(self::$iso4217Codes));
        }

        if ($symbols === []) {
            // Fallback: codes only, not cached.
            return array_combine(self::$iso4217Codes, self::$iso4217Codes);
        }

        asort($symbols);
        $cache->set($cacheKey, $symbols, self::CACHE_DURATION);

        return $symbols;
    }

}