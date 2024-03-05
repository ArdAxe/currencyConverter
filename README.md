# currencyConverter

Функционал конвертации сумм заказов в единую валюту:

Входные данные:
- Код валюты, в которую нужно конвертировать суммы всех заказов
- Список заказов с суммами в разных валютах

Выходные данные:
- список заказов в виде массива, где ключом является номер заказа, а значением - сумма, конвертированная в выбранную валюту.
ИЛИ по выбору
- список DTO заказов, содержащих данные о конвертации суммы




Срок выполнения - 1 день (12 часов)

Сначала сделал примитивное преобразование одной функцией.
convertCurrencyOfOrders(string $convertTo, array $orderList, string $responseFormat) :array
- convertTo (валюта, в которую нужно конвертировать)
- orderList (список заказов, в которых нужно конвертировать суммы)
- responseFormat (формат ожидаемого ответа)

