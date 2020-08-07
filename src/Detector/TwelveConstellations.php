<?php

namespace Click108\Detector;

use Click108\Constants\Click108Constants;
use Click108\DTO\Day\TwelveConstellationsDTO;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Independent\Kit\Contract\IHttpRequest;
use Independent\Kit\Network\Curl\Curl;
use Independent\Kit\Support\Scalar\StringMaster;

class TwelveConstellations
{
    /** @var IHttpRequest|Curl $curl */
    private $curl;

    /**
     * TwelveConstellations constructor.
     * @param IHttpRequest|null $curl
     */
    public function __construct(IHttpRequest $curl = null)
    {
        $this->curl = $curl ?? new Curl();
    }

    /**
     * @return array|TwelveConstellationsDTO[]
     */
    public function mine(): array
    {
        $redirectUrls = $this->getRedirectUrls();
        $dto = [];
        foreach ($redirectUrls as $url) {
            $response = $this->curl->get($url);
            if ($response->isSuccess()) {
                $body = $response->body();
                $start = strpos($body, "http://astro.click108.com.tw/");
                $end = strpos($body, "\";");
                $url = StringMaster::substr($body, $start, $end - $start);
                $response = $this->curl->get($url);
                if ($response->isSuccess()) {
                    $dto[] = new TwelveConstellationsDTO($response->body());
                }
            }
        }

        return $dto;
    }

    /**
     * @return array
     */
    private function getRedirectUrls(): array
    {
        $response = $this->curl->get(Click108Constants::HOST);
        $dom = new DOMDocument();
        $body = $response->body();
        @$dom->loadHTML($body);
        $attrName = 'class';
        $attrValue = 'STAR12_BOX';
        $finder = new DomXPath($dom);
        /** @var DOMElement[]|false $nodes */
        $nodes = $finder->query("//*[@" . $attrName . "='$attrValue']");
        $redirectUrls = [];
        if (!is_bool($nodes)) {
            foreach ($nodes as $node) {
                $liNodes = $node->getElementsByTagName('li');
                for ($i = 0; $i < $liNodes->count(); $i++) {
                    /** @var DOMElement $element */
                    $element = $liNodes->item($i);
                    $aElement = $element->getElementsByTagName('a');
                    for ($j = 0; $j < $aElement->count(); $j++) {
                        $element = $aElement->item($j);
                        $redirectUrls[] = $element->attributes->getNamedItem('href')->textContent;
                    }
                }
            }
        }

        return $redirectUrls;
    }
}
