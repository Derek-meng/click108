

Introduction
-

撈取每日十二星座Clinet


Requirements
-

PHP 7.2 or greater
PHPUnit 7 or greater (if you want to run unit tests)

 Installation
-

composer require derek/click108

 Examples
-
```
        $detector = new TwelveConstellations();
        $dto = $detector->day();
        foreach ($dto as $constellations) {
            //獲取星座名稱
            $constellations->name();
            //獲取當日
            $constellations->day();
            //獲取月份
            $constellations->month();
            //獲取整體分數
            $constellations->entireScore();
            //獲取整體內容評分
            $constellations->entireContent();
        }
```

### See More  DTO 

[src/DTO/TwelveConstellationsDTO](https://github.com/Derek-meng/click108/blob/master/src/DTO/Day/TwelveConstellationsDTO.php)

