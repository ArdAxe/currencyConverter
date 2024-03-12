# CurrencyConverter

## Функционал конвертации сумм заказов в единую валюту

**Входные данные:**

- Код валюты, в которую нужно конвертировать суммы всех заказов

- Список заказов с суммами в разных валютах

**Выходные данные (по выбору):**

- список заказов в виде массива, где ключом является номер заказа, а значением является конвертированная в выбранную валюту сумма заказа

- список DTO заказов, содержащих данные о конвертации суммы


**[Сначала сделал примитивную отдельную функцию]**

`convertCurrencyOfOrders(string $convertTo, array $orderList, string $responseFormat)`

- convertTo (валюта, в которую нужно конвертировать)

- orderList (список заказов, в которых нужно конвертировать суммы)

- responseFormat (формат ожидаемого ответа)

И функцию-тестировщик:

`Test_convertCurrencyInOrders()`



**[Затем развернул функционал в набор классов]**

- OrderCurrencyConvert

- TestConvertCurrencyInOrders

Unit-тест на простую арифметику делать не вижу смысла. Кроме того, проверить вычисления абсолютно нереально, так как курсы валют меняются быстрее, чем происходит взаимодействие с сервисом. Из-за этого такой тест почти никогда не будет пройден.

Сделал тесты для различных ситуаций:
- нулевая сумма заказа
- конвертация валюты в себя же
- корректнрость типов выходных данных

В качестве внешнего сервиса для получения курсов валют взял Fixer. Он имеет тот же интерфейс взаимодействия, что и OER, только не требует платы для смены базовой валюты.
