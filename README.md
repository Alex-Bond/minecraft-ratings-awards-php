Скрипт для получения бонуса за голосование на MC TOP.
====================

Описание
----------------------
Данный скрипт был специально разработан для администраторов серверов, которые хотят поощрять своих игроков за голосование за них на mctop.su.

Настройка
----------------------
Для того чтобы настроить данный скрипт вам понадобится настроить для начала твой профиль на MC Top. Для этого войти в контрольную панель своего сервера в топе и укажи в разделе "Данные" секретное слово и путь к данном скрипту.

Далее нам понадобится настроить сам скрипт. Все настройки хранятся в массиве $config. Его структура фиксированная и не может подвергаться изменениям со стороны человека слабо понимающего принцип работы скрипта.

Массив состоит из:
<table>
  <tr>
    <th>Название</th><th>Функционал</th>
  </tr>
  <tr>
    <td>db</td><td>Настройки базы данных</td>
  </tr>
  <tr>
    <td>security-code</td><td>Секретное слово из панели управления</td>
  </tr>
  <tr>
    <td>jobs</td><td>Задачи для выполнения</td>
  </tr>
</table>

Как ты заметил - есть некое поле "jobs" в котором указываются задачи для выполнения. Иными словами - можно делать при голосовании несколько действий сразу. Например, добавить денег игроку и добавить в какую-то табличку запись о голосовании.

Настройка jobs
----------------------

Для начала рассмотрим настройки задач:

*Jobs*
<table>
  <tr>
    <th>Название</th><th>Функционал</th><th>Формат</th><th>Значение по-умолчанию</th>
  </tr>
  <tr>
    <td>active</td><td>Включает выполнение данной задачи</td><td>boolean</td><td>true</td>
  </tr>
  <tr>
    <td>stable-name</td><td>Название таблички которую правим</td><td>string</td><td>iConomy</td>
  </tr>
  <tr>
    <td>type</td><td>Тип задачи (расшифровка в табличке ниже).</td><td>int</td><td>0</td>
  </tr>
  <tr>
    <td>advanced</td><td>Использовать ли ручное указание всех полей для изменения при type=4</td><td>boolean</td><td>false</td>
  </tr>
  <tr>
    <td>fields</td><td>Массив полей для изменения при включенном режиме "advanced". {player} в значении поля будет заменено на имя игрока который проголосовал.</td><td>array</td><td>array('username' => '{player}','item' => '1','item-count' => 64)</td>
  </tr>
  <tr>
    <td>change-column</td><td>Поле которое изменяем при всех случаях в режиме advanced=false</td><td>string</td><td>balance</td>
  </tr>
  <tr>
    <td>user-column</td><td>Поле в котором ищется игрок при всех случаях в режиме advanced=false</td><td>string</td><td>balance</td>
  </tr>
  <tr>
    <td>default</td><td>Стандартное значение поля "change-column" если поиск по "user-column" не дал результатов</td><td>int</td><td>30</td>
  </tr>
  <tr>
    <td>change</td><td>Сколько добавлять к "change-column". Например, пополнение счета на 500 рублей будет происходить если тут указать 500</td><td>string/int</td><td>1</td>
  </tr>
</table>

*Type*
<table>
    <tr>
        <th>Значение</th><th>Функционал</th>
    </tr>
    <tr>
        <td>0</td><td>Попытка добавления "change" к значению "change-column". Если юзер не найден в базе - ничего не делаем.</td>
    </tr>
    <tr>
        <td>1</td><td>Попытка добавления "change" к значению "change-column". Если юзер не найден в базе - добавляем новую запись с значением "default" + "change".</td>
    </tr>
    <tr>
        <td>2</td><td>Выставления значения поля "change-column" на "change". Если юзер не найден в базе - ничего не делаем.</td>
    </tr>
    <tr>
        <td>3</td><td>Выставления значения поля "change-column" на "change". Если юзер не найден в базе - добавляем новую запись.</td>
    </tr>
    <tr>
        <td>4</td><td>Вставка новой записи</td>
    </tr>
</table>

Рассмотрим несколько вариантов ситуаций.

 *[1] Добавление денег в табличку iConomy.*
<pre><code>array(
    'active' => true,
    'table-name' => 'iConomy',
    'type' => 0,
    'change-column' => 'balance',
    'user-column' => 'username',
    'change' => 1
)
</code></pre>

 *[2] Добавление денег в табличку iConomy. Если игрок не найден - создаем новую запись.*
<pre><code>array(
    'active' => true,
    'table-name' => 'iConomy',
    'type' => 1,
    'change-column' => 'balance',
    'user-column' => 'username',
    'default' => 30,
    'change' => 1
)
</code></pre>

 *[3] Выставление значения поля group в значение "voted" в табличке "users-groups"*
<pre><code>array(
    'active' => true,
    'table-name' => 'users-groups',
    'type' => 2,
    'change-column' => 'group',
    'user-column' => 'username',
    'change' => 'voted'
)
</code></pre>

 *[4] Выставление значения поля group в значение "voted" в табличке "users-groups" или добавление если юзер не найден.*
<pre><code>array(
    'active' => true,
    'table-name' => 'users-groups',
    'type' => 3,
    'change-column' => 'group',
    'user-column' => 'username',
    'change' => 'voted'
)
</code></pre>

 *[5] Добавление в таблицу "votes" запись о голосовании. Обычный режим. В поле "portal" вписываем заначение "mctop"*
<pre><code>array(
    'active' => true,
    'table-name' => 'votes',
    'type' => 4,
    'change-column' => 'portal',
    'user-column' => 'username',
    'change' => 'mctop'
)
</code></pre>

 *[6] Добавление в таблицу "votes" запись о голосовании. Расширенный режим. Добавление записи для выдачи предметов (плагин отдельно ищи).*
<pre><code>array(
    'active' => true,
    'table-name' => 'items',
    'type' => 4,
    'advanced' => true.
    'fields' => array(
        'username' => '{player}',
        'item' => '1',
        'item-count' => 64
    ),
)
</code></pre>

Комплексный пример настройки
----------------------

При голосовании выполняем действия 1, 3 и 6 из выше указанного списка.
<pre><code>$config = array(
    'db' => array(
        'driver' => 'mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'minecraft',
    ),
    'security-code' => '',
    'jobs' => array(
        array(
            'active' => true,
            'table-name' => 'iConomy',
            'type' => 0,
            'change-column' => 'balance',
            'user-column' => 'username',
            'change' => 1
        ),
        array(
            'active' => true,
            'table-name' => 'users-groups',
            'type' => 3,
            'change-column' => 'group',
            'user-column' => 'username',
            'change' => 'voted'
        ),
        array(
            'active' => true,
            'table-name' => 'items',
            'type' => 4,
            'advanced' => true.
            'fields' => array(
                'username' => '{player}',
                'item' => '1',
                'item-count' => 64
            ),
        )

    )
</code></pre>

![MC Talk](http://mctalk.org/public/style_images/mctalk/other/logo-small.png)

Поддержка скрипта осуществляется на портале MC Talk - http://mctalk.org/topic/11-mctop-php-bonusi-prodvinutoe-reshenie/ 