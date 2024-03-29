<?php
ini_set('display_errors', 1);
session_start();
// session_destroy();
class Gender {
  const MAN = 1;
  const WOMAN = 2;
  const TRANS = 3;
}
class History {
  public static function set($str) {
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear() {
    $_SESSION['history'] = '';
  }
}
abstract class Creature {
  protected $name;
  protected $hp;
  protected $attack;

 public function __construct($name, $hp, $attack) {
   $this->name = $name;
   $this->hp = $hp;
   $this->attack = $attack;
 }

 public function getName() {
   return $this->name;
 }
 public function getHp() {
   return $this->hp;
 }
 public function getAttack() {
   return $this->attack;
 }
 public function setHp($num) {
   $this->hp = $num;
 }

 abstract public function shout();
 public function attack($targetObj) {
   if (!mt_rand(0, 9)) {
     $this->attack *= 1.5;
     $this->attack = (int)$this->attack;
   }
   $targetObj->setHp($targetObj->getHp() - $this->attack);
 }
}
class Human extends Creature {
  protected $gender;
  public function __construct($name, $hp, $attack, $gender) {
    parent::__construct($name, $hp, $attack);
    $this->gender = $gender;
  }

  public function getGender() {
    return $this->gender;
  }

  public function shout() {
    switch ($this->gender) {
      case Gender::MAN:
        History::set('Omg!!');
        break;
      case Gender::WOMAN:
        History::set('Ahhhhhhhhhh!');
        break;
      case Gender::TRANS:
        History::set('Moreeeeeeeee!!!');
        break;
    }
  }
}
class Monster extends Creature {
  protected $img;

  public function __construct($name, $hp, $attack, $img) {
    parent::__construct($name, $hp, $attack);
    $this->img = $img;
  }

  public function getImg() {
    return $this->img;
  }

  public function shout() {
    History::set('Auch!!');
  }
}
class MagicMonster extends Monster {
  protected $magicAttack;

  public function __construct($name, $hp, $attack, $img, $magicAttack) {
    parent::__construct($name, $hp, $attack, $img);
    $this->magicAttack = $magicAttack;
  }

  public function getMagicAttack() {
    return $this->magicAttack;
  }

  public function attack($targetObj) {
    if (!mt_rand(0, 9)) {
      $targetObj->setHp($targetObj->getHp() - $this->magicAttack);
    } else {
      parent::attack($targetObj);
    }
  }
}

$human = new Human('Junya', 1000, mt_rand(60, 100), Gender::MAN);
$monsters = [];
$monsters[] = new Monster('Monster1', 500, mt_rand(50, 100), 'img/monster01.png');
$monsters[] = new MagicMonster('Monster2', 200, mt_rand(30, 80), 'img/monster02.png', mt_rand(100, 160));
$monsters[] = new Monster('Monster3', 250, mt_rand(10, 30), 'img/monster03.png');
$monsters[] = new Monster('Monster4', 20, mt_rand(5, 1000), 'img/monster04.png');
$monsters[] = new MagicMonster('Monster5', 340, mt_rand(70, 120), 'img/monster05.png', mt_rand(90, 150));

function createMonster() {
  global $monsters;
  $monster = $monsters[mt_rand(0, 4)];
  History::set($monster->getName().' appeared!!');
  $_SESSION['monster'] =  $monster;
}
function createHuman() {
  global $human;
  $_SESSION['human'] = $human;
}
function init() {
  History::clear();
  $_SESSION = [];
  createMonster();
  createHuman();
}
function gameover() {
  $_SESSION = [];
}

if (!empty($_POST)) {
  $startFlg = !empty($_POST['start']) ? true : false;
  $attackFlg = !empty($_POST['attack']) ? true : false;
  $escapeFlg = !empty($_POST['escape']) ? true : false;

  if ($startFlg) {
    init();
  } else {
    if ($attackFlg) {
      $_SESSION['human']->attack($_SESSION['monster']);
      $_SESSION['monster']->shout();
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->shout();

      if ($_SESSION['human']->getHp() <= 0) {
        gameover();
      } else {
        if ($_SESSION['monster']->getHp() <= 0) {
          createMonster();
        }
      }
    } elseif ($escapeFlg) {
      createMonster();
    }
  }
  $_POST = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dragon Quest</title>
  <link rel="stylesheet" href="style.min.css">
</head>
<body>
<div class="container">
  <h1>Dragon Quest</h1>
  <?php if (empty($_SESSION)) : ?>
    <form method="post" class='start-form'>
      <input type="submit" name="start" value="Game start">
    </form>
  <?php else : ?>
    <div class="monster">
      <img src="<?= $_SESSION['monster']->getImg() ?>">
      <p>HP: <?= $_SESSION['monster']->getHp() ?></p>
    </div>
    <div class="information">
      <div class="history">
        <p>HP: <?= $_SESSION['human']->getHp() ?></p>
        <p class='hidden'><?= $_SESSION['history'] ?></p>
      </div>
      <div class="option">
        <form class="battle-form" method="post">
          <ul>
            <li><input type='submit' name="attack" value='Attack'></li>
            <li><input type='submit' name="escape" value='Escape'></li>
          </ul>
        </form>
      </div>
    </div>
  <?php endif ?>
</div>
</body>
</html>
