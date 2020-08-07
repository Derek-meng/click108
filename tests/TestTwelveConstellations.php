<?php

namespace Click108\Tests;

use Click108\Detector\TwelveConstellations;
use Click108\DTO\Day\TwelveConstellationsDTO;
use PHPUnit\Framework\TestCase;

class TestTwelveConstellations extends TestCase
{
    public function testMine()
    {
        $detector = new TwelveConstellations();
        $dto = $detector->mine();
        foreach ($dto as $constellations) {
            var_dump($constellations->entireContent());
            $this->assertIsString($constellations->name());
            $this->assertIsInt($constellations->day());
            $this->assertIsInt($constellations->month());
            $this->assertIsInt($constellations->entireScore());
            $this->assertIsString($constellations->entireContent());
            $this->assertIsInt($constellations->loveScore());
            $this->assertIsString($constellations->loveContent());
            $this->assertIsInt($constellations->careerScore());
            $this->assertIsString($constellations->careerContent());
            $this->assertIsInt($constellations->fortuneScore());
            $this->assertIsString($constellations->fortuneContent());
        }
        $this->assertIsArray($dto);
    }

    public function testDTO()
    {
        $content = file_get_contents("../src/index.html");
        $dto = new TwelveConstellationsDTO($content, false);
        $this->assertEquals($dto->name(), '水瓶座');
        $this->assertEquals($dto->day(), 7);
        $this->assertEquals($dto->month(), 8);
        $this->assertEquals($dto->entireScore(), 3);
        $this->assertEquals($dto->entireContent(), '愛情磁場強度升高，有結婚打算的人不妨選今天向對方表明心意，成功機率頗高' .
            '。理財上可謂一波未平，一波又起，將理財權讓賢反而更顯輕鬆。人際關係遇險，需防備公司內一位與你實力相當的同事。');
        $this->assertEquals($dto->loveScore(), 3);
        $this->assertEquals($dto->loveContent(), '已婚者空閒時應多陪另一半，不妨安排一些戶外活動，可以增進彼此的情意。');
        $this->assertEquals($dto->careerScore(), 2);
        $this->assertEquals($dto->careerContent(), '事業運略差，工作上會有小障礙出現，大多來自於溝通不良，要注意口舌是非或是文件失誤喔！');
        $this->assertEquals($dto->fortuneScore(), 3);
        $this->assertEquals($dto->fortuneContent(), '暗財運偏弱，別奢望坐享其成。');
    }
}
