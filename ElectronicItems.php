<?php

class ElectronicItems
{
    private $items = array();
    public function __construct(array $items)
    {
        $this->items = $items;
    }
    /**
     * Returns the items depending on the sorting type requested
     *
     * @return array
     */
    public function getSortedItems()
    {
        $sorted = array();
        foreach ($this->items as $item)
        {
            $sorted[($item->price * 100)] = $item;
        }
        ksort($sorted, SORT_NUMERIC);
        return $sorted;
    }
    /**
     *
     * @param string $type
     * @return array
     */
    public function getItemsByType($type)
    {
        if (in_array($type, ElectronicItem::$types))
        {
            $callback = function($item) use ($type)
            {
                return $item->type == $type;
            };
            $items = array_filter($this->items, $callback);
        }
        return false;
    }
}
abstract class ElectronicItem
{
    /**
     * @var float
     */
    public $price;
    /**
     * @var string
     */
    private $type;
    public $wired;
    const ELECTRONIC_ITEM_TELEVISION = 'television';
    const ELECTRONIC_ITEM_CONSOLE = 'console';
    const ELECTRONIC_ITEM_MICROWAVE = 'microwave';
    const ELECTRONIC_ITEM_CONTROLLER = 'controller';
    private static $types = array(self::ELECTRONIC_ITEM_CONSOLE,
        self::ELECTRONIC_ITEM_MICROWAVE, self::ELECTRONIC_ITEM_TELEVISION, self::ELECTRONIC_ITEM_CONTROLLER);
    abstract public function maxExtras() : int;

    function getPrice()
    {
        return $this->price;
    }
    function getType()
    {
        return $this->type;
    }
    function getWired()
    {
        return $this->wired;
    }
    function setPrice($price)
    {
        $this->price = $price;
    }
    function setType($type)
    {
        $this->type = $type;
    }
    function setWired($wired)
    {
        $this->wired = $wired;
    }
}

class Console extends ElectronicItem
{
    const MAXEXTRAS = 4;

    function __construct(){

        $this->setPrice(150);
        $this->setType(self::ELECTRONIC_ITEM_CONSOLE);
    }

    function item($remoteController = 0, $wiredController = 0) {
        if (($remoteController + $wiredController) > $this->maxExtras()) {
            return false;
        }
        $item = new \stdClass();
        $item->price = $this->getPrice();
        $item->type = $this->getType();
        $controller = new Controller();

        $item->price += $controller->item(true, 2)->price;
        $item->price += $controller->item(false, 2)->price;
        return $item;
    }

    function maxExtras() : int
    {
        return self::MAXEXTRAS;
    }
}

class Television extends ElectronicItem
{
    const TELEVISION_TYPE1 = 'TV1';
    const TELEVISION_TYPE2 = 'TV2';
    public $type;

    function __construct($type){
        $this->type = $type;
        switch ($type) {
            case self::TELEVISION_TYPE1:
                $this->setPrice(200);
                break;
            case self::TELEVISION_TYPE2:
                $this->setPrice(100);
                break;
        }
        $this->setType(self::ELECTRONIC_ITEM_TELEVISION);
    }
    function item($remoteController = 0, $wiredController = 0) {
        $item = new \stdClass();
        $item->price = $this->getPrice();
        $item->type = $this->getType();
        $controller = new Controller();
        switch ($this->type) {
            case self::TELEVISION_TYPE1:
                $item->price += $controller->item(false, 2)->price;
                break;
            case self::TELEVISION_TYPE2:
                $item->price += $controller->item(false, 1)->price;
                break;
        }
        $items[] = $item;
        return $item;
    }
    function maxExtras(): int
    {
        return true;
    }
}

class Microwave extends ElectronicItem
{
    function __construct(){
        $this->setPrice(50);
        $this->setType(self::ELECTRONIC_ITEM_MICROWAVE);
    }

    function item() {
        $item = new \stdClass();
        $item->price = $this->getPrice();
        $item->type = $this->getType();
        $this->wired = $this->getWired();
        return $item;
    }

    function maxExtras(): int
    {
        return 0;
    }
}

class Controller extends ElectronicItem
{
    function __construct(){
        $this->setType(self::ELECTRONIC_ITEM_CONTROLLER);
    }

    function item($wired, $number) {
        $item = new \stdClass();
        if ($wired) {
            $this->setPrice(10);
        }
        else {
            $this->setPrice(20);
        }
        $item->type = $this->getType();
        $price = 0;

        while ($number > 0) {
            $price += $this->getPrice();
            $number--;
        }
        $item->price = $price;

        return $item;
    }

    function maxExtras() : int
    {
        return 0;
    }
}
/*
 * Buy Microwave
 */
$microwaveObj = new Microwave();
$items[] = $microwaveObj->item();
/*
 * Buy TV with 2 remote controllers
 */
$televisionObj = new Television('TV1');
$items[] = $televisionObj->item(2);
/*
 * Buy TV with 1 remote controller
 */
$televisionObj = new Television('TV2');
$items[] = $televisionObj->item(1);
/*
 * Buy Console with 2 remote controller and 2 wired controller
 */
$consoleObj = new Console();
$items[] = $consoleObj->item(2, 2);


$electronicItems = new ElectronicItems($items);
$items1 = $electronicItems->getSortedItems();

$total = 0;
//Display all item prices
echo "\n-----Item Prices-----";
echo "\nTV #1: 200";
echo "\nTV #2: 100";
echo "\nMicrowave: 50";
echo "\nConsole: 150";
echo "\nWired Controllers: 10";
echo "\nRemote Controllers: 20";

echo "\n\n\n-----Question 1-----";
$consoleTotalPrice = 0;
foreach ($items1 as $item) {
    if (!empty($item)) {
        if ($item->type == 'console') {
            $consoleTotalPrice = $item->price;
        }
        echo "\nItem name: " . $item->type . "   Price:" . $item->price;
        $total += $item->price;
    }
}
echo "\nTotal Price: ".$total;
echo "\n\n\n-----Question 2-----";
echo "\nConsole and its controllers Total Price: " . $consoleTotalPrice;

?>