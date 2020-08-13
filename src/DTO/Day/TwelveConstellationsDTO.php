<?php

namespace Click108\DTO\Day;

use Closure;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Independent\Kit\Support\Scalar\StringMaster;

class TwelveConstellationsDTO
{
    /** @var string $content */
    private $content;
    /** @var DOMXPath $domPath */
    private $domPath;
    /** @var bool $isNeedDecode */
    private $isNeedDecode;

    /***
     * TwelveConstellationsDTO constructor.
     * @param string $content
     * @param bool $isNeedUtf8Decode
     */
    public function __construct(string $content, bool $isNeedUtf8Decode = true)
    {
        $this->content = $content;
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $this->domPath = new DomXPath($dom);
        $this->isNeedDecode = $isNeedUtf8Decode;
    }

    /**
     * 星座名稱
     * @return string|null
     */
    public function name(): ?string
    {
        $name = null;
        $this->parse('//*[@class="TODAY_CONTENT"]', function (DOMElement $node) use (&$name) {
            $h3 = $node->getElementsByTagName('h3');
            for ($i = 0; $i < $h3->count(); $i++) {
                /** @var DOMElement $element */
                $element = $h3->item($i);
                $title = StringMaster::replace(
                    $this->isNeedDecode ? utf8_decode($element->textContent) : $element->textContent,
                    '今日',
                    ''
                );
                $name = StringMaster::replace($title, '解析', '');
            }
        });

        return $name;
    }

    /**
     * @return string
     */
    public function content()
    {
        return $this->content;
    }

    /**
     * 獲取日期
     * @return int|null
     */
    public function day(): ?int
    {
        return $this->getDate('//*[@class="DATE"]');
    }

    /**
     * 獲取月份
     * @return int|null
     */
    public function month(): ?int
    {
        return $this->getDate('//*[@class="MONTH"]');
    }

    /**
     * 整體運勢的評分
     * @return int|null
     */
    public function entireScore(): ?int
    {
        return $this->getScopesScore(0);
    }

    /**
     * 整體運勢說明
     * @return string|null
     */
    public function entireContent(): ?string
    {
        return $this->getScopeContent(1);
    }

    /**
     * 愛情運勢的評分
     * @return int
     */
    public function loveScore(): int
    {
        return $this->getScopesScore(2);
    }

    /**
     * 愛情運勢說明
     * @return string|null
     */
    public function loveContent(): ?string
    {
        return $this->getScopeContent(3);
    }

    /**
     * 事業運勢的評分
     * @return int|null
     */
    public function careerScore(): ?int
    {
        return $this->getScopesScore(4);
    }

    /**
     * 事業運勢說明
     * @return string|null
     */
    public function careerContent(): ?string
    {
        return $this->getScopeContent(5);
    }

    /**
     * 財運運勢的評分
     * @return int|null
     */
    public function fortuneScore(): ?int
    {
        return $this->getScopesScore(6);
    }

    /**
     * 財運運勢的說明
     * @return string|null
     */
    public function fortuneContent(): ?string
    {
        return $this->getScopeContent(7);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return [
            'content'        => $this->content(),
            'name'           => $this->name(),
            'month'          => $this->month(),
            'day'            => $this->day(),
            'entireScore'    => $this->entireScore(),
            'entireContent'  => $this->entireContent(),
            'loveScore'      => $this->loveScore(),
            'loveContent'    => $this->loveContent(),
            'careerScore'    => $this->careerScore(),
            'careerContent'  => $this->careerContent(),
            'fortuneScore'   => $this->fortuneScore(),
            'fortuneContent' => $this->fortuneContent(),
        ];
    }

    /**
     * @param string $expression
     * @return int|null
     */
    private function getDate(string $expression): ?int
    {
        $day = null;
        $this->parse($expression, function (DOMElement $node) use (&$day) {
            $img = $node->getElementsByTagName('img');
            for ($i = 0; $i < $img->count(); $i++) {
                /** @var DOMElement $element */
                $element = $img->item($i);
                $src = $element->attributes->getNamedItem('src');
                if (!is_null($src)) {
                    $href = $src->textContent;
                    $href = StringMaster::replace(
                        $href,
                        'https://yimgs.click108.com.tw/astro/images/2013/daily_images/DATE2/',
                        ''
                    );
                    $day = !is_null($day) ? (int)StringMaster::replace($href, '.png', '') * pow(10, 1 - $i) :
                        $day += (int)StringMaster::replace($href, '.png', '') * pow(10, 1 - $i);
                }
            }
        });

        return $day;
    }

    /**
     * @param int $index
     * @return int|null
     */
    private function getScopesScore(int $index): ?int
    {
        $score = null;
        /** @var DOMElement[]|false $nodes */
        $this->parse('//*[@class="TODAY_CONTENT"]', function (DOMElement $node) use ($index, &$score) {
            $p = $node->getElementsByTagName('p');
            if (!is_null($element = $p->item($index))) {
                $score = substr_count(
                    $this->isNeedDecode ? utf8_decode($element->textContent) : $element->textContent,
                    '★'
                );
            }
        });

        return $score;
    }

    /**
     * @param int $index
     * @return string|null
     */
    private function getScopeContent(int $index): ?string
    {
        $content = null;
        $this->parse('//*[@class="TODAY_CONTENT"]', function (DOMElement $node) use (&$content, $index) {
            $p = $node->getElementsByTagName('p');
            if (!is_null($element = $p->item($index))) {
                $content = $this->isNeedDecode ? utf8_decode($element->textContent) : $element->textContent;
            }
        });

        return $content;
    }

    /**
     * @param string $expression Evaluates the given XPath expression
     * @param Closure $closure iterates over the items in the DOMElement and passes each item to a callback
     * @see https://php.net/manual/en/domxpath.query.php
     */
    private function parse(string $expression, Closure $closure)
    {
        $nodes = $this->domPath->query($expression);
        if (is_bool(!$nodes)) {
            foreach ($nodes as $node) {
                $closure($node);
            }
        }
    }
}
